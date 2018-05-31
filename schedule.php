<!--
	Written by: Adam Marshall
-->
<html>
<head>
    <title>TBD Schedule</title> 
   
    <?php include("header.php"); ?>

	<div id="main">
		<br><br><br>
		<?PHP
			//Connects to database
			$dbc = DBConnect();
			
			$sort = (isset($_GET['sort']) ) ? $_GET['sort'] : 'gm';
			
			switch ($sort)
			{
				case 'rf' :
					$table = 'Referee';
					$key1 = 'RID';
					$key2 = 'RID';
					$order_by = 'last_name';
					break;
				case 'ht' :
					$table = 'Team';
					$key1 = 'home_TID';
					$key2 = 'TID';
					$order_by = 'name';
					break;
				case 'at' :
					$table = 'Team';
					$key1 = 'away_TID';
					$key2 = 'TID';
					$order_by = 'name';
					break;
				case 'dt' :
					$table = 'Schedule';
					$key1 = 'date';
					$key2 = 'date';
					$order_by = 'date';
					break;
				case 'tm' :
					$table = 'Schedule';
					$key1 = 'time';
					$key2 = 'time';
					$order_by = 'time';
					break;
				case 'ct' :
					$table = 'Schedule';
					$key1 = 'location';
					$key2 = 'location';
					$order_by = 'location';
					break;
				default :
					$table = 'Schedule';
					$key1 = 'GID';
					$key2 = 'GID';
					$order_by = 'GID';
					$sort = 'gm';
					break;
			}
			
			//Query for Schedule table
			$qGame = "SELECT * FROM Schedule s JOIN $table x ON s.$key1 = x.$key2 ORDER BY x.$order_by";
			
			//Runs query
			$rGame = $dbc->query($qGame);
			
			
			//Establishes table with headers
			echo "<div class='wrap'><table class='bordered' cellspacing='3' cellpadding='3' width='80%' border='1'>
			<tr>
			<th id='cellmid'><a href='schedule.php?sort=gm'>Game</a></th>
			<th><a href='schedule.php?sort=rf'>Referee</a></th>
			<th><a href='schedule.php?sort=ht'>Home Team</a></th>
			<th><a href='schedule.php?sort=at'>Away Team</a></th>
			<th><a href='schedule.php?sort=dt'>Date</a></th>
			<th><a href='schedule.php?sort=tm'>Time</a></th>
			<th><a href='schedule.php?sort=ct'>Court</a></th>
			<th>Home Score</th>
			<th>Away Score</th>
			</tr></table>";

			echo "<div class='inner_table'>
			      <table class='bordered' cellspacing='3' cellpadding='3' width='80%' border='1'>";

			//Gathers data from query
			while($row = mysqli_fetch_array($rGame))
			{

				$TID = $row['home_TID'];
				$qTeam = "SELECT * FROM Team WHERE TID = '$TID'";
				$rTeam = $dbc->query($qTeam);
				$row2 = mysqli_fetch_array($rTeam);
				
				mysqli_free_result($rTeam);

				$TID = $row['away_TID'];
				$qTeam = "SELECT * FROM Team WHERE TID = '$TID'";
				$rTeam = $dbc->query($qTeam);
				$row3 = mysqli_fetch_array($rTeam);

				mysqli_free_result($rTeam);

				$RID = $row['RID'];
				$qRef = "SELECT * FROM Referee WHERE RID = '$RID'";
				$rRef = $dbc->query($qRef);
				$row4 = mysqli_fetch_array($rRef);

				mysqli_free_result($rRef);
				//Displays data in table
				echo "<tr>";
				echo "<td id='cellmid'>" . substr($row['GID'],1) . "</td>";
				echo "<td align='center'>&nbsp" . $row4['first_name'][0] . ". " . $row4['last_name'] . "</td>";
				echo "<td align='center'>&nbsp" . $row2['name'] . "</td>";
				echo "<td align='center'>&nbsp&nbsp" . $row3['name'] . "</td>";
				echo "<td align='center'>&nbsp&nbsp" . $row['date'] . "</td>";
				echo "<td align='center'>&nbsp&nbsp&nbsp" . $row['time'] . "</td>";
				echo "<td align='center'>&nbsp&nbsp&nbsp&nbsp" . $row['location'] . "</td>";
				echo "<td id='cellmid'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp" . $row['home_score'] . "</td>";
				echo "<td id='cellmid'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp" . $row['away_score'] . "</td>";
				echo "</tr>";
			}
			echo "</table></div></div>";
			
			//Releases resources
			mysqli_free_result($rGame);
		?>

	</div>
	
    <?php include("footer.php"); ?>