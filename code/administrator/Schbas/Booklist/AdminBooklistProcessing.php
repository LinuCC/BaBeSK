<?php

class AdminBooklistProcessing {
	function __construct($BookInterface) {

		$this->BookInterface = $BookInterface;
		$this->messages = array(
				'error' => array('no_books' => 'Keine B&uuml;cher gefunden.','notFound' => 'Buch nicht gefunden!'));
	}

	var $messages = array();
	private $bookInterface;

	/**
	 * Shows booklist
	 * @param $filter
	 */
	function OLDShowBooklist($option, $filter) {

		require_once PATH_ACCESS . '/BookManager.php';
		require_once PATH_ACCESS . '/UserManager.php';

		$booklistManager = new BookManager();
		$userManager = new UserManager();

		try {
			isset($_GET['sitePointer'])?$showPage = $_GET['sitePointer'] + 0:$showPage = 1;
			$nextPointer = $showPage*10-10;
			if ($option == "filter"){
				$booklist = $booklistManager->getBooklistSorted($nextPointer, $filter);
			}elseif ($option == "search"){
				try {
					$class = $userManager->getClassByUsername($filter);
					$booklist = $booklistManager->getBooksByClass($class);
				} catch (Exception $e) {
					$booklist = $e->getMessage();
				}
				if ($booklist == 'MySQL returned no data!'){
					try {
						$booklist = $booklistManager->getBooksByClass($filter);
					} catch (Exception $e) {
						$this->BookInterface->dieError("Keine Eintr&auml;ge gefunden!");
					}
				}
			}
		} catch (Exception $e) {
			$this->logs
			->log('ADMIN', 'MODERATE',
					sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->booklistInterface->dieError($this->messages['error']['get_data_failed']);
		}
		$navbar = navBar($showPage, 'schbas_books', 'Schbas', 'Booklist', '1',$filter);
		$this->BookInterface->ShowBooklist($booklist,$navbar);
	}

	/**
	 * Show list of books which students can keep for next schoolyear, ordered by schoolyear.
	 */
	 function showBooksForNextYear() {

	 	require_once 'AdminBooklistInterface.php';
	 	if (isset($_POST['grade'])) {
	 		require_once PATH_ACCESS . '/BookManager.php';
	 		$booklistManager = new BookManager();
	 		$booklist_act = $booklistManager->getBooksByClass($_POST['grade']);
	 		$booklist_nxt = $booklistManager->getBooksByClass($_POST['grade']+1);


	 		$booklistFNY = array();
	 		$booklistFNY = array_map("unserialize", array_intersect($this->serialize_array_values($booklist_act),$this->serialize_array_values($booklist_nxt)));

	 		$this->showPdf($booklistFNY);
	 	}
	 	else {
	 		$this->BookInterface->ShowSelectionForBooksToKeep();
	 	}


	}

        /**
	 * Show list of books by topics.
	 */
	 function showBooksByTopic() {

	 	require_once 'AdminBooklistInterface.php';
	 	if (isset($_POST['topic'])) {
	 		require_once PATH_ACCESS . '/BookManager.php';
	 		$booklistManager = new BookManager();
	 		$booklist = $booklistManager->getBooksByTopic($_POST['topic']);
	 		$this->showPdfFT($booklist);
	 	}
	 	else {
	 		$this->BookInterface->ShowSelectionForBooksByTopic();
	 	}
	}

	function serialize_array_values($arr){
		foreach($arr as $key=>$val){
			//sort($val);
			$arr[$key]=serialize($val);
		}

		return $arr;
	}

	private function showPdf($booklist) {
			$books = '<table border="0" bordercolor="#FFFFFF" style="background-color:#FFFFFF" width="100%" cellpadding="0" cellspacing="1">

				<tr style="font-weight:bold; text-align:center;"><th>Fach</th><th>Titel</th><th>Verlag</th><th>ISBN-Nr.</th><th>Preis</th></tr>';
		foreach ($booklist as $book) {
			// $bookPrices += $book['price'];
			$books.= '<tr><td>'.$book["subject"].'</td><td>'.$book["title"].'</td><td>'.$book["publisher"].'</td><td>'.$book["isbn"].'</td><td align="right">'.$book["price"].' &euro;</td></tr>';
		}
		//$books .= '<tr><td></td><td></td><td></td><td style="font-weight:bold; text-align:center;">Summe:</td><td align="right">'.$bookPrices.' &euro;</td></tr>';
		$books .= '</table>';
		$books = str_replace('ä', '&auml;', $books);
		$books = str_replace('é', '&eacute;', $books);
		$this->createPdf("Lehrb&uuml;cher, die f&uuml;r Jahrgang ".($_POST['grade']+1)." behalten werden k&ouml;nnen",$books);
	}


        private function showPdfFT($booklist) {
            require_once 'LoanSystemPdf.php';
			$books = '<table border="0" bordercolor="#FFFFFF" style="background-color:#FFFFFF" width="100%" cellpadding="0" cellspacing="1">

				<tr style="font-weight:bold; text-align:center;"><th>Klasse</th><th>Titel</th><th>Verlag</th><th>ISBN-Nr.</th><th>Preis</th></tr>';
		 $classAssign = array(
				'5'=>'05,56',			// hier mit assoziativem array
										// arbeiten, in der wertzuw.
				'6'=>'56,06,69,67',		// alle kombinationen auflisten
								// sql-abfrage:
				'7'=>'78,07,69,79,67',	// SELECT * FROM `schbas_books` WHERE `class` IN (werte-array pro klasse)
				'8'=>'78,08,69,79,89',
				'9'=>'90,91,09,92,69,79,89',
				'10'=>'90,91,10,92',
				'11'=>'12,92,13',
				'12'=>'12,92,13');

                        foreach ($booklist as $book) {
                               $classKey="";
                            foreach ($classAssign as $key => $value) {
                               if (strpos($value,$book["class"]) !== false) $classKey.=$key."/";
                            }


                    $classKey = rtrim($classKey, "/");
			$books.= '<tr><td>'.$classKey.'</td><td>'.$book["title"].'</td><td>'.$book["publisher"].'</td><td>'.$book["isbn"].'</td><td align="right">'.$book["price"].' &euro;</td></tr>';
		}

		$books .= '</table>';
		$books = str_replace('ä', '&auml;', $books);
		$books = str_replace('é', '&eacute;', $books);
                try {
			$pdfCreator = new LoanSystemPdf("Lehrb&uuml;cher f&uuml;r Fach ".($_POST['topic']),$books,"");
			$pdfCreator->create();
			$pdfCreator->output("Buchliste_Fach_".$_POST['topic']);

		} catch (Exception $e) {
			$this->_interface->DieError('Konnte das PDF nicht erstellen!');
		}


	}


	/**
	 * Creates a PDF for the Participation Confirmation and returns its Path
	 */
	private function createPdf ($page1Title,$page1Text) {

		require_once 'LoanSystemPdf.php';

		try {
			$pdfCreator = new LoanSystemPdf($page1Title,$page1Text,$_POST['grade']);
			$pdfCreator->create();
			$pdfCreator->output("Buchliste_Folgejahr_".$_POST['grade']);

		} catch (Exception $e) {
			$this->_interface->DieError('Konnte das PDF nicht erstellen!');
		}
	}

	/**
	 * Returns the book ID by a given ISBN
	 */
	function getBookIdByISBN($isbn_search) {
		require_once PATH_ACCESS . '/BookManager.php';
		$bookManager = new BookManager();
		try {
			$book_id = $bookManager->getBookIDByISBN($isbn_search);
		} catch (Exception $e) {
			$this->BookInterface->dieError($this->messages['error']['notFound'] . $e->getMessage());
		}
		return $book_id['id'];
	}

	/**
	 *
	 * @var unknown
	 */
	function ScanForDeleteEntry() {
		$this->BookInterface->ShowScanforDeleteEntry();
	}
}

?>