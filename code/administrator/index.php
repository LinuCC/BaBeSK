<?php

require_once 'Administrator.php';

$adminManager = new Administrator();

$adminManager->setUserLoggedIn(isset($_SESSION['UID']));

if (isset($_GET['action']) AND $_GET['action'] == 'logout') {
	$adminManager->userLogOut();
	die();
}
if ($adminManager->testLogin()) {
	//workaround of modules using smarty, logger and modManager globally
	$smarty = $adminManager->getSmarty();
	$logger = $adminManager->getLogger();
	$modManager = $adminManager->getModuleManager();
	$adminManager->initUserInterface();
	
	if (isset($_GET['section'])) {
		$adminManager->executeModule($_GET['section'], false);
	}
	else {
		$adminManager->MainMenu();
	}
}

?>