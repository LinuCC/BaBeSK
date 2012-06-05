<?php

class InstallationComponent {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_name;
	private $_nameDisplay;
	private $_path;
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct($name = NULL, $nameDisplay = NULL, $path = NULL) {
		
		!isset($name) or $this->_name = $name;
		!isset($nameDisplay) or $this->_nameDisplay = $nameDisplay;
		!isset($path) or $this->_path = $path;
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	public function getName() {
		return $this->_name;
	}
	
	public function getNameDisplay() {
		return $this->_nameDisplay;
	}
	
	public function setName($name) {
		$this->_name = $name;
	}
	
	public function setNameDisplay($name) {
		$this->_nameDisplay = $name;
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute() {
		
		die('The method "execute" of "' . $this->_name . '" was not overwritten. Cancelling Process');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

}

?>