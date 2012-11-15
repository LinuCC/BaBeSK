<?php

require_once PATH_ACCESS . '/TableManager.php';

class KuwasysUsersInClassStatusManager extends TableManager {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct($interface = NULL) {
		parent::__construct('usersInClassStatus');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function statusAdd ($name, $translatedName) {
		$this->addEntry ('name', $name, 'translatedName', $translatedName);
	}

	/** Returns the status with the given $id
	 * @param $id the Id of the status to return
	 * @return array () An Array containing the information of the status
	 */
	public function statusGet ($id) {
		return $this->searchEntry('ID =' . $id);
	}

	/** Returns one status based on the given $name
	 * @param $name The name of the status to return
	 * @return array () An Array containing the information of the status
	 */
	public function statusGetByName ($name) {
		return $this->searchEntry(sprintf('name ="%s"', $name));
	}

	/** Returns all statuses of the table
	 *
	 * @return array (array()) An Array containing the statuses and an Array containing the information of each status
	 */
	public function statusGetAll () {
		return $this->getTableData ();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
}
?>