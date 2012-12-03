<?php

/**
 * A Class defining functions for userOutput. Intended to get inherited for program-parts.
 * @author voelkerball
 *
 */
class GeneralInterface {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct() {
		
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * Show an error to the user and dies
	 */
	function dieError ($msg) {
		
		die('ERROR:' . $msg);
	}
	
	/**
	 * Show a message to the user and dies
	 */
	function dieMsg ($msg) {
		
		die($msg);
	}
	
	function showError ($msg) {
		
		echo 'ERROR: ' . $msg . '<br>';
	}
	
	function showMsg ($msg) {
		
		echo $msg . '<br>';
	}
	
	function dieDisplay () {
		
		die();
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

}

?>