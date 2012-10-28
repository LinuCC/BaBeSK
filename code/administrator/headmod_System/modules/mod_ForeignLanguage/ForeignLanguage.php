<?php

require_once PATH_INCLUDE . '/Module.php';

class ForeignLanguage extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		require_once 'AdminForeignLanguageInterface.php';
		require_once 'AdminForeignLanguageProcessing.php';

		$ForeignLanguageInterface = new AdminForeignLanguageInterface($this->relPath);
		$ForeignLanguageProcessing = new AdminForeignLanguageProcessing($ForeignLanguageInterface);

		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //edit the language list
					$ForeignLanguageProcessing->EditForeignLanguages(0);
				break;
				case 2: //save the langiuage list
					$ForeignLanguageProcessing->EditForeignLanguages($_POST);
				break;
				case 3: //edit the users
					if (isset($_POST['filter'])) {
						$ForeignLanguageProcessing->ShowUsers($_POST['filter']);
					} else {
						$ForeignLanguageProcessing->ShowUsers("name");
					};
				break;
				case 4: //save the users
					$ForeignLanguageProcessing->SaveUsers($_POST);
				break;
			}
		} elseif  (('GET' == $_SERVER['REQUEST_METHOD'])&&isset($_GET['action'])) {
					$action = $_GET['action'];
					switch ($action) {
						case 3: //show the users
					if (isset($_GET['filter'])) {
						$ForeignLanguageProcessing->ShowUsers($_GET['filter']);
					} else {
						$ForeignLanguageProcessing->ShowUsers("name");
					}
					}


		} else {
			$ForeignLanguageInterface->ShowSelectionFunctionality();
		}
	}
}

?>