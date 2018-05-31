<div id="logout">
<!--     Logged in as: <span style="color: blue"><?php //echo $_SESSION['user']; ?></span>  -->
	Logged in as: <span style="color: blue">Admin</span>
    <br />
    <?php

//     if($_SESSION['level'] == '3')
    	echo '<input type="button" id="admin-button" value="Administration" />';
    
    ?>
    <input type="button" id="logout-button" value="Log Out" disabled />
</div>
<script type="text/javascript" src="scripts/jquery-2.1.0.js"></script>
<script type="text/javascript" src="scripts/Logout.js"></script>