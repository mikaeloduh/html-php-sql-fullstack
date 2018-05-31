<?php

class ScheduleDataLink extends DataLink {
	public function __construct() {
		// Construct queries list
		$queries = array();
		
		// Create Players table query and push onto queries list
		$query  = 'SELECT * ';
		$query .= 'FROM';
		$query .= ' Schedule;';
		$queries['schedule'] = $query;
		
		// Create Team titles query and push onto queries list
		$query  = 'SELECT';
		$query .= ' TID, name ';
		$query .= 'FROM';
		$query .= ' Team;';
		$queries['teamId'] = $query;
		
		// Create Referee names query and push onto queries list
		$query  = 'SELECT';
		$query .= ' RID, CONCAT(first_name, \' \', last_name) AS ref_name ';
		$query .= 'FROM';
		$query .= ' Referee;';
		$queries['refId'] = $query;
		
		// Initialize base
		Parent::__construct($queries);
	}
}

class SchedulePanelController extends TaskPanelController {
	public function buildPanel() {
		// Create data link object
		$scheduleTaskDataObj = new ScheduleDataLink();
		$resultSet = $scheduleTaskDataObj->executeQueries();
		
		echo "<script>\n";		// Script output opening tag
		
		// Retrieve data and embed in html stream
		foreach ($resultSet as $key => $result) {
			$data = $scheduleTaskDataObj->retrieveData($resultSet[$key]);
			$this->embedDataSet('    ', $key . 'Data', $data, TRUE, TRUE);
		}
		
		echo "</script>\n";		// Script output closing tag		
	}
}

function getTaskPanelController() {
	return new SchedulePanelController();
}

?>