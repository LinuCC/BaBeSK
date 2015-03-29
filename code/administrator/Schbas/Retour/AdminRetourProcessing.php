<?php
class AdminRetourProcessing {


	var $messages = array();
	private $RetourInterface;

	function __construct($RetourInterface, $dataContainer) {


		require_once PATH_ACCESS . '/CardManager.php';
		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/LoanManager.php';
		require_once PATH_ACCESS . '/InventoryManager.php';
		require_once PATH_ACCESS . '/BookManager.php';

		$this->cardManager = new CardManager();
		$this->userManager = new UserManager();
		$this->loanManager = new LoanManager();
		$this->inventoryManager = new InventoryManager();
		$this->bookManager = new BookManager();
		$this->RetourInterface = $RetourInterface;
		$this->msg = array('err_empty_books' => 'keine B&uuml;cher ausgeliehen!',
							'err_get_user_by_card' => 'Kein Benutzer gefunden!',
							'err_card_id' => 'Die Karten-ID ist fehlerhaft!',
							'err_usr_locked' =>'Der Benutzer ist gesperrt!');
		$this->_logger = $dataContainer->getLogger();
		$this->_em = $dataContainer->getEntityManager();
	}

	/**
	 * Ausleihtabelle anzeigen
	 */
	function RetourTableData($card_id) {


		$uid = $this->GetUser($card_id);

		$loanbooks = $this->loanManager->getLoanlistByUID($uid);
		$data = array();
		foreach ($loanbooks as $loanbook){
			$invData = $this->inventoryManager->getInvDataByID($loanbook['inventory_id']);
			$bookdata = $this->bookManager->getBookDataByID($invData['book_id']);
			$datatmp = array_merge($loanbook, $invData, $bookdata);
			$data[] = $datatmp;
		}
			//$datatmp = null;
			$class = $this->fetchUserDetails($uid);
			// $class = $this->userManager->getUserDetails($uid);
		$class = $class['class'];
		$fullname = $this->userManager->getForename($uid)." ".$this->userManager->getName($uid)." (".$class.")";

		if (empty($data)) {
			$this->RetourInterface->dieError($fullname." hat ".sprintf($this->msg['err_empty_books']));
		} else {
			$this->RetourInterface->ShowRetourBooks($data,$card_id,$uid,$fullname);
		}
	}

	/**
	 * Ausleihtabelle per Ajax anzeigen
	 */
	function RetourTableDataAjax($card_id) {

		try {
			$uid = $this->GetUser($card_id);
			$loanbooks = $this->loanManager->getLoanlistByUID($uid);

			foreach ($loanbooks as $loanbook){
				$invData = $this->inventoryManager->getInvDataByID($loanbook['inventory_id']);
				$bookdata = $this->bookManager->getBookDataByID($invData['book_id']);
				$datatmp = array_merge($loanbook, $invData, $bookdata);
				$data[] = $datatmp;
				//$datatmp = null;
			}
			$class = $this->fetchUserDetails($uid);
			$class = $class['class'];
			$fullname = $this->userManager->getForename($uid)." ".$this->userManager->getName($uid)." (".$class.")";
			if (!isset($data)) {
				$this->RetourInterface->showMsg($fullname." hat keine B&uuml;cher ausgeliehen!");
			} else {
				$this->RetourInterface->ShowRetourBooksAjax($data,$card_id,$uid,$fullname);
			}

		} catch (Exception $e) {
			$this->_logger->log('Error in Ajax of Schbas Retour',
				'Moderate', Null, json_encode(array(
					'msg' => $e->getMessage())));
			$this->RetourInterface->showMsg('Ein Fehler ist aufgetreten.');
		}
	}


	/**
	 * Ein Buch zurückgeben
	 */
	function RetourBook($inventarnr,$uid) {

		$inv_id = $this->inventoryManager->getInvIDByBarcode($inventarnr);
	    if($this->loanManager->isUserEntry($uid)) {
	    try {
			$this->loanManager->RemoveLoanByIDs($inv_id, $uid);
			return $this->loanManager->isUserEntry($uid);
		} catch (Exception $e) {

		}
	    } else {
	    	return false;
	    }


	}

	/**
	 * Looks the user for the given CardID up, checks if the Card is locked and returns the UserID
	 * @param string $card_id The ID of the Card
	 * @return string UserID
	 */
	public function GetUser ($card_id) {
		$isCard = TableMng::query(sprintf(
		'SELECT COUNT(*) FROM BabeskCards WHERE cardnumber LIKE "%s"',$card_id));

		$isUser = TableMng::query(sprintf(
				'SELECT COUNT(*) FROM SystemUsers WHERE username LIKE "%s"',$card_id));

		if ($isCard[0]['COUNT(*)']==="1") {
			if (!$this->cardManager->valid_card_ID($card_id))
				$this->RetourInterface->dieError(sprintf($this->msg['err_card_id']));
			try {
				$uid = $this->cardManager->getUserID($card_id);
				if ($this->userManager->checkAccount($uid)) {
					$this->RetourInterface->dieError(sprintf($this->msg['err_usr_locked']));
				}
			} catch (Exception $e) {
				$this->RetourInterface->dieError($this->msg['err_get_user_by_card'] . ' Error:' . $e->getMessage());
			}
		}
		else if ($isUser[0]['COUNT(*)']==="1") {
			try {
				$uid = $this->userManager->getUserID($card_id);
				if ($this->userManager->checkAccount($uid)) {
					$this->RetourInterface->dieError(sprintf($this->msg['err_usr_locked']));
				}
			} catch (Exception $e) {
				$this->RetourInterface->dieError($this->msg['err_get_user_by_card'] . ' Error:' . $e->getMessage());
			}
		}
		else {
			$this->RetourInterface->dieError(
				sprintf($this->msg['err_get_user_by_card'])
			);
		}
		return $uid;
	}

	public function fetchUserDetails($userId) {

		$userDetails = TableMng::query(sprintf(
			'SELECT u.*,
			(SELECT CONCAT(g.gradelevel, g.label) AS class
					FROM SystemAttendances uigs
					LEFT JOIN SystemGrades g ON uigs.gradeId = g.ID
					WHERE uigs.userId = u.ID AND
						uigs.schoolyearId = @activeSchoolyear) AS class
			FROM SystemUsers u WHERE `ID` = %s', $userId));


		return $userDetails[0];
	}
}

?>