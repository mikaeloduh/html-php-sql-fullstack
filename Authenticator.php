<?php

/*
 * Authentication script, queries the db for user, adds user id to $_SESSION
 * superglobal if user is registered, does nothing if not. Dependent scripts
 * can test if $_SESSION['user'] is set.
 * 
 * NOTE: This script depends on DBConnect.php. It is the calling page's
 * responsibility to include it *before* including Authenticator.php
 *
 * NOTE: To logout use session_destory()
 */

session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
	// Connect to the database
    $dbc = DBConnect();
    
    // Query for user id
    $auth_query  = 'SELECT * FROM User ';
    $auth_query .= 'WHERE';
    $auth_query .= ' user_name = "' . $_POST['username'] . '" AND';
    $auth_query .= ' password = "' . $_POST['password'] . '";';
    $result = $dbc->query($auth_query);
    
    // Confirm authentication and log user in
    if ($result->num_rows) {
    	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $_SESSION['user'] = $_POST['username'];
        $_SESSION['level'] = $row['level'];
    }
    
    // Clean up and close connection
    mysqli_free_result($result);
    $dbc->close();
} else if (isset($_POST['sub-logout'])) {
	unset($_SESSION['user']);
	unset($_SESSION['level']);
	session_destroy();
}

?>