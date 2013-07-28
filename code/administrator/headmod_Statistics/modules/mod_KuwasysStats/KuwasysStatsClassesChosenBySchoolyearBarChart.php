<?php

require_once PATH_STATISTICS_CHART . '/StatisticsBarChart.php';

class KuwasysStatsClassesChosenBySchoolyearBarChart extends StatisticsBarChart {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		parent::__construct();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function imageDraw($externalData = NULL) {

		$this->_heading['text'] = 'Kurswahlen in Schuljahren';
		$this->setMarginRatio(array('X' => 0.15, 'Y' => 0.15));
		$this->setScale(array("GridR" => 200,"GridG" => 200,"GridB" => 200,
			"DrawSubTicks" => TRUE, 'LabelRotation'  =>  45,
			"Pos" => SCALE_POS_TOPBOTTOM, "Mode" => SCALE_MODE_ADDALL_START0));
		parent::imageDraw();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function dataFetch() {

		$this->_schoolyearData = TableMng::query(
			'SELECT COUNT(*) AS classCount, sy.ID AS id, sy.label AS label
			FROM grade g
				JOIN usersInGradesAndSchoolyears uigs ON g.ID = uigs.GradeID
				JOIN jointUsersInClass uic ON uic.UserID = uigs.UserID
				JOIN jointClassInSchoolYear cisy
					ON uic.ClassID = cisy.ClassID
				JOIN schoolYear sy ON cisy.SchoolYearID = sy.ID
				JOIN (
						SELECT ID
						FROM usersInClassStatus
						WHERE name="active" OR name="waiting"
					) status ON status.ID = uic.statusId
				GROUP BY cisy.SchoolYearID
			', true);
	}

	protected function dataProcess() {

		$names = array();
		$data = array();
		$this->_pData = new pData();


		foreach($this->_schoolyearData as $schoolyear) {
			$names[] = $schoolyear['label'];
			$data[] = $schoolyear['classCount'];
		}

		$this->_pData->addPoints($data, 'Kurswahlen');
		$this->_pData->addPoints($names, 'schoolyearLabels');
		$this->_pData->setSerieDescription('schoolyearLabels', 'Schuljahr');
		$this->_pData->setAbscissa('schoolyearLabels');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_schoolyearData;
}

?>
