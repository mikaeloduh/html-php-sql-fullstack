<?php

class CoachesDataLink extends DataLink {
	public function __construct() {
		// Construct queries list
		$queries = array();
		
		// Create Players table query and push onto queries list
		$query  = 'SELECT * ';
		$query .= 'FROM';
		$query .= ' Coach;';
		$queries['coaches'] = $query;
				
		// Create Team titles query and push onto queries list
		$query  = 'SELECT';
		$query .= ' TID, name ';
		$query .= 'FROM';
		$query .= ' Team;';
		$queries['teamId'] = $query;
				
		// Initialize base
		Parent::__construct($queries);
	}
}

class CoachesPanelController extends TaskPanelController {
	public function buildPanel() {
		// Create data link object
		$coachesTaskDataObj = new CoachesDataLink();		
		$resultSet = $coachesTaskDataObj->executeQueries();
		
		echo "<script>\n";		// Script output opening tag
		
		// Retrieve data and embed in html stream
		foreach ($resultSet as $key => $result) {
			$data = $coachesTaskDataObj->retrieveData($resultSet[$key]);
			$this->embedDataSet('    ', $key . 'Data', $data, TRUE, TRUE);
		}
		
		echo "</script>\n";		// Script output closing tag
	}
}

function getTaskPanelController() {
	return new CoachesPanelController();
}

?>