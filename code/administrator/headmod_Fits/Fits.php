<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * class for Interface administrator
 * @author Mirek Hancl
 *
 */
class Fits extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name,$headmod_menu) {
		parent::__construct($name, $display_name,$headmod_menu);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		//function not needed, javascript is doing everything
	}
}
?>
