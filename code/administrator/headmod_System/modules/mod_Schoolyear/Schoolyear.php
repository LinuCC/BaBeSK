<?php

require_once PATH_INCLUDE . '/Module.php';
require_once 'SchoolyearInterface.php';

class Schoolyear extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		$this->_subExecPath = $dataContainer->getSubmoduleExecutionRequest();
		if($this->execPathHasSubmoduleLevel(1, $this->_subExecPath)) {
			$this->submoduleExecute($this->_subExecPath, 1);
			die();
		}

		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'addSchoolYear':
					$this->addSchoolYear();
					break;
				case 'showSchoolYear':
					$this->showSchoolYears();
					break;
				case 'activateSchoolYear':
					$this->activateSchoolYear();
					break;
				case 'changeSchoolYear':
					$this->changeSchoolYear();
					break;
				case 'deleteSchoolYear':
					$this->deleteSchoolYear();
					break;
				default:
					$this->_interface->dieError(_g('Wrong action-value!'));
					break;
			}
		}
		else {
			$this->_interface->displayMainMenu();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		$this->_dataContainer = $dataContainer;
		$this->_interface = new SchoolyearInterface($this->relPath,
			$this->_dataContainer->getSmarty());
		$this->_acl = $dataContainer->getAcl();
	}

	protected function addSchoolYear() {

		if(isset($_POST['label'])) {

			$this->handleCheckboxActive();
			$this->checkInput();
			$this->addSchoolYearToDatabase();
			$this->_interface->dieMsg(_g('The Schoolyear was added successfully'));
		}
		else {
			$this->showAddSchoolYearForm();
		}
	}

	protected function handleCheckboxActive() {

		if(!isset($_POST['active'])) {
			$_POST['active'] = 0;
		}
		else {
			$_POST['active'] = true;
		}
	}

	protected function checkInput() {

		try {
			inputcheck($_POST['label'], 'name', _g('Name of Schoolyear'));
		} catch(WrongInputException $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorInput'), $e->getFieldName()));
		}
	}

	protected function addSchoolYearToDatabase() {

		try {
			TableMng::query("INSERT INTO schoolYear (label, active)
				VALUES ('{$_POST['label']}', '{$_POST['active']}')");

		} catch(Exception $e) {
			$this->_interface->dieError(_g('Could not add the Schoolyear'));
		}
	}

	protected function showAddSchoolYearForm() {

		$this->_interface->displayAddSchoolYear();
	}

	protected function showSchoolYears() {

		$schoolYears = $this->getAllSchoolYears();
		$this->_interface->displayShowSchoolYears($schoolYears);
	}

	protected function getAllSchoolYears() {

		try {
			$schoolyears = TableMng::query('SELECT * FROM schoolYear');

		} catch(Exception $e) {
			$this->_interface->dieError(_g('Error Fetching the Schoolyears!'));
		}

		if(!count($schoolyears)) {
			throw new Exception('No Schoolyears exist!');
		}
		return $schoolyears;
	}

	protected function deleteSchoolYear() {

		if(isset($_POST['dialogConfirmed'])) {
			$this->deleteSchoolYearInDatabase();
			$this->_interface->dieMsg(_g('The Schoolyear was successfully deleted'));
		}
		else if(isset($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg(_g('The Schoolyear was not deleted'));
		}
		else {
			$this->_interface->displayDeleteSchoolYearConfirmation($this->getSchoolYear());
		}
	}

	protected function deleteSchoolYearInDatabase() {

		try {
			TableMng::query("DELETE FROM schoolYear WHERE ID = {$_GET['ID']}");

		} catch(Exception $e) {
			$this->_interface->dieError(_g('Could not delete the Schoolyear!'));
		}
	}

	protected function activateSchoolYear() {

		if(isset($_POST['dialogConfirmed'])) {
			$this->activateSchoolYearInDatabase();
			$this->_interface->dieMsg(_g('The Schoolyear was successfully activated'));
		}
		else if(isset($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg($this->_languageManager->getText('notActivateSchoolYear'));
		}
		else {
			$this->showActivateSchoolYearConfirmationDialog();
		}
	}

	protected function activateSchoolYearInDatabase() {

		TableMng::getDb()->autocommit(false);
		TableMng::query("UPDATE schoolYear SET active = 0
			WHERE active = 1");
		TableMng::query("UPDATE schoolYear SET active = 1
			WHERE ID = {$_GET['ID']}");
		TableMng::getDb()->autocommit(true);
	}

	protected function showActivateSchoolYearConfirmationDialog() {

		$schoolYear = $this->getSchoolYear();
		$this->_interface->displayActivateSchoolYearConfirmation($schoolYear);
	}

	protected function getSchoolYear() {

		try {
			$schoolyear = TableMng::querySingleEntry("SELECT * FROM schoolYear
				WHERE ID = {$_GET['ID']}");

		} catch(Exception $e) {
			$this->_interface->dieError(_g('Could not fetch the Schoolyear from the Database'));
		}
		return $schoolyear;
	}

	protected function changeSchoolYear() {

		if(isset($_POST['label'])) {
			$this->checkInput();
			$this->handleCheckboxActive();
			$this->changeSchoolYearInDatabase();
			$this->_interface->dieMsg(_g('The Schoolyear was successfully changed'));
		}
		else {
			$this->showChangeSchoolYear();
		}
	}

	protected function showChangeSchoolYear() {

		$schoolYear = $this->getSchoolYear();
		$this->_interface->displayChangeSchoolYear($schoolYear);
	}

	protected function changeSchoolYearInDatabase() {

		try {
			TableMng::query("UPDATE schoolYear SET label = '{$_POST['label']}',
				active = '{$_POST['active']}' WHERE ID = {$_GET['ID']}");

		} catch(Exception $e) {
			$this->_interface->dieError(_g('Could not change the Schoolyear'));
		}
	}

	/**
	 * User wants to switch to the next Schoolyear
	 */
	protected function submoduleSwitchSchoolyearExecute() {

		if($this->execPathHasSubmoduleLevel(2, $this->_subExecPath)) {
			$this->submoduleExecute(
				$this->_subExecPath,
				2,
				'switchSchoolyear');
		}
		else {
			$this->switchSchoolyearChooseSchoolyearExecute();
		}
	}

	/**
	 * Gets and escapes the Schoolyear-ID the User selected
	 *
	 * Dies with an Error if the Variable was not found
	 *
	 * @return string The SchoolyearId
	 */
	protected function schoolyearInputVarGet() {

		if(isset($_POST['schoolyearId'])) {
			TableMng::sqlEscape($_POST['schoolyearId']);
		}
		else {
			$this->_interface->dieError(_g('No schoolyear selected!'));
		}

		return $_POST['schoolyearId'];
	}

	protected function switchSchoolyearUploadExecute() {

		require_once 'SchoolyearSwitch.php';

		$schoolyearId = $this->schoolyearInputVarGet();
		$switcher = new SchoolyearSwitch($this->_interface);
		$switcher->execute($schoolyearId);
	}

	protected function switchSchoolyearChooseSchoolyearExecute() {

		$schoolyears = $this->getAllSchoolYears();
		$this->_interface->displaySelectSchoolyear($schoolyears);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


	protected $_interface;

	protected $_dataContainer;

	protected $_acl;

	protected $_subExecPath;
}

?>