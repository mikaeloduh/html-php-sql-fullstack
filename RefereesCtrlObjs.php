<?php

class RefereesDataLink extends DataLink {
	public function __construct() {
		// Construct queries list
		$queries = array();

		// Create Players table query and push onto queries list
		$query  = 'SELECT * ';
		$query .= 'FROM';
		$query .= ' Referee;';
		$queries['referees'] = $query;
		
		// Initialize base
		Parent::__construct($queries);
	}
}

class RefereesPanelController extends TaskPanelController {
	public function buildPanel() {
		// Create data link object
		$refereesTaskDataObj = new RefereesDataLink();
		$resultSet = $refereesTaskDataObj->executeQueries();
		
		echo "<script>\n";		// Script output opening tag
		
		// Retrieve data and embed in html stream
		foreach ($resultSet as $key => $result) {
			$data = $refereesTaskDataObj->retrieveData($resultSet[$key]);
			$this->embedDataSet('    ', $key . 'Data', $data, TRUE, TRUE);
		}
		
		echo "</script>\n";		// Script output closing tag
		
	}
}

function getTaskPanelController() {
	return new RefereesPanelController();
}

?>