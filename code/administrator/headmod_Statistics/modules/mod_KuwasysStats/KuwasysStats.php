<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * Analyzes data of the headmodule Kuwasys and puts them out as statistics
 *
 * @author  Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class KuwasysStats extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/administrator/' . $path;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

					// $this->test2();
		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'chooseChart':
					$this->mainpageWithChart($_POST['chartName']);
					break;
				case 'showChart':
					$this->chartSelect($_GET['chart']);
					break;
				default:
					die('Wrong action-value given');
			}
		}
		else {
			$this->mainPage();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		defined('_AEXEC') or die("Access denied");

		$this->_smarty = $dataContainer->getSmarty();
		$this->_interface = $dataContainer->getInterface();
	}

	protected function mainpageWithChart($chartName) {

		$this->_smarty->assign('chartName', $chartName);
		$this->mainPage();
	}

	protected function mainPage() {

		$this->_smarty->display($this->_smartyPath . 'main.tpl');
	}

	/**
	 * All hail the Spaghetti[Code]Monster! Yarrr!
	 * @return void
	 */
	protected function chartSelect($switch) {

		require_once PCHART_PATH . '/pDraw.class.php';
		require_once PCHART_PATH . '/pData.class.php';
		require_once PCHART_PATH . '/pImage.class.php';

		switch ($switch) {
			case 'gradesChosen':
				require_once 'KuwasysStatsGradesChosenBarChart.php';
				$chart = new KuwasysStatsGradesChosenBarChart();
				break;
			case 'gradelevelsChosen':
				require_once 'KuwasysStatsGradelevelsChosenBarChart.php';
				$chart = new KuwasysStatsGradelevelsChosenBarChart();
				break;
			case 'usersChosenInSchoolyears':
				require_once 'KuwasysStatsUsersChosenBySchoolyearBarChart.php';
				$chart = new KuwasysStatsUsersChosenBySchoolyearBarChart();
				break;
			case 'classesChosenInSchoolyears':
				require_once
					'KuwasysStatsClassesChosenBySchoolyearBarChart.php';
				$chart =
					new KuwasysStatsClassesChosenBySchoolyearBarChart();
				break;
			default:
				die('Wrong Chart-Switch given');
				break;
		}

		$this->chartShow($chart);
	}

	/**
	 * Shows the Chart
	 * @param  Object $chart A child of StatisticChart
	 * @return void
	 */
	protected function chartShow($chart) {

		try {
			$chart->setImageWidth($this->_imgWidth);
			$chart->setImageHeight($this->_imgHeight);
			$chart->imageDraw();
			$chart->imageDisplay();

		} catch (Exception $e) {
			$this->_interface->dieError('Konnte die Statistik nicht auswerten');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_smarty;
	protected $_interface;

	protected $_imgWidth = 1000;
	protected $_imgHeight = 300;

}

?>