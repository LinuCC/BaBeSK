<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Schbas/Schbas.php';

class Booklist extends Schbas {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		require_once 'AdminBooklistInterface.php';
		require_once 'AdminBooklistProcessing.php';

		$BookInterface = new AdminBooklistInterface($this->relPath);
		$BookProcessing = new AdminBooklistProcessing($BookInterface);

		$action_arr = array('show_booklist' => 1,
							'add_book' => 4,
							'del_book' => 6);

		if ('POST' == $_SERVER['REQUEST_METHOD']){
		if (isset($_GET['action'])) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //show booklist
					if (isset($_POST['filter'])){
						$BookProcessing->ShowBooklist("filter", $_POST['filter']);
					}else{
						$BookProcessing->ShowBooklist("filter","subject");
					}
					break;
				case 2: //edit a book
					if (isset ($_POST['isbn_search'])) {
						$bookID = $BookProcessing->getBookIdByISBN($_POST['isbn_search']);
						$BookProcessing->editBook($bookID);
					}
					else if (!isset ($_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle'])){
						$BookProcessing->editBook($_GET['ID']);
					}else{
						$BookProcessing->changeBook($_GET['ID'],$_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle']);
					}
					break;
				case 3: //delete an entry

					if (isset($_POST['barcode'])) {
						try {

							$BookProcessing->DeleteEntry($BookProcessing->GetIDFromBarcode($_POST['barcode']));
						} catch (Exception $e) {
						}
					}
					else if (isset($_POST['delete'])) {
						$BookProcessing->DeleteEntry($_GET['ID']);
					} else if (isset($_POST['not_delete'])) {
						$BookInterface->ShowSelectionFunctionality($action_arr);
					} else {

						if (!$BookProcessing->isInvForBook($_GET['ID'])){
							$BookProcessing->DeleteConfirmation($_GET['ID']);
						}else{
							$BookInterface->dieError("Es ist noch Inventar zu diesem Buch vorhanden! Bitte l&ouml;schen Sie dies zuerst!");
						}
					}
					break;
				case 4: //add an entry
					if (!isset($_POST['title'])) {
						$BookProcessing->AddEntry();
					} else {
						$BookProcessing->AddEntryFin($_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle']);
					}
					break;
				case 5: //filter
					$BookProcessing->ShowBooklist("search", $_POST['search']);
					break;

					case 6: //search an entry for deleting

						$BookProcessing->ScanForDeleteEntry();

						break;
					case 'showBooksFNY':
						$BookProcessing->showBooksForNextYear();
						break;
                                            case 'showBooksBT':
						$BookProcessing->showBooksByTopic();
						break;
				break;
			}
		}
		}elseif  (('GET' == $_SERVER['REQUEST_METHOD'])&&isset($_GET['action'])) {
			$action = $_GET['action'];
			$mode = $_GET['mode'];
			switch ($action) {
				case 1: //show the users
					if (isset($_GET['filter'])) {
						$BookProcessing->ShowBooklist($mode,$_GET['filter']);
					} else {
						$BookProcessing->ShowBooklist("name");
					}
			}
		}else {
			$BookInterface->ShowSelectionFunctionality($action_arr);
		}
	}
}

?>
