<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Schbas/Schbas.php';
require_once PATH_INCLUDE . '/Schbas/Loan.php';

class Loan extends Schbas {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		require_once 'AdminLoanInterface.php';
		require_once 'AdminLoanProcessing.php';

		$LoanInterface = new AdminLoanInterface($this->relPath);
		$LoanProcessing = new AdminLoanProcessing($LoanInterface);

		if(isset($_GET['wacken'])) {
			if(isset($_POST['barcode']) && isset($_POST['userId'])) {
				$this->bookLoanToUserByBarcode('KU 2006 12 1 / 39', 1267);
			}
			else {
				$this->loanDisplay("0000000001");
			}
			die();
		}

		if ('GET' == $_SERVER['REQUEST_METHOD'] && isset($_GET['inventarnr'])) {
			if (!$LoanProcessing->LoanBook(urldecode($_GET['inventarnr']),$_GET['uid'])) {
				$LoanInterface->LoanEmpty();
			} else {
				$LoanProcessing->LoanAjax($_GET['card_ID']);
			}
		}
		else if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$LoanProcessing->Loan($_POST['card_ID']);
		}
		else{
			// Scan the card-id
			$this->displayTpl('form.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::moduleTemplatePathSet();
	}

	private function loanDisplay($cardnumber) {

		$loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);
		$user = $this->userByCardnumberGet($cardnumber);
		$formSubmitted = $this->userFormSubmittedCheck($user);
		if($user->getSchbasAccounting() !== Null) {
			$userPaid = $this->userPaidForLoanCheck($user);
			$userSelfpayer = $this->selfpayerCheck($user);
		}
		else {
			$userPaid = false;
			$userSelfpayer = false;
		}
		$booksLent = $this->booksStillLendByUserGet($user);
		$booksSelfpaid = $user->getSelfpayingBooks();
		$booksToLoan = $loanHelper->loanBooksGet($user->getId());
		$this->_smarty->assign('user', $user);
		$this->_smarty->assign('formSubmitted', $formSubmitted);
		$this->_smarty->assign('userPaid', $userPaid);
		$this->_smarty->assign('userSelfpayer', $userSelfpayer);
		$this->_smarty->assign('booksLent', $booksLent);
		$this->_smarty->assign('booksSelfpaid', $booksSelfpaid);
		$this->_smarty->assign('booksToLoan', $booksToLoan);
		$this->displayTpl('user-loan-list.tpl');
	}

	private function userByCardnumberGet($cardnumber) {

		$card = $this->_entityManager->getRepository('Babesk:BabeskCards')
			->findOneByCardnumber($cardnumber);
		if($card) {
			if(!$card->getLost()) {
				$user = $card->getUser();
				if($user->getLocked()) {
					$this->_interface->dieError('Der Benutzer ist gesperrt!');
				}
				else {
					return $user;
				}
			}
			else {
				$this->_interface->dieError(
					'Diese Karte ist verloren gegangen!'
				);
			}
		}
		else {
			$this->_interface->dieError('Die Karte wurde nicht gefunden!');
		}
	}

	private function userFormSubmittedCheck($user) {

		$acc = $user->getSchbasAccounting();
		return isset($acc);
	}

	private function userPaidForLoanCheck($user) {

		$acc = $user->getSchbasAccounting();
		$loanChoice = $acc->getLoanChoice();
		return (
			(
				$loanChoice->getAbbreviation() == 'ln' ||
				$loanChoice->getAbbreviation() == 'lr'
			) &&
			$acc->getPayedAmount() >= $acc->getAmountToPay()
		);
	}

	private function selfpayerCheck($user) {

		$abbr = $user->getSchbasAccounting()
			->getLoanChoice()
			->getAbbreviation();
		return $abbr == 'nl';
	}

	private function booksStillLendByUserGet($user) {

		$books = $this->_entityManager->createQuery(
			'SELECT b FROM Babesk:SchbasBooks b
				INNER JOIN b.exemplars i
				INNER JOIN i.usersLent u
				WHERE u.id = :userId
		')->setParameter('userId', $user->getId())
			->getResult();
		return $books;
	}

	private function booksSelfpaidByUserGet($user) {

		$books = $user->getSelfpayingBooks();
		return $books;
	}

	private function bookLoanToUserByBarcode($barcode, $userId) {

		$exemplar = $this->exemplarByBarcodeGet($barcode);
		if($exemplar) {
			//Check if book is lent to someone
			if($exemplar->getUsersLent()->count() == 0) {
				if($this->bookLoanToUserToDb($exemplar, $userId)) {
					die(json_encode(array(
						'bookId' => $exemplar->getBook()->getId(),
						'exemplarId' => $exemplar->getId()
					)));
				}
				else {
					http_response_code(500);
					die(json_encode(array(
						'message' => 'Ein Fehler ist beim Eintragen der ' .
							'Ausleihe aufgetreten.'
					)));
				}
			}
			else {
				http_response_code(500);
				die(json_encode(array(
					'message' => 'Dieses Exemplar ist im System bereits ' .
						'verliehen!'
				)));
			}
		}
		else {
			$this->_logger->log('Book not found by barcode',
				'Notice', Null, json_encode(array('barcode' => $barcode)));
			http_response_code(500);
			die(json_encode(array(
				'message' => 'Das Exemplar konnte nicht anhand des Barcodes ' .
					'gefunden werden!'
			)));
		}
	}

	/**
	 * Checks if the book-exemplar is already lent to a user
	 * @param  string $barcode The Barcode of the exemplar
	 * @return bool            true if it is lent
	 */
	private function exemplarByBarcodeGet($barcodeStr) {

		$barcodeStr = $this->barcodeNormalize($barcodeStr);
		$barcode = $this->barcodeParseToArray($barcodeStr);
		//Delimiter not used in Query
		unset($barcode['delimiter']);
		$query = $this->_entityManager->createQuery(
			'SELECT i, b FROM Babesk:SchbasInventory i
				INNER JOIN i.book b
					WITH b.class = :class AND b.bundle = :bundle
				INNER JOIN b.subject s
					WITH s.abbreviation = :subject
				WHERE i.yearOfPurchase = :purchaseYear
					AND i.exemplar = :exemplar
		')->setParameters($barcode);
		try {
			$lent = $query->getSingleResult();
		}
		catch(\Doctrine\ORM\NoResultException $e) {
			return false;
		}
		return $lent;
	}

	private function barcodeParseToArray($barcode) {

		$barcodeAr = array();
		list(
				$barcodeAr['subject'],
				$barcodeAr['purchaseYear'],
				$barcodeAr['class'],
				$barcodeAr['bundle'],
				$barcodeAr['delimiter'],
				$barcodeAr['exemplar']
			) = explode(' ', $barcode);
		return $barcodeAr;
	}

	private function barcodeNormalize($barcode) {

		$barcode = str_replace("-", "/", $barcode);
		//add space after / when it's missing
		$barcode = preg_replace("/\/([0-9])/", "/ $1", $barcode);
		$barcode = str_replace("  ", " ", $barcode);
		return $barcode;
	}

	private function bookLoanToUserToDb($exemplar, $userId) {

		try {
			$lending = new \Babesk\ORM\SchbasLending();
			$user = $this->_entityManager->find(
				'Babesk:SystemUsers', $userId
			);
			$lending->setUser($user);
			$lending->setInventory($exemplar);
			$lending->setLendDate(new \DateTime());
			$this->_entityManager->persist($lending);
			//$this->_entityManager->flush();

		} catch (Exception $e) {
			$this->_logger->log('Error loaning a book-exemplar to a user',
				'Moderate', Null, json_encode(array(
					'msg' => $e->getMessage())));
			return false;
		}
		return true;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>
