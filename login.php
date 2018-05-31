<html>
<head>
    <title>Logging In...</title> 
   
    <?php include("header.php"); ?>
    <div id="main">
	<br /><br /><br /><br />
	<?php 
		$user = $_POST['username'];
		$pass = $_POST['password'];
		// Connect to database
                $dbc = DBConnect();

		// Query DB for teams
		$user_query = "SELECT * FROM User WHERE user_name = '$user' AND password = '$pass'";
                $user_result = $dbc->query($user_query);
		$row = mysqli_fetch_array($user_result, MYSQLI_ASSOC);

		if($row) {
			echo '<h1>Welcome ' . $user . '!</h1>'; 
			echo '<h4>Redirecting you in 3 seconds...</h4>';
			$url = "index.php";
			if($row['level'] > 2)
				$url = 'AdminShell.php';

			echo '<meta http-equiv="refresh" content="3;url='.$url.'" />';	
		}
		else {
			$url = "index.php";
			echo '<h1>LOGIN FAILED FOR USER "'.$user.'"';
			echo '<h4>Redirecting you in 3 seconds...</h4>';
			echo '<meta http-equiv="refresh" content="3;url='.$url.'" />';
		}

    	?>
    </div>

    <?php include("footer.php"); ?>