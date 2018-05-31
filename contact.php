<html>
<head>
    <title>TBD Contact Us</title> 
   
    <?php include("header.php"); ?>

	<div id="main">
		<br />
		<?PHP
			$dbc = DBConnect();

			echo "<a class='contacts' href='contact.php?list=coach'>Coaches</a>" . "  | ";
			echo "<a class='contacts' href='contact.php?list=parents'>Parents</a>" . "  | ";
			echo "<a class='contacts' href='contact.php?list=refs'>Referees</a>";

			echo "<br />";
			echo "<br />";

			$list = (isset($_GET['list']) ) ? $_GET['list'] : 'default';

			switch ($list)
			{
				case 'coach' : 
					
					$q = 'SELECT  * FROM Coach ORDER BY last_name';
					$r = $dbc -> query($q);
			
					echo "<div class='wrap'><table class='bordered' align='center' cellspacing='3' cellpadding='3' width='80%' border='1'>
					<tr>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Phone</th>
					<th>email</th>
					<th>TID</th>	
					</tr></table>";

					echo "<div class='inner_table'>
			      		<table class='bordered' cellspacing='3' cellpadding='3' width='80%' border='1'>";
				
					while($row = mysqli_fetch_array($r))
					{	
						$TID = $row['TID'];
						$q = "SELECT * FROM Team WHERE TID = '$TID'";
						$rTeam = $dbc -> query($q);		
						$row2 = mysqli_fetch_array($rTeam);
						mysqli_free_result($rTeam);			
						
						echo "<tr>";
						echo "<td align='center'>" . $row['first_name'] . "</td>";
						echo "<td align='center'>&nbsp" . $row['last_name'] . "</td>";
						echo "<td align='center'>&nbsp&nbsp" . $row['phone'] . "</td>";
						echo "<td align='center'>&nbsp&nbsp&nbsp" . $row['email'] . "</td>";
						echo "<td align='center'>&nbsp&nbsp&nbsp&nbsp" . $row2['name'] . "</td>";
						echo "</tr>";
					}
					echo "</table></div></div>";
			
					mysqli_free_result($r);

					break;
	
				case 'parents' :
							
					$q = 'SELECT  * FROM Parent ORDER BY last_name';
					$r = $dbc -> query($q);
					
					echo "<div class='wrap'><table class='bordered' align='center' cellspacing='3' cellpadding='3' width='80%' border='1'>
					<tr>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Phone</th>
					<th>email</th>
					<th>Player</th>
					</tr></table>";

					echo "<div class='inner_table'>
			      		<table class='bordered' cellspacing='3' cellpadding='3' width='80%' border='1'>";

					while($row = mysqli_fetch_array($r))
					{	
						$PID = $row['PID'];
						$q = "SELECT * FROM Player WHERE PID = '$PID'";
						$rPlayer = $dbc -> query($q);		
						$row2 = mysqli_fetch_array($rPlayer);
						mysqli_free_result($rPlayer);
					
						echo "<tr>";
						echo "<td align='center'>" . $row['first_name'] . "</td>";
						echo "<td align='center'>&nbsp" . $row['last_name'] . "</td>";
						echo "<td align='center'>&nbsp&nbsp" . $row['phone'] . "</td>";
						echo "<td align='center'>&nbsp&nbsp&nbsp" . $row['email'] . "</td>";
						echo "<td align='center'>&nbsp&nbsp&nbsp&nbsp" . $row2['first_name'] . "</td>";
						echo "</tr>";
					}
					echo "</table></div></div>";
		
					mysqli_free_result($r);

					break;
		
				case 'refs' :

					$q = 'SELECT  * FROM Referee ORDER BY last_name';
					$r = $dbc -> query($q);

					echo "<div class='wrap'><table class='bordered' align='center' cellspacing='3' cellpadding='3' width='80%' border='1'>
					<tr>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Phone</th>
					<th>email</th>
					</tr></table>";

					echo "<div class='inner_table'>
			     		<table class='bordered' cellspacing='3' cellpadding='3' width='80%' border='1'>";

					while($row = mysqli_fetch_array($r))
					{
						echo "<tr>";
						echo "<td align='center'>" . $row['first_name'] . "</td>";
						echo "<td align='center'>" . $row['last_name'] . "</td>";
						echo "<td align='center'>" . $row['phone'] . "</td>";
						echo "<td align='center'>" . $row['email'] . "</td>";
						echo "</tr>";
					}
					echo "</table></div></div>";
			
					mysqli_free_result($r);
					break;

				default :
					break;
			}//end switch
	
	echo "</div>";
	
    include("footer.php"); ?>