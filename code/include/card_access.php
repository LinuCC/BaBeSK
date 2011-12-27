<?php

     /**
     * Provides functions for the RFID Cards
     */

require_once PATH_INCLUDE.'/access.php';

class CardManager extends TableManager {
	function __construct() {
		parent::__construct('cards');
	}
	/**
	 * Validates the card ID
	 * Enter description here ...
	 * @param numeric $card_ID The Cardnumber
	 * @return boolean
	 */
	function valid_card_ID($card_ID) {
		/**
		 * @todo rename this function. this valids the cardnumber not the cardid
		 */
		require_once 'constants.php';
	
		if(!preg_match('/\A[0-9a-zA-Z]{10}\z/',$card_ID)){
			echo INVALID_CARD_ID."<br>";
			return false;
		}
		return true;
	}
	
	/**
	 * This function adds an entry to the card-table
	 * Enter description here ...
	 * @param numeric string $cardnumber The number of the card
	 * @param numeric string $UID The User-id of the card
	 */
	function addCard($cardnumber, $UID) {
		parent::addEntry('cardnumber', $cardnumber, 'UID', $UID);
	}
	
	/**
	 * This function checks if the Card is already existing in the database (and if yes, it returns the card-ID
	 * Enter description here ...
	 * @param unknown_type $cardnumber
	 */
	function is_card_existing($cardnumber) {
		$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE cardnumber=%s',
							$this->tablename, $cardnumber));
		$result = $this->db->query($query);
		$card = $result->fetch_assoc();
		if(!$card) {
			return false;
		}
		return true;
	}
	
	function getUserID($cardnumber) {
		require_once PATH_INCLUDE.'/dbconnect.php';
		$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE cardnumber=%s',
							$this->tablename, $cardnumber));
		$result = $this->db->query($query);
		$card = $result->fetch_assoc();
		if(!$card) {
			throw new MySQLVoidDataException('MySQL returned no data!'); 
		}
		$card = $result->fetch_assoc();
		if(!$card) {
			throw new MySQLVoidDataException('MySQL returned no data!'); 
		}
		$card = $result->fetch_assoc();
		if(!$card) {
			throw new MySQLVoidDataException('MySQL returned no data!'); 
		}
		$card = $result->fetch_assoc();
		if(!$card) {
			throw new MySQLVoidDataException('MySQL returned no data!'); 
		}
		$user = parent::getEntryData($card['UID'], 'UID');
		return $user['UID'];
	}
}
?>