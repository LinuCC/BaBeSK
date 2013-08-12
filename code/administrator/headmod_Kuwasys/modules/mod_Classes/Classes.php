<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/gump.php';

/**
 * Allows the User to use Classes. Classes as in the Workgroups in Schools
 *
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class Classes extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the Module
	 *
	 * @param string $name         The Name of the Module
	 * @param string $display_name The Name that should be displayed to the
	 *                             User
	 * @param string $path         A relative Path to the Module
	 */
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Executes the Module, does things based on ExecutionRequest
	 * @param  DataContainer $dataContainer contains data needed by the Module
	 */
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		if($execReq = $dataContainer->getSubmoduleExecutionRequest()) {
			$this->submoduleExecute($execReq);
		}
		else {
			$this->mainMenu();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Initializes data needed by the Object
	 * @param  DataContainer $dataContainer Contains data needed by Classes
	 */
	protected function entryPoint($dataContainer) {

		$this->_interface = $dataContainer->getInterface();
		$this->_acl = $dataContainer->getAcl();
		$this->_pdo = $dataContainer->getPdo();
		$this->_smarty = $dataContainer->getSmarty();

		$this->initSmartyVariables();
	}

	/**
	 * Displays a MainMenu to the User
	 *
	 * Dies displaying the Main Menu
	 */
	protected function mainMenu() {

		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'mainmenu.tpl');
	}

	protected function submoduleAddClassExecute() {

		if(isset($_POST['label'], $_POST['description'])) {
			$_POST['allowRegistration'] =
				(isset($_POST['allowRegistration'])) ? 1 : 0;
			$this->classInputCheck();
			$this->addClassUpload();
			$this->_interface->dieSuccess(
				_g('The Class was successfully added.'));
		}
		else {
			$this->addClassDisplay();
		}
	}

	/**
	 * Fetches all Schoolyears and returns them
	 *
	 * @return array The Schoolyears as an Array
	 */
	protected function schoolyearsGetAll() {

		try {
			$stmt = $this->_pdo->query('SELECT * FROM schoolYear');
			return $stmt->fetchAll();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Schoolyears!'));
		}
	}

	/**
	 * Fetches all Classunits (usually days) and returns them
	 *
	 * @return array The Classunits
	 */
	protected function classunitsGetAll() {

		try {
			$stmt = $this->_pdo->query('SELECT * FROM kuwasysClassUnit');
			return $stmt->fetchAll();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Classunits!'));
		}
	}

	/**
	 * Checks the given Input of the ChangeClass and AddClass Dialog
	 */
	protected function classInputCheck() {

		$gump = new GUMP();
		$gump->rules(array(
			'label' => array(
				'required|min_len,2|max_len,64',
				'',
				_g('Classname')
			),
			'description' => array(
				'max_len,1024',
				'',
				_g('Classdescription')
			),
			'maxRegistration' => array(
				'required|min_len,1|max_len,4|numeric',
				'',
				_g('Max Amount of Registrations for this Class')
			),
			'classunit' => array(
				'required|numeric',
				'',
				_g('Classunit')
			),
			'schoolyear' => array(
				'required|numeric',
				'',
				_g('Schoolyear-ID')
			),
			'allowRegistration' => array(
				'boolean',
				'',
				_g('Allow registration')
			)
		));

		if(!($_POST = $gump->run($_POST))) {
			$this->_interface->dieError(
				$gump->get_readable_string_errors(true));
		}
	}

	/**
	 * Adds all necessary data to the Database
	 */
	protected function addClassUpload() {

		$this->_pdo->beginTransaction();
		$this->newClassUpload();
		$this->_pdo->commit();
	}

	/**
	 * Adds a new Row to the class-Table
	 *
	 * Dies displaying a Message on Error
	 */
	protected function newClassUpload() {

		try {
			$stmt = $this->_pdo->prepare(
				'INSERT INTO class (label, description, maxRegistration,
					registrationEnabled, unitId, schoolyearId)
				VALUES (:label, :description, :maxRegistration,
					:registrationEnabled, :unitId, :schoolyearId)');

			$stmt->execute(array(
				':label' => $_POST['label'],
				':description' => $_POST['description'],
				':maxRegistration' => $_POST['maxRegistration'],
				':registrationEnabled' => $_POST['allowRegistration'],
				':unitId' => $_POST['classunit'],
				':schoolyearId' => $_POST['schoolyear'],
			));

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not add the new Class!'));
		}
	}

	/**
	 * Displays a Form to the User which allows him to add a Class
	 */
	protected function addClassDisplay() {

		$this->_smarty->assign('schoolyears', $this->schoolyearsGetAll());
		$this->_smarty->assign('classunits', $this->classunitsGetAll());
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'addClass.tpl');
	}

	/**
	 * Allows the User to change a Class
	 */
	protected function submoduleChangeClassExecute() {

		if(isset($_POST['label'], $_POST['description'])) {
			$_POST['allowRegistration'] =
				(isset($_POST['allowRegistration'])) ? 1 : 0;
			$this->classInputCheck();
			$this->changeClassUpload();
			$this->_interface->dieSuccess(
				_g('The Class was successfully changed.'));
		}
		else {
			$this->changeClassDisplay();
		}
	}

	/**
	 * Uploads the Change of the Class
	 *
	 * Dies with a Message on Error
	 */
	protected function changeClassUpload() {

		try {
			$stmt = $this->_pdo->prepare(
				'UPDATE class SET label = :label, description = :description,
					maxRegistration = :maxRegistration,
					registrationEnabled = :registrationEnabled,
					unitId = :unitId, schoolyearId = :schoolyearId
					WHERE ID = :id');

			$stmt->execute(array(
				':label' => $_POST['label'],
				':description' => $_POST['description'],
				':maxRegistration' => $_POST['maxRegistration'],
				':registrationEnabled' => $_POST['allowRegistration'],
				':unitId' => $_POST['classunit'],
				':schoolyearId' => $_POST['schoolyear'],
				':id' => $_GET['ID'],
			));

		} catch (Exception $e) {
			$this->_interface->dieError(_g("Could not change the Class with the ID {$_GET[ID]}!"));
		}
	}

	/**
	 * Displays a form allowing the User to Change the Class
	 */
	protected function changeClassDisplay() {

		$this->_smarty->assign('schoolyears', $this->schoolyearsGetAll());
		$this->_smarty->assign('classunits', $this->classunitsGetAll());
		$this->_smarty->assign('class', $this->classGet($_GET['ID']));
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'changeClass.tpl');
	}

	/**
	 * Fetches and returns the Class with the ID $id
	 *
	 * @param  string $id The ID of the Class
	 * @return array      The Class as an Array
	 */
	protected function classGet($id) {

		try {
			$stmt = $this->_pdo->prepare('SELECT * FROM class WHERE ID = :id');
			$stmt->execute(array(':id' => $id));
			return $stmt->fetch();

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not fetch the Class'));
		}
	}

	/**
	 * Deletes a Class
	 */
	protected function submoduleDeleteClassExecute() {

		if(isset($_POST['confirmed'])) {
			$this->classDeletionRun();
		}
		else if(isset($_POST['declined'])) {
			$this->_interface->dieMsg(_g('The Class was not deleted.'));
		}
		else {
			$this->classDeletionConfirmation();
		}
	}

	/**
	 * Displays a Confirmation asking wether the user wants to delete the class
	 *
	 * Dies Displaying the Form
	 */
	protected function classDeletionConfirmation() {

		$class = $this->classGet($_GET['ID']);
		$this->_smarty->assign('class', $class);
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'deleteClassConfirmation.tpl');
	}

	/**
	 * Checks the given ID before starting the Deletion-process of the Class
	 *
	 * Dies displaying a message when Input not correct
	 */
	protected function classDeletionInputCheck() {

		$gump = new GUMP();
		$gump->rules(array('ID' => array(
			'required|min_len,1|max_len,11|numeric', '', _g('Class-ID')
		)));
		if(!($_GET = $gump->run($_GET))) {
			$this->_interface->dieError(
				$gump->get_readable_string_errors(true));
		}
	}

	/**
	 * Checks the Input and deletes the Class
	 *
	 * Dies displaying a Message
	 */
	protected function classDeletionRun() {

		$this->classDeletionInputCheck();
		$this->classDeletionUpload();
		$this->_interface->dieSuccess(_g(
			'The Class was successfully deleted'));
	}

	/**
	 * Deletes the Class with the given ID from the Database
	 *
	 * Dies displaying a Message on Error
	 */
	protected function classDeletionUpload() {

		try {
			$stmt = $this->_pdo->prepare(
				'DELETE c.*, uic.*
				FROM class c
				LEFT JOIN jointUsersInClass uic ON c.ID = uic.ClassID
				WHERE c.ID = :id');

			$stmt->execute(array(':id' => $_GET['ID']));

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not delete the Class!') . $e->getMessage());
		}
	}

	/**
	 * Display all Classes to the User
	 */
	protected function submoduleDisplayClassesExecute() {

		$classes = $this->classesGetWithAdditionalReadableData();
		$this->_smarty->assign('classes', $classes);
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'displayClasses.tpl');
	}

	/**
	 * Fetches one/all Classes from the Database and linked data
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param  $classId If Set, only the Data for the Class will be fetched -
	 * else all classes will be fetched
	 * @return array The Classes
	 */
	protected function classesGetWithAdditionalReadableData($classId = false) {

		$whereStr = ($classId !== false) ? 'WHERE c.ID = :id' : '';

		$subQueryCountUsers = '(SELECT Count(*)
				FROM jointUsersInClass uic
				JOIN users ON users.ID = uic.UserID
				WHERE uic.statusId = (SELECT ID FROM usersInClassStatus
					WHERE name="%s") AND c.ID = uic.ClassID
				)
			';

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT c.*, sy.label As schoolyearLabel,
					cu.translatedName AS unitTranslatedName,
					GROUP_CONCAT(DISTINCT ct.name SEPARATOR "; ") AS classteacherName,
					'. sprintf ($subQueryCountUsers, 'active') . ' AS activeCount,
					'. sprintf ($subQueryCountUsers, 'waiting') . ' AS waitingCount,
					'. sprintf ($subQueryCountUsers, 'request1') . ' AS request1Count,
					'. sprintf ($subQueryCountUsers, 'request2') . ' AS request2Count
				FROM class c
				LEFT JOIN schoolYear sy ON c.schoolyearId = sy.ID
				LEFT JOIN kuwasysClassUnit cu ON c.unitId = cu.ID
				LEFT JOIN (
						SELECT ctic.ClassID AS classId,
							CONCAT(ct.forename, " ", ct.name) AS name
						FROM classTeacher ct
						JOIN jointClassTeacherInClass ctic
							ON ct.ID = ctic.ClassTeacherID
					) ct ON c.ID = ct.classId
				' . $whereStr . '
				GROUP BY c.ID');

			if($classId !== false) {
				$stmt->execute(array(':id' => $classId));
				return $stmt->fetch();
			}
			else {
				$stmt->execute();
				return $stmt->fetchAll();
			}


		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not fetch the Class(es)!'));
		}
	}

	protected function submoduleDisplayClassDetailsExecute() {

		$class = $this->classesGetWithAdditionalReadableData($_GET['ID']);
		$users = $this->usersByClassIdGet($_GET['ID']);
		$users = $this->assignClassesOfSameClassunitToUsers(
			$users, $class['unitId']);
		$this->_smarty->assign('class', $class);
		$this->_smarty->assign('users', $users);
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'displayClassDetails.tpl');
	}

	/**
	 * Returns the Users that are in the ClassId
	 * @param  string $classId The ID of the Class
	 * @return array           The Users that are in the Class and the Status
	 *                         of this connection
	 */
	protected function usersByClassIdGet($classId) {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT u.*, g.gradename AS gradename,
					uics.translatedName AS statusTranslated
				FROM users u
				JOIN jointUsersInClass uic ON u.ID = uic.UserID
				JOIN usersInClassStatus uics ON uic.statusId = uics.ID
				LEFT JOIN (
						SELECT CONCAT(label, "-", gradelevel) AS gradename,
							uigs.UserID AS userId
						FROM Grades g
						JOIN usersInGradesAndSchoolyears uigs ON
							uigs.gradeId = g.ID
						WHERE uigs.schoolyearId = @activeSchoolyear
					) g ON g.userId = u.ID
				WHERE uic.ClassID = :id'
			);

			$stmt->execute(array(':id' => $classId));
			return $stmt->fetchAll();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Users by Class') . $e->getMessage());
		}
	}

	/**
	 * Fetches the Classes that has the UnitId and one of the User in it
	 *
	 * @param  string $userIds The User-IDs of the User
	 * @param  string $unitId The Unit-ID of the Class
	 * @return array          Returns the Classes
	 */
	protected function assignClassesOfSameClassunitToUsers($users, $unitId) {

		$userIdString = '';
		foreach($users as &$user) {
			$userIdString .= $this->_pdo->quote($user['ID']) . ', ';
		}
		$userIdString = trim($userIdString, ', ');

		try {
			$stmt = $this->_pdo->prepare(
				"SELECT c.*, uic.UserID AS userId FROM class c
				JOIN jointUsersInClass uic ON c.ID = uic.ClassID
				WHERE uic.UserID IN($userIdString) AND c.unitId = :unitId
					AND c.ID <> :classId"
			);

			$stmt->execute(
				array(':unitId' => $unitId, ':classId' => $_GET['ID']));
			while($row = $stmt->fetch()) {
				foreach($users as &$user) {
					if($user['ID'] == $row['userId']) {
						$user['classesOfSameDay'][] = $row;
					}
				}
			}
			return $users;

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Classes of the User at the same day') . $e->getMessage());
		}
	}

	protected function submoduleGlobalClassRegistrationExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleAssignUsersToClassesExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleCreateClassSummaryExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleUnregisterUserExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Handy functions to display things to the User
	 * @var AdminInterface
	 */
	protected $_interface;

	/**
	 * The AccessControlLayer used for getting the Submodules
	 * @var Acl
	 */
	protected $_acl;

	/**
	 * The Database-Connection
	 * @var PDO
	 */
	protected $_pdo;

}

?>
