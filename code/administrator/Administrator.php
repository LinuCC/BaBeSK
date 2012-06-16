<?php

require_once "../include/path.php";

/**
 *
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Administrator {
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $_moduleManager;
	private $_userLoggedIn;
	private $_smarty;
	private $_logger;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct () {

		$this->initEnvironment();

		require_once PATH_ADMIN . '/admin_functions.php';
		require_once PATH_INCLUDE . "/logs.php";
		require_once 'AdminInterface.php';
		require_once PATH_INCLUDE . "/functions.php";
		require_once PATH_INCLUDE . '/exception_def.php';
		require_once PATH_INCLUDE . '/moduleManager.php';
		require_once 'locales.php';

		validSession() or die(INVALID_SESSION);
		$this->initSmarty();

		$this->_logger = $logger; //from logs.php (Its Bullshit btw, remove the Object in logs.php)
		$this->_moduleManager = new ModuleManager('administrator');

	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	public function getUserLoggedIn () {
		return $this->_userLoggedIn;
	}

	public function setUserLoggedIn ($userLoggedIn) {
		$this->_userLoggedIn = $userLoggedIn;
	}

	public function getSmarty () {
		return $this->_smarty;
	}

	public function getLogger () {
		return $this->_logger;
	}

	public function getModuleManager () {
		return $this->_moduleManager;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function initUserInterface () {

		$this->_smarty->assign('_ADMIN_USERNAME', $_SESSION['username']);
		$this->_smarty->assign('sid', htmlspecialchars(SID));
		$this->_smarty->assign('base_path', PATH_SMARTY . '/templates/administrator/base_layout.tpl');
	}

	public function userLogOut () {

		$login = False;
		session_destroy();
		$this->showLogin();
	}

	public function executeModule ($moduleName) {

		$smarty = $this->_smarty;
		$this->_moduleManager->execute($moduleName, false);
	}

	public function MainMenu () {

		$allowedModules = $this->_moduleManager->getAllowedModules();
		$headModules = $this->_moduleManager->getHeadModulesOfModules($allowedModules);
		$head_mod_arr = array();

		foreach ($headModules as $headModule) {
			$head_mod_arr[] = array('name' => $headModule->getName(), 'display_name' => $headModule->getDisplayName());
		}

		$this->_smarty->assign('is_mainmenu', true);
		$this->_smarty->assign('modules', $allowedModules);
		$this->_smarty->assign('head_modules', $head_mod_arr);
		$this->_smarty->assign('module_names', $this->_moduleManager->getModuleDisplayNames());
		$this->_smarty->display('administrator/menu.tpl');
	}

	public function testLogin () {

		if (!$this->getUserLoggedIn()) {
			if ($this->showLogin())
				return true;
			else
				return false;
		}
		else {
			return true;
		}

	}

	public function showLogin () {

		$smarty = $this->_smarty;
		require_once "login.php";

		if ($login) //coming from login.php, another problem...
			return true;
		else
			return false;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	private function initEnvironment () {

		$this->setPhpIni();

		//if this value is not set, the modules will not execute
		define('_AEXEC', 1);

		session_name('sid');
		session_start();
		error_reporting(E_ALL);
	}

	private function initSmarty () {

		require_once PATH_SMARTY . "/smarty_init.php";

		$this->_smarty = $smarty;
		$this->_smarty->assign('smarty_path', REL_PATH_SMARTY);
		$this->_smarty->assign('status', '');
		$this->_smarty->assign('babesk_version', file_get_contents("../version.txt"));
	}

	private function setPhpIni () {

		ini_set('display_errors', 1);
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 0);
		ini_set("default_charset", "utf-8");
	}
}

?>