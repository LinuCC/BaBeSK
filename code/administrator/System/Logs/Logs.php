<?php

namespace administrator\System;

require_once PATH_ADMIN . '/System/System.php';

class Logs extends \System {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->moduleTemplatePathSet();
		$this->displayTpl('mainmenu.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>