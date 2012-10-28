<?php

/**
 * This class acts as the central point for accessing data of the database. Additionally, it does most of the
 * Exceptionhandling.
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class KuwasysDatabaseAccess {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($interface) {

		require_once 'KuwasysLanguageManager.php';

		$this->_interface = $interface;
		$this->_languageManager = new KuwasysLanguageManager($this->_interface);
		$this->_languageManager->setModule('databaseAccess');
		$this->initManagers();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function initManagers () {

		require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysClassTeacherManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysGradeManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysGlobalSettingsManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysJointClassInSchoolYearManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysJointClassTeacherInClass.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysJointGradeInSchoolYear.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInGrade.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInSchoolYear.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysSchoolYearManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';

		$this->_userManager = new KuwasysUsersManager();
		$this->_classManager = new KuwasysClassManager();
		$this->_gradeManager = new KuwasysGradeManager();
		$this->_schoolyearManager = new KuwasysSchoolYearManager();
		$this->_classteacherManager = new KuwasysClassTeacherManager();
		$this->_globalSettingsManager = new KuwasysGlobalSettingsManager();
		$this->_jointUserInClassManager = new KuwasysJointUsersInClass();
		$this->_jointUserInGradeManager = new KuwasysJointUsersInGrade();
		$this->_jointClassInSchoolyearManager = new KuwasysJointClassInSchoolYearManager();
		$this->_jointGradeInSchoolyearManager = new KuwasysJointGradeInSchoolYear();
		$this->_jointUserInSchoolyearManager = new KuwasysJointUsersInSchoolYear();
		$this->_jointClassteacherInClassManager = new KuwasysJointClassTeacherInClass();
	}

	public function classAdd ($label, $description, $maxRegistration, $allowRegistration, $weekday) {

		try {
			$this->_classManager->addClass($label, $description, $maxRegistration, $allowRegistration, $weekday);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classErrorAdd') . $e->getMessage());
		}
	}

	public function classDelete ($classId) {

		try {
			$this->_classManager->deleteClass($classId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classErrorDelete'));
		}
	}

	public function classLabelGet ($classId) {

		try {
			$label = $this->_classManager->getLabelOfClass($classId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classLabelErrorFetch'));
		}
		return $label;
	}

	public function classGetById ($classId) {

		try {
			$class = $this->_classManager->getClass($classId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('classErrorNoClasses'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classErrorFetch'));
		}
		return $class;
	}

	public function classGetByClassIdArray ($classIdArray) {

		try {
			$classes = $this->_classManager->getClassesByClassIdArray($classIdArray);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('classErrorNoClasses'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classErrorFetch'));
		}
		return $classes;
	}

	public function classIdGetLastAdded () {

		try {
			$lastID = $this->_classManager->getLastClassID();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('classErrorNoClasses'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classIdErrorFetchLastAdded'));
		}
		return $lastID;
	}

	public function classNextAutoincrementIdGet () {

		try {
			$idOfClass = $this->_classManager->getNextAutoIncrementID();
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classIdErrorFetchNextAutoincrement'));
		}
		return $idOfClass;
	}

	public function classGetAll () {

		try {
			$classes = $this->_classManager->getAllClasses();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('classErrorNoClasses'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classErrorFetch'));
		}
		return $classes;
	}

	public function classGet ($classId) {

		try {
			$class = $this->_classManager->getClass($classId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classErrorFetch'));
		}
		return $class;
	}

	public function classChange ($id, $label, $description, $maxRegistration, $allowRegistration, $weekday) {

		try {
			$this->_classManager->alterClass($id, $label, $description, $maxRegistration, $allowRegistration, $weekday);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classErrorChange'));
		}
	}

	public function classRegistrationGloballyEnabledGetAndAddingWhenVoid () {

		try {
			$toggle = $this->_globalSettingsManager->isClassRegistrationGloballyEnabledGet();
		} catch (MySQLVoidDataException $e) {
			$this->classRegistrationGloballyIsEnabledAdd(false);
			return false;
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('globalSettingsErrorFetchClassRegEnabled'));
		}
		return $toggle;
	}

	public function classRegistrationGloballyIsEnabledSet ($toggle) {

		try {
			$this->_globalSettingsManager->isClassRegistrationGloballyEnabledAlter($toggle);
		} catch (MySQLVoidDataException $e) {
			$this->_globalSettingsManager->isClassRegistrationGloballyEnabledAdd($toggle);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('globalSettingsErrorSetClassRegEnabled'));
		}
	}

	public function classRegistrationGloballyIsEnabledAdd ($toggle) {

		try {
			$this->_globalSettingsManager->isClassRegistrationGloballyEnabledAdd($toggle);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('globalSettingsErrorSetClassRegEnabled'));
		}
	}

	public function classteacherGetAll () {

		try {
			$classTeachers = $this->_classteacherManager->getAllClassTeachers();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('classteacherErrorNoClassteachers'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classteacherErrorFetch'));
		}
		return $classTeachers;
	}

	public function classteacherGetById ($classteacherId) {

		try {
			$classteacher = $this->_classteacherManager->getClassTeacher($classteacherId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('classteacherErrorNoClassteachers'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classteacherErrorFetch'));
		}
		return $classteacher;
	}

	public function classteacherAdd ($name, $forename, $address, $telephone) {

		try {
			$this->_classteacherManager->addClassTeacher($name, $forename, $address, $telephone);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classteacherErrorAdd'));
		}
	}

	public function classteacherDelete ($classteacherId) {

		try {
			$this->_classteacherManager->deleteClassTeacher($classteacherId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classteacherErrorDelete'));
		}
	}

	public function classteacherChange ($id, $name, $forename, $address, $telephone) {

		try {
			$this->_classteacherManager->alterClassTeacher($id, $name, $forename, $address, $telephone);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classteacherErrorChange'));
		}
	}

	public function classteacherLastAddedGetId () {

		try {
			$lastId = $this->_classteacherManager->getLastAddedId();
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classteacherIdErrorFetchLastAdded'));
		}
		return $lastId;
	}

	public function classteacherGetByIdArrayWithoutDyingWhenVoid ($classteacherIdArray) {

		try {
			$classteachers = $this->_classteacherManager->getClassteachersByClassteacherIdArray($classteacherIdArray);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('classteacherErrorNoClassteachers'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('classteacherErrorFetch'));
		}
		if(is_array($classteachers)) {
			return $classteachers;
		}
	}

	public function gradeAdd($label, $year) {

		try {
			$this->_gradeManager->addGrade($label, $year);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('gradeErrorAdd'));
		}
	}

	public function gradeDeleteFromDatabase ($id) {

		try {
			$this->_gradeManager->delEntry($id);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('gradeErrorDelete'));
		}
	}

	public function gradeChange ($id, $label, $year) {

		try {
			$this->_gradeManager->alterGrade($id, $label, $year);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('gradeErrorNoGrades'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('gradeErrorChange'));
		}
	}

	public function gradeGetById ($gradeId) {

		try {
			$grade = $this->_gradeManager->getGrade($gradeId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('gradeErrorFetch'));
		}
		return $grade;
	}

	public function gradeGetWithoutDyingWhenError ($gradeId) {

		try {
			$grade = $this->_gradeManager->getGrade($gradeId);
		} catch (Exception $e) {
			$this->_interface->showMsg($this->_languageManager->getText('gradeErrorFetch'));
		}
		if (is_array($grade)) {
			return $grade;
		}
	}

	public function gradeGetAll () {

		try {
			$grades = $this->_gradeManager->getAllGrades();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('gradeErrorNoGrades'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('gradeErrorFetch'));
		}
		return $grades;
	}

	public function gradeIdAddToFetchArray ($gradeId) {

		$this->_gradeManager->addGradeIdItemToFetch($gradeId);
	}

	public function gradeGetAllByFetchArray () {

		try {
			$grades = $this->_gradeManager->getAllGradesOfGradeIdItemArray();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError('gradeErrorNoGrades');
		} catch (Exception $e) {
			$this->_interface->dieError('gradeErrorFetch');
		}
		return $grades;
	}

	public function schoolyearAdd ($label, $active) {

		try {
			$this->_schoolyearManager->addSchoolYear($label, $active);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorAdd') . $e->getMessage());
		}
	}

	public function schoolyearDelete($schoolyearId) {

		try {
			$this->_schoolyearManager->deleteSchoolYear($schoolyearId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorDelete'));
		}
	}

	public function schoolyearChange ($id, $label, $active) {

		try {
			$this->_schoolyearManager->alterSchoolYear($id, $label, $active);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorNoSpecificSchoolyear'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorChange'));
		}
	}

	public function schoolyearGetAll () {

		try {
			$schoolYears = $this->_schoolyearManager->getAllSchoolYears();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorNoSchoolyears'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorFetch'));
		}
		return $schoolYears;
	}

	public function schoolyearGet ($schoolyearId) {

		try {
			$schoolyear = $this->_schoolyearManager->getSchoolYear($schoolyearId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorNoSpecificSchoolyear'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorFetch'));
		}
		return $schoolyear;
	}

	public function schoolyearLabelGet ($schoolyearId) {

		try {
			$schoolyearLabel = $this->_schoolyearManager->getSchoolyearLabel ($schoolyearId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorNoSpecificSchoolyear'));
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorFetchSchoolyearLabel'));
		}
		return $schoolyearLabel;
	}

	public function schoolyearActiveGetId () {

		try {
			$schoolyear = $this->_schoolyearManager->getActiveSchoolYear();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorNoActive'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorFetchActive'));
		}
		return $schoolyear['ID'];
	}

	public function schoolyearIdGetBySchoolyearNameWithoutDying ($schoolyearName) {

		try {
			$id = $this->_schoolyearManager->getSchoolyearIdOfSchoolyearName($schoolyearName);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showError(sprintf($this->_languageManager->getText('schoolyearErrorNoSpecificSchoolyearName'),
					$schoolyearName));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorFetch'));
		}
		if(isset($id)) {
			return $id;
		}
	}

	public function schoolyearCheckExisting ($schoolyearId) {

		try {
			$this->_schoolyearManager->getSchoolYear($schoolyearId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorNoSpecificSchoolyear'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('schoolyearErrorFetch'));
		}
	}

	public function userAdd ($forename, $name, $username, $password, $email, $telephone, $birthday) {

		try {
			$this->_userManager->addUser($forename, $name, $username, $password, $email, $telephone, $birthday);
		} catch (Exception $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('userErrorAdd'), $e->getMessage()));
		}
	}

	public function userDelete ($userId) {

		try {
			$this->_userManager->deleteUser($userId);
		} catch (Exception $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('userErrorDelete'), $e->getMessage()));
		}
	}

	public function userGetAll () {

		try {
			$users = $this->_userManager->getAllUsers();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('userErrorNoUsers'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('userErrorFetch'));
		}
		return $users;
	}

	public function userIdAddToUserIdArray ($userId) {

		$this->_userManager->addUserIdToUserIdArray($userId);
	}

	public function userGetByUserIdArray () {

		try {
			$users = $this->_userManager->getUsersByUserIdArray ();
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('userErrorFetch'));
		}
		return $users;
	}

	public function userGet ($userId) {

		try {
			$userData = $this->_userManager->getUserByID($userId);
		} catch (MySQLConnectionException $e) {
			$this->_interface->dieError($this->_languageManager->getText('userErrorNoSpecific'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('userErrorFetch'));
		}
		return $userData;
	}

	public function jointUserInClassGetAllByClassId ($classId) {

		try {
			$joints = $this->_jointUserInClassManager->getAllJointsWithClassId($classId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointUserInClassErrorNoJointsOfClass'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorFetch'));
		}
		if(isset($joints) && is_array($joints)) {
			return $joints;
		}
	}

	public function jointUserInClassGetAllByClassIdAndStatusActiveWithoutDyingWhenVoid ($classId) {

		try {
			$joints = $this->_jointUserInClassManager->getAllJointsOfClassIdAndStatusActive($classId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointUserInClassErrorNoJointsOfClass'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorFetch'));
		}
		if(isset($joints)) {
			return $joints;
		}
	}

	public function jointUserInClassGetAllByUserIdWithoutDyingWhenVoid ($userId) {

		try {
			$joints = $this->_jointUserInClassManager->getAllJointsOfUserId($userId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg(sprintf($this->_languageManager->getText('jointUserInClassNoJointsOfUser'), $userId));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorFetch'));
		}
		if (is_array($joints)) {
			return $joints;
		}
	}

	public function jointUserInClassGetByUserIdAndClassId ($userId, $classId) {

		try {
			$joint = $this->_jointUserInClassManager->getJointOfUserIdAndClassId($userId, $classId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorNoSpecific'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorFetch'));
		}
		return $joint;
	}

	public function jointUserInClassIsExistingByUserIdAndClassId ($userId, $classId) {

		try {
			$isExisting = $this->_jointUserInClassManager->isJointExistingByUserIdAndClassId($userId, $classId);
		} catch (Exception $e) {
			$this->_interface->dieError('jointUserInClassErrorCheckForExisting');
		}
		return $isExisting;
	}

	public function jointUserInClassGetCountOfActiveUsersOfClassId ($classId) {

		try {
			$counter = $this->_jointUserInClassManager->getCountOfActiveUsersInClass($classId);
		} catch (MySQLVoidDataException $e) {
			$counter = 0;
		} catch (Exception $e) {
			$this->_interface->showError($this->_languageManager->getText('jointUserInClassErrorFetch'));
		}
		if(is_numeric($counter)) {
			return $counter;
		}
	}

	public function jointUserInClassGetAllWithStatusWaiting() {

		try {
			$joints = $this->_jointUserInClassManager->getAllJointsWithStatusWaiting();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorNoWaiting'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorFetchWaiting'));
		}
		return $joints;
	}

	public function jointUserInClassGetAllWithStatusWaitingWithoutDyingWhenVoid () {

		try {
			$joints = $this->_jointUserInClassManager->getAllJointsWithStatusWaiting();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointUserInClassErrorNoWaiting'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorFetchWaiting'));
		}
		if(is_array($joints)) {
			return $joints;
		}
	}

	public function jointUserInClassGetAllWithStatusActiveWithoutDyingWhenVoid () {

		try {
			$jointsUsersInClassActive = $this->_jointUserInClassManager->getAllJointsWithStatusActive();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointUserInClassErrorNoActive'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorFetchActive'));
		}
		return $jointsUsersInClassActive;
	}

	public function jointUserInClassAdd ($userId, $classId, $status) {

		try {
			$this->_jointUserInClassManager->addJoint($userId, $classId, $status);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrirAdd'));
		}
	}

	public function jointUserInClassDelete ($jointId) {

		try {
			$this->_jointUserInClassManager->deleteJoint($jointId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorDelete'));
		}
	}

	public function jointUserInClassDeleteAllOfClassId ($classId) {

		try {
			$this->_jointUserInClassManager->deleteAllJointsOfClassId($classId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorDelete'));
		}
	}

	public function jointUserInClassAlterStatus ($jointId, $status) {

		try {
			$this->_jointUserInClassManager->alterStatusOfJoint($jointId, $status);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorChange'));
		}
	}
	public function jointUserInClassAlter ($jointId, $classId, $userId, $status) {

		try {
			$this->_jointUserInClassManager->alterJoint($jointId, $classId, $userId, $status);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorChange'));
		}
	}

	public function jointUserInClassIsClassJointedWithUser ($classId) {

		try {
			$this->_jointUserInClassManager->getAllJointsWithClassId($classId);
		} catch (MySQLVoidDataException $e) {
			return false;
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorCheckForExisting'));
		}
		return true;
	}

	public function jointUserInClassSetToActiveAddToMultipleChangesList ($jointId) {

		try {
			$this->_jointUserInClassManager->alterStatusOfJointAddEntryToTempList($jointId,
					$this->_jointUserInClassManager->getActiveStatusString());
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorChange'));
		}
	}

	public function jointUserInClassSetToWaitingAddToMultipleChangesList ($jointId) {

		try {
			$this->_jointUserInClassManager->alterStatusOfJointAddEntryToTempList($jointId,
					$this->_jointUserInClassManager->getWaitingStatusString());
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorChange'));
		}
	}

	public function jointUserInClassFirstRequestGetAndThrowWhenVoid () {

		try {
			$joints = $this->_jointUserInClassManager->getAllJointsWithStatusRequestFirst();
		} catch (MySQLVoidDataException $e) {
			throw new MySQLVoidDataException('No UsersInClass-Joints with firstRequests');
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorFetchFirstRequest'));
		}
		return $joints;
	}

	public function jointUserInClassSecondRequestGetAndThrowWhenVoid () {

		try {
			$joints = $this->_jointUserInClassManager->getAllJointsWithStatusRequestSecond();
		} catch (MySQLVoidDataException $e) {
			throw new MySQLVoidDataException('No UsersInClass-Joints with secondRequests');
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorFetchSecondRequest'));
		}
		return $joints;
	}

	public function jointUserInClassUploadMultipleChangesList () {

		try {
			$this->_jointUserInClassManager->upAlterStatusOfJointTempListToDatabase();
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorChange'));
		}
	}

	public function jointUserInClassGetAllWithoutDyingWhenVoid () {

		try {
			$joints = $this->_jointUserInClassManager->getAllJointsWithStatusWaiting();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointUserInClassErrorNoWaiting'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInClassErrorFetchWaiting'));
		}
		if(is_array($joints)) {
			return $joints;
		}
	}

	public function jointUserInGradeAdd ($userId, $gradeId) {

		try {
			$this->_jointUserInGradeManager->addJoint($userId, $gradeId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInGradeErrorAdd'));
		}
	}

	public function jointUserInGradeDelete($jointId) {

		try {
			$this->_jointUserInGradeManager->deleteJoint($jointId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInGradeErrorDelete'));
		}
	}

	public function jointUserInGradeDeleteAllWithUserIdWithoutDyingWhenError($userId) {

		try {
			$this->_jointUserInGradeManager->deleteJointsByUserId($userId);
		} catch (Exception $e) {
			$this->_interface->ShowMsg($this->_languageManager->getText('jointUserInGradeErrorDelete'));
		}
	}

	public function jointUserInGradeGetAllWithoutDyingWhenVoid () {

		try {
			$joints = $this->_jointUserInGradeManager->getAllJoints();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointUserInGradeErrorNoJoints'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInGradeErrorFetch'));
		}
		if(is_array($joints)) {
			return $joints;
		}
	}

	public function jointUserInGradeGetByUserIdWithoutDying ($userId) {

		try {
			$jointUsersInGrade = $this->_jointUserInGradeManager->getJointByUserId($userId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointUserInGradeErrorNoJoints'));
		} catch (Exception $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointUserInGradeErrorFetch'));
		}
		if (isset($jointUsersInGrade)) {
			return $jointUsersInGrade;
		}
	}

	public function jointUserInGradeGetAllByGradeId ($gradeId) {

		try {
			$joints = $this->_jointUserInGradeManager->getAllJointsOfGradeId($gradeId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError('jointUserInGradeErrorNoJoints');
		} catch (Exception $e) {
			$this->_interface->dieError('jointUserInGradeErrorFetch');
		}
		return $joints;
	}

	public function jointClassInSchoolyearGetAll () {

		try {
			$joints = $this->_jointClassInSchoolyearManager->getAllJoints();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointClassInSchoolyearErrorNoJoints'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointClassInSchoolyearErrorFetch'));
		}
		return $joints;
	}

	public function jointClassInSchoolyearChangeByClassId ($classId, $schoolyearId) {

		try {
			$this->_jointClassInSchoolyearManager->alterSchoolYearIdOfClassId($classId, $schoolyearId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointClassInSchoolyearErrorChange'));
		}
	}

	public function jointClassInSchoolyearAdd ($schoolyearId, $classId) {

		try {
			$this->_jointClassInSchoolyearManager->addJoint($schoolyearId, $classId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointClassInSchoolyearErrorAdd'));
		}
	}

	public function jointClassInSchoolyearDelete ($id) {

		try {
			$this->_jointClassInSchoolyearManager->deleteAllJointsOfClass($id);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointClassInSchoolyearErrorDelete'));
		}
	}

	public function jointClassInSchoolyearGetSchoolyearIdByClassIdWithoutDyingWhenVoid ($classId) {

		try {
			$schoolyearId = $this->_jointClassInSchoolyearManager->getSchoolYearIdOfClassId($classId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showError($this->_languageManager->getText('jointClassInSchoolyearErrorNoJoints'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointClassInSchoolyearErrorFetchSpecific'));
		}
		if (isset($schoolyearId)) {
			return $schoolyearId;
		}
	}

	public function jointClassteacherInClassAdd ($classTeacherID, $classID) {

		try {
			$this->_jointClassteacherInClassManager->addJoint($classTeacherID, $classID);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointClassteacherInClassErrorAdd'));
		}
	}

	public function jointClassteacherInClassDelete ($id) {

		try {
			$this->_jointClassteacherInClassManager->deleteJoint($id);
		} catch (Exception $e) {
			$this->_interface->showError($this->_languageManager->getText('jointClassteacherInClassErrorDelete'));
		}
	}

	public function jointClassteacherInClassGetAllWithoutDyingWhenVoid () {

		try {
			$joints = $this->_jointClassteacherInClassManager->getAllJoints();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointClassteacherInClassErrorNoJoints'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointClassteacherInClassErrorFetch'));
		}
		if (isset($joints)) {
			return $joints;
		}
	}

	public function jointClassteacherInClassGetByClassIdArrayWithoutDyingWhenVoid ($classIdArray) {

		try {
			$joints = $this->_jointClassteacherInClassManager->getJointsByClassIdArray($classIdArray);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointClassteacherInClassErrorNoJoints'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointClassteacherInClassErrorFetch'));
		}
		if(is_array($joints)) {
			return $joints;
		}
	}

	public function jointGradeInSchoolyearAdd ($gradeId, $schoolyearId) {

		try {
			$this->_jointGradeInSchoolyearManager->addJoint($gradeId, $schoolyearId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointGradeInSchoolyearErrorAdd'));
		}
	}

	public function jointGradeInSchoolyearDelete ($jointId) {

		try {
			$this->_jointGradeInSchoolyearManager->deleteJoint($id);
		} catch (Exception $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointGradeInSchoolyearErrorDelete'));
		}
	}

	public function jointGradeInSchoolyearDeleteByGradeId ($gradeId) {

		try {
			$this->_jointGradeInSchoolyearManager->deleteJointByGradeId($gradeId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointGradeInSchoolyearErrorDelete'));
		}
	}

	public function jointGradeInSchoolyearGetAll () {

		try {
			$joints = $this->_jointGradeInSchoolyearManager->getAllJoints();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointGradeInSchoolyearErrorNoJoints'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointGradeInSchoolyearErrorFetch'));
		}
		return $joints;
	}

	public function jointGradeInSchoolyearGetBySchoolyearId ($schoolyearId) {

		try {
			$joints = $this->_jointGradeInSchoolyearManager->getAllJointsOfSchoolyearId($schoolyearId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError('jointGradeInSchoolyearErrorNoJoints');
		} catch (Exception $e) {
			$this->_interface->dieError('jointGradeInSchoolyearErrorFetch');
		}
		return $joints;
	}

	public function jointGradeInSchoolyearGetSchoolyearIdByGradeIdWithoutDyingWhenVoid($gradeId) {

		try {
			$schoolyearId = $this->_jointGradeInSchoolyearManager->getSchoolyearIdOfGradeId($gradeId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('jointGradeInSchoolyearErrorNoJoints'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointGradeInSchoolyearErrorFetch'));
		}
		return $schoolyearId;
	}

	public function jointGradeInSchoolyearGetByGradeId ($gradeId) {

		try {
			$joint = $this->_jointGradeInSchoolyearManager->getJointByGradeId($gradeId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointGradeInSchoolyearError'));
		}
		return $joint;
	}

	public function jointUserInSchoolyearAdd ($userId, $schoolyearId) {

		try {
			$this->_jointUserInSchoolyearManager->addJoint($userId, $schoolyearId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInSchoolyearErrorAdd'));
		}
	}

	public function jointUserInSchoolyearDeleteByUserId ($userId) {

		try {
			$this->_jointUserInSchoolyearManager->deleteJointByUserId($userId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInSchoolyearErrorChange'));
		}
	}

	public function jointUserInSchoolyearGetAll () {

		try {
			$joints = $this->_jointUserInSchoolyearManager->getAllJoints();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInSchoolyearErrorNoJoints'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('jointUserInSchoolyearErrorFetch'));
		}
		return $joints;
	}

	public function jointUserInSchoolyearGetSchoolyearIdByUserIdWithoutDyingWhenVoid ($userId) {

		try {
			$schoolyearId = $this->_jointUserInSchoolyearManager->getSchoolYearIdByUserId($userId);
		} catch (MySQLVoidDataException $e) {
			return;
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManagerManager->getText('jointUserInSchoolyearErrorFetch'));
		}
		return $schoolyearId;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	private $_interface;
	private $_languageManager;

	/********************
	 * Managers
	********************/
	private $_userManager;
	private $_classManager;
	private $_gradeManager;
	private $_schoolyearManager;
	private $_classteacherManager;
	private $_globalSettingsManager;
	private $_jointUserInClassManager;
	private $_jointUserInGradeManager;
	private $_jointClassInSchoolyearManager;
	private $_jointGradeInSchoolyearManager;
	private $_jointUserInSchoolyearManager;
	private $_jointClassteacherInClassManager;

}
?>