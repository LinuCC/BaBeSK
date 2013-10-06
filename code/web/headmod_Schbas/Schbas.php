<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * class for Interface web
 * @author Mirek Hancl <mirek@hancl.de>
 *
 */
class Schbas extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name,$headmod_menu) {
		parent::__construct($name, $display_name,$headmod_menu);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute( $dataContainer) {


		$defaultMod = new ModuleExecutionInputParser(
				'root/web/Schbas/LoanSystem');
		$dataContainer->getAcl()->moduleExecute($defaultMod,
				$dataContainer);


	}
}
?>
