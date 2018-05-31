<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
	<title>Games Schedule</title>
	<link rel='stylesheet' type='text/css' href='ScheduleTable.css' />
</head>
<body>
	<?php
		// Connect to localhost
        require('DBConnect.php');
		$dbconnect = DBConnect();

		// Define query
		$query  = "SELECT";
        $query .= " date, time, location,";
		$query .= " HTeam.name AS HomeTeam, home_score,";
		$query .= " ATeam.name AS AwayTeam, away_score,";
		$query .= " CONCAT(first_name, ' ', last_name) AS referee ";
		$query .= "FROM";
		$query .= " Schedule, Team AS HTeam, Team AS ATeam, Referee ";
		$query .= "WHERE";
		$query .= " HTeam.TID = home_TID AND";
		$query .= " ATeam.TID = away_TID AND";
		$query .= " Schedule.RID = Referee.RID ";
		$query .= "ORDER BY";
		$query .= " date, time;";

		// Perform query
		$gameDataSet = mysqli_query($dbconnect, $query);
	?>
	<div id='schedule-table' class='table-viewport' style='display: table;'>
		<!-- Schedule table header -->
		<div style='display: table-row; background-color: grey'>
			<div class='column-header' style='display: table-cell;'>Date</div>
			<div class='column-header' style='display: table-cell;'>Time</div>
			<div class='column-header' style='display: table-cell;'>Location</div>			
			<div class='column-header' style='display: table-cell;'>Home Team</div>
			<div class='column-header' style='display: table-cell;'>Score</div>
			<div class='column-header' style='display: table-cell;'>Away Team</div>
			<div class='column-header' style='display: table-cell;'>Score</div>
			<div class='column-header' style='display: table-cell;'>Referee</div>
		</div>
		<?php
			$alt = 0;
			while ($row = mysqli_fetch_row($gameDataSet)) {
                // Ternary conditional tests $alt, if even then row bg color is default, else hilighted (alternate)
				echo "<div style='display: table-row;'" . (($alt % 2 == 0) ? ">\n" : " class='row-alt'>\n");
                foreach ($row as $col) {
                    echo "<div class='cell-common' style='display: table-cell;'>$col</div>\n";
                }
				echo "</div>";
                $alt += 1;
			}

			// Clean up
			mysqli_free_result($gameDataSet);
			mysqli_close($dbconnect);
		?>
	</div>
</body>
</html>