<?php

class PlayersDataLink extends DataLink {
	public function __construct() {
		// Construct queries list
		$queries = array();
		
		// Create Players table query and push onto queries list
		$query  = 'SELECT * ';
		$query .= 'FROM';
		$query .= ' Player;';
		$queries['players'] = $query;
		
		// Create Team titles query and push onto queries list
		$query  = 'SELECT';
		$query .= ' TID, name ';
		$query .= 'FROM';
		$query .= ' Team;';
		$queries['teamId'] = $query;
		
		// Initialize base
		$this->init($queries);
	}
}

class PlayersPanelController extends TaskPanelController {
	public function buildPanel() {
		// Create data link object and retrieve result set
		$playerTaskDataObj = new PlayersDataLink();
		$resultSet = $playerTaskDataObj->executeQueries();
		
		echo "<script>\n";		// Script output opening tag
		
		// Retrieve data and embed in html stream
		$dataSet = $playerTaskDataObj->retrieveData($resultSet['players']);
		$this->embedDataSet('    ', 'playersData', $dataSet, TRUE, FALSE);
		
		$dataSet = $playerTaskDataObj->retrieveData($resultSet['teamId']);
		$slimmed = array();
		foreach ($dataSet as $dataRow) {
			$slimmed[$dataRow['TID']] = $dataRow['name'];
		}
		$this->embedDataSet('    ', 'teamIdData', $slimmed, FALSE, TRUE);
				
		echo "</script>\n";		// Script output closing tag
		
		// Import players edit grid template and script
		$this->importPageTemplate('SimpleSpreadsheetTemplate.php', 'PlayersTaskPnlInit.js');
	}
}

/* Factory function for AdminLogic, encapsulates creation of PlayersPanelContoller
 * objects
 */
function getTaskPanelController() {
	return new PlayersPanelController();
}

?>