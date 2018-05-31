<?php

abstract class TaskPanelController {
	protected function setCSS($file) {
		echo "<link rel='stylesheet' type='text/css' href='themes/$file' />";
	}
	
	private function isAssociative($array) {
		return (bool) count(array_filter(array_keys($array), 'is_string'));
	}
	
	/*
	* embedDataSet($tab, $name, $dataSet, $size, $isfirst, $islast)
	*
	* Embeds a dataset as a javascript array. Works with both associative
	* normal php arrays containing associative arrays. Multiple datasets can
	* be merged into a single javascript var list by manipulating the $isfirst,
	* $islast parameters. Otherwise datasets that embedded as stand-alone
	* objects must be accompanied by TRUE for both parameters.
	*
	* Params:
	*  $tab	  Tab whitespace; prepended to all echoes
	*  $name	  The (variable) name of the javascript array
	*  $dataSet  The data to embed; can be normal or associative array
	*  $size	  The size of the dataset
	*  $isfirst  Boolean; indicates whether the dataset will be the first
	*				variable in a var list.
	*  $islast   Boolean; indicates whether the dataset will be the last
	*				variable in a var list.
	*/
	protected function embedDataSet($tab, $name, $dataSet, $isfirst, $islast) {
		// Test if dataset is associative array, if not then assume list of associative
		// Get size of dataset
		$isAssoc = $this->isAssociative($dataSet);
		$size = count($dataSet);
	
		// Define variable
		if ($isfirst)
			echo $tab . "var "; // If first in list then start var list
		else
			echo $tab . '    '; // Otherwise indent to keep aligned with rest of variable list
		echo "$name = ";
		if ($isAssoc)
			echo "{\n";
		else
			echo "[\n";
	
		if ($isAssoc) {
			// Embed data as associative list
			$count = 1;
			foreach ($dataSet as $field => $value) {
				$out = "'$field': ";
		
				// Weed out missing values, replace with javascript null object
				if ($value !== NULL)
					$out .= "'$value'";
				else
					$out .= 'null';
	
				$out .= ($count++ < $size) ? ",\n" : "\n";
	
				// Output array object string
				echo $tab;
				if ($isfirst && $islast) {  // If single entry then indent for single-entry list
					echo '    ';
				} else                      // Otherwise indent normally
					echo '        ';
				echo $out;
			}
		} else {
			// Embed data as list of associative sub-arrays
			for ($i = 1; $i <= $size; $i++) {
				$subSet = $dataSet[$i - 1];
				$subSize = count($subSet);

				// Build associative sub-array
				$out = '{ ';
				$colCount = 1;
				foreach ($subSet as $field => $value) {
					$out .= "'$field': ";
	
					// Weed out missing values, replace with javascript null object
					if ($value !== NULL)
						$out .= "'$value'";
					else
						$out .= 'null';
					$out .= ($colCount++ < $subSize) ? ', ' : '';
				}
				$out .= ' }';
				$out .= ($i < $size) ? ",\n" : "\n";
	
				// Output array object string
				echo $tab;
				if ($isfirst && $islast)
					echo '    ';
				else
					echo '        ';
				echo $out;
			}
		}
	
		// Close out variable definition
		if ($isfirst && $islast) {
			echo $tab;
			if ($isAssoc)
				echo "};\n";
			else
				echo "];\n";
		} else {
			echo $tab . "    ";
			if ($isAssoc)
				echo "}";
			else
				echo "]";

			if ($islast) {
				echo ";\n";
			} else {
				echo ",\n";
			}
		}
	}
	
	function embedConst($tab, $name, $value) {
		// Define variable
		echo $tab . "var $name = '$value';\n";
	}

	protected function importPageTemplate($template, $initScript) {
		include($template);
		echo "<script type='text/javascript' src='scripts/jquery-2.1.0.js'></script>\n";
		echo "<script type='text/javascript' src='scripts/$initScript'></script>\n";
	}
	
	abstract public function buildPanel();
}

abstract class DataLink {
	private $dbc;
	private $queryList;
	
	// Initializes the base DataLink object
	protected function init($queryList) {
		$this->dbc = DBConnect();
		$this->queryList = $queryList;
	}
	
	// Executes query set against MySQL and returns the result sets
	public function executeQueries() {
		$results = array();
		foreach ($this->queryList as $key => $query)
			$results[$key] = $this->dbc->query($query);
		return $results;
	}
	
	// Retrieves data for the given result metadata
	public function retrieveData($result) {
		$results = array();
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			array_push($results, $row);
		}
		return $results;
	}
	
	public function __destruct() {
		// Close database connection
		$this->dbc->close();
	}
}

// Control panel selection mapping
$controlMap = [
	'sel-Players' => 'PlayersCtrlObjs.php',
	'sel-Coaches' => 'CoachesCtrlObjs.php',
	'sel-Referees' => 'RefereesCtrlObjs.php',
	'sel-Schedule' => 'ScheduleCtrlObjs.php' 
];

/* If selection indicated (should always be true by default) then
 * retrieve and activate corresponding view control object
 */
if (isset($_POST['sub-selected'])) {
	include($controlMap[ $_POST['sub-selected'] ]);
	$controller = getTaskPanelController();
	$controller->buildPanel();
}

?>