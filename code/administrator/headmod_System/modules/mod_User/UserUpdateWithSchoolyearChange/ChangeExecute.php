<?php

namespace administrator\System\User\UserUpdateWithSchoolyearChange;

require_once 'UserUpdateWithSchoolyearChange.php';

/**
 * Executes the changes made beforehand.
 */
class ChangeExecute extends \administrator\System\User\UserUpdateWithSchoolyearChange {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if($this->conflictsSolvedCheck()) {
			$this->_existingGrades = $this->gradesFetch();
			$this->_pdo->beginTransaction();
			$this->userChangesCommit();
			$this->usersNewCommit();
			$this->schoolyearNewSwitchTo();
			$this->_pdo->commit();
			$this->_interface->backlink('administrator|System|User');
			$this->_interface->dieSuccess(_g(
				'The userdata were changed successfully.'
			));
		}
		else {
			$this->_interface->backlink(
				'administrator|System|User|UserUpdateWithSchoolyearChange' .
				'|SessionMenu'
			);
			$this->_interface->dieError(_g('Please resolve the conflicts ' .
				'before comitting the changes'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Checks if conflicts exist that are not solved yet
	 * @return bool  true if all conflicts are solved
	 */
	private function conflictsSolvedCheck() {

		try {
			$res = $this->_pdo->query(
				'SELECT COUNT(*) FROM UserUpdateTempConflicts WHERE solved = 0
			');
			return $res->fetchColumn() == 0;

		} catch (\PDOException $e) {
			$this->_logger->log('Error checking for not solved conflicts',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not upload the data!'));
		}
	}

	/**
	 * Commits the changes to the users to the real users-table
	 * Dies displaying a message on error
	 */
	private function userChangesCommit() {

		$this->usersToChangeCheckGrades();

		try {
			$queryJoints = 'INSERT INTO usersInGradesAndSchoolyears (
					userId, gradeId, schoolyearId
				) SELECT su.origUserId, g.ID,
					(SELECT value FROM global_settings
						WHERE name =
							"userUpdateWithSchoolyearChangeNewSchoolyearId"
					) AS schoolyear
				FROM UserUpdateTempSolvedUsers su
					LEFT JOIN Grades g ON g.gradelevel = su.gradelevel AND
						g.label = su.gradelabel
					WHERE su.origUserId <> 0
			';
			//Update user-entries if data is given
			$queryUsers = 'UPDATE users u
				LEFT JOIN UserUpdateTempSolvedUsers su ON u.ID = su.origUserId
				SET u.email = IFNULL(su.newEmail, u.email),
					u.telephone = IFNULL(su.newTelephone, u.telephone),
					u.username = IFNULL(su.newUsername, u.username)
			';

			$this->_pdo->query($queryJoints);
			$this->_pdo->query($queryUsers);

		} catch (\PDOException $e) {
			$this->_logger->log('Could not commit the user Changes',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g(
				'Could not upload the userchanges!')
			);
		}
	}

	/**
	 * Checks if new grades should be added and adds them
	 * Adds the missing grades so that when the users get changed, the new
	 * grades exist and can be assigned to their users
	 */
	private function usersToChangeCheckGrades() {

		$grades = $this->usersToChangeGradeIdsCommitFetch();

		foreach($grades as $grade) {
			$existingGradeId = array_search(
				$grade['gradelevel'] . $grade['gradelabel'],
				$this->_existingGrades
			);
			if($existingGradeId === FALSE) {
				$this->gradeAdd($grade['gradelevel'], $grade['gradelabel']);
				$this->_existingGrades[$grade['gradeId']] =
					$grade['gradelevel'] . $grade['gradelabel'];
			}
		}
	}

	/**
	 * Fetches the grades already existing in the Database
	 * @return array '<gradeId>' => '<gradelevel.gradelabel>'
	 */
	private function gradesFetch() {

		try {
			$stmt = $this->_pdo->query(
				'SELECT ID, CONCAT(gradelevel, label) AS name
				FROM Grades WHERE 1'
			);
			return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

		} catch (\PDOException $e) {
			$this->_logger->log('Could not fetch the grades',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not fetch the grades!'));
		}
	}

	/**
	 * Adds the users that are new to the database (were in csv but not in db)
	 * Dies displaying a message on error.
	 */
	private function usersNewCommit() {

		try {
			$users = $this->usersNewToCommitFetch();
			if(empty($users) || !count($users)) {
				return;
			}

			$stmtu = $this->_pdo->prepare(
				'INSERT INTO users
					(forename, name, username, password, email, telephone,
						last_login, locked, GID, credit, soli, birthday)
				VALUES (?, ?, IFNULL(?, CONCAT(forename, ".", name)), "", IFNULL(?, ""), IFNULL(?, ""), "", 0, 0, 0, 0, ?)'
			);
			$stmtg = $this->_pdo->prepare(
				'INSERT INTO usersInGradesAndSchoolyears (
					userId, gradeId, schoolyearId
				) VALUES (? ,?, (SELECT value FROM global_settings
					WHERE name =
						"userUpdateWithSchoolyearChangeNewSchoolyearId"
				))'
			);

			foreach($users as $user) {
				if(empty($user['birthday'])) {
					$user['birthday'] = '';
				}
				$user = $this->userNewGradeCheckAndAdd($user);
				$stmtu->execute(array(
					$user['forename'], $user['name'], $user['newUsername'],
					$user['newEmail'], $user['newTelephone'],
					$user['birthday']
				));
				$userId = $this->_pdo->lastInsertId();
				$stmtg->execute(array($userId, $user['gradeId']));
			}

		} catch (\PDOException $e) {
			$this->_logger->log('Could not commit the new users',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not commit the new users!' . $e->getMessage()));
		}
	}

	/**
	 * Checks if any grades should be added and adds them
	 */
	private function userNewGradeCheckAndAdd($user) {

		if(empty($user['gradeId'])) {
			//Check if the new Grade has already been added by another
			//userentry
			$existingGradeId = array_search(
				$user['gradelevel'] . $user['gradelabel'],
				$this->_existingGrades
			);
			if($existingGradeId === FALSE) {
				$gradeId = $this->gradeAdd(
					$user['gradelevel'], $user['gradelabel']
				);
				$user['gradeId'] = $gradeId;
				$this->_existingGrades[$gradeId] =
					$user['gradelevel'] . $user['gradelabel'];
			}
			else {
				$user['gradeId'] = $existingGradeId;
			}
		}
		return $user;
	}

	/**
	 * Adds a grade and returns its new Id
	 * Dies displaying a message on error
	 * @param  int    $level The gradelevel
	 * @param  string $label The gradelabel
	 * @return int           The Id of the new grade
	 */
	private function gradeAdd($level, $label) {

		try {
			if(empty($this->_gradeStmt)) {
				$this->_gradeStmt = $this->_pdo->prepare(
					'INSERT INTO Grades (label, gradelevel, schooltypeId) VALUES (?,?, 0)'
				);
			}
			$this->_gradeStmt->execute(array($label, $level));
			return $this->_pdo->lastInsertId();

		} catch (\PDOException $e) {
			$this->_logger->log('Error adding the grade', 'Notice', Null,
				json_encode(array(
					'msg' => $e->getMessage(),
					'level' => $level,
					'label' => $label))
			);
			$this->_interface->dieError(_g('Could not add the grade!') . $e->getMessage());
		}
	}

	/**
	 * Fetches the users that will be added when comitting
	 * @return array  the users to add
	 */
	private function usersNewToCommitFetch() {

		try {
			$res = $this->_pdo->query('SELECT su.*, g.ID AS gradeId
				FROM UserUpdateTempSolvedUsers su
				LEFT JOIN Grades g ON su.gradelevel = g.gradelevel AND
					su.gradelabel = g.label
				WHERE su.origUserId = 0');
			$users = $res->fetchAll(\PDO::FETCH_ASSOC);

			return $users;

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching the new users to commit',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not fetch the new users!'));
		}
	}

	/**
	 * Fetches the users that will be changed when comitting
	 * @return array  '<index>' => [userId, gradeStuff]
	 */
	private function usersToChangeGradeIdsCommitFetch() {

		try {
			$res = $this->_pdo->query('SELECT su.ID, g.ID AS gradeId,
					su.gradelevel AS gradelevel, su.gradelabel AS gradelabel
				FROM UserUpdateTempSolvedUsers su
				LEFT JOIN Grades g ON su.gradelevel = g.gradelevel AND
					su.gradelabel = g.label
				WHERE su.origUserId <> 0');
			$users = $res->fetchAll(\PDO::FETCH_ASSOC);

			return $users;

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching the new users to commit',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not fetch the new users!'));
		}
	}

	/**
	 * Switches to the new schoolyear
	 * Dies displaying a message on error
	 */
	private function schoolyearNewSwitchTo() {

		try {
			$this->_pdo->query(
				'UPDATE schoolYear SET active = 0 WHERE active = 1;
				UPDATE schoolYear SET active = 1 WHERE ID = (
					SELECT value FROM global_settings
						WHERE name =
							"userUpdateWithSchoolyearChangeNewSchoolyearId")'
			);

		} catch (\PDOException $e) {
			$this->_logger->log('Could not switch to the new Schoolyear!',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not switch to the new Schoolyear!'));
		}
	}



	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Use a prepared statement, so if multiple grades are added, its faster
	 * @var PDOStatement
	 */
	private $_gradeStmt;

	private $_existingGrades;
}

?>