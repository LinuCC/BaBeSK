<?php

/**
 * This class contains Data used by the modules. It is intended to solve the problem of global data
 * (like the smarty-variable) and is a "databridge" between the main-Class of the Program which stores
 * all the Information (here Administrator.php) and the modules.
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class DataContainer {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($smarty, $interface) {
		$this->_smarty = $smarty;
		$this->_interface = $interface;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	public function getSmarty () {
		return $this->_smarty;
	}

	public function setSmarty ($smarty) {
		$this->_smarty = $smarty;
	}
	
	public function setInterface ($interface) {
		$this->_interface = $interface;
	}
	
	public function getInterface () {
		return $this->_interface;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	/**
	 * Needed to use Smarty, an external Templating-Program for better separation between Programcode and
	 * Displaying Code
	 */
	protected $_smarty;
	protected $_interface;
}

?>