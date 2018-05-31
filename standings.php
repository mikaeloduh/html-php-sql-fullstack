<html>
<head>
    <title>TBD Standings</title> 
   
    <?php include("header.php"); ?>

	<div id="main">
		<br /><br /><br /><br />
		<?PHP 
			$dbc = DBConnect();

			$teams = array();

                        $query_sched = 'SELECT home_TID AS TID FROM Schedule
					UNION
					SELECT away_TID AS TID FROM Schedule';
					
                        $sched = $dbc->query($query_sched);		

			while ($row = mysqli_fetch_array($sched, MYSQLI_ASSOC)) {
				$aTeam = array($row['TID'],0,0);
				$teams[] = $aTeam;
			}
			
			mysqli_free_result($sched);

		/***************************************************************************/			

			$query_sched = 'SELECT home_TID,away_TID,home_score,away_score
					FROM Schedule';
					
                        $sched = $dbc->query($query_sched);
			
			while ($row = mysqli_fetch_array($sched, MYSQLI_ASSOC)) {
				// set the home and away indices
				for($i = 0; $i < count($teams); $i++) {
					if($row['home_TID'] == $teams[$i][0])
						$home = $i;
					else if($row['away_TID'] == $teams[$i][0])
						$away = $i;
				}
				// add the wins and losses
				if($row['home_score'] > $row['away_score']) {
					$teams[$home][1]++;
					$teams[$away][2]++;
				}
				else {
					$teams[$home][2]++;
					$teams[$away][1]++;
				}
			}

			mysqli_free_result($sched);

		/***************************************************************************/

			for($i = 0; $i < count($teams)-1; $i++) {
				$max = $i;
				for($j = $i+1; $j < count($teams); $j++) {
					if($teams[$j][1] > $teams[$max][1])
						$max = $j;
				}
				if ($max != $i) {
					$tarray = array($teams[$i][0],$teams[$i][1],$teams[$i][2]);
					$teams[$i] = $teams[$max];
					$teams[$max] = $tarray;
				}
			}		

		/***************************************************************************/

			echo '<div class="wrap"><table id="norm" class="bordered" align="center" cellspacing="3" cellpadding="3" width="80%">
			<tr>
				<th id="cellmid">Team Name</th>
				<th id="cellmid">Wins</th>
				<th id="cellmid">Losses</th>
				<th id="cellmid">Winning %</th>
			</tr>';

			for($i = 0; $i < count($teams); $i++) {
				
				$wnpctg = $teams[$i][1] / ($teams[$i][1] + $teams[$i][2]);
				
				$TID = $teams[$i][0];

				$query_sched = "SELECT * FROM Team WHERE TID = '$TID'";
	                  	$sched = $dbc->query($query_sched);	
				$row = mysqli_fetch_array($sched, MYSQLI_ASSOC);
				
				echo '<tr>
					<td id="cellmid">' . $row['name'] . '</td>
					<td id="cellmid">' . $teams[$i][1] . '</td>
					<td id="cellmid">' . $teams[$i][2] . '</td>
					<td id="cellmid">' . number_format($wnpctg,3) . '</td>
 				</tr>';
			}

		echo '</table></div>';

		?>
	</div>
	
    <?php include("footer.php"); ?>