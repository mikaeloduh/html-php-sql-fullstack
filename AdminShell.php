<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>TB Park District Administration</title>
	<link rel="stylesheet" href="themes/Administration.css" />
	<script src="scripts/jquery-2.1.0.js"></script>
	<script src="scripts/PHPProxy.js"></script>
	<?php
	
	if (!isset($_POST['sub-selected']))
		$_POST['sub-selected'] = 'sel-Players';
	
	echo "<script>\n";
	echo "	  var selected = '" . $_POST['sub-selected'] . "';\n";
	echo "</script>\n";
	
	?>
</head>
<body>
	<div id="content-frame">
		<!-- Administrative page header area -->
		<?php include('Admin-header.php'); ?>
		<div style="margin-top: 1em;">
			<div style="float: left;">
				<!-- Task selection panel -->
				<div style="width: 185px;">
					<ul class="selection-panel">
						<li id="sel-Players" class="selection-container">Players</li>
						<li id="sel-Coaches" class="selection-container">Coaches</li>
						<li id="sel-Referees" class="selection-container">Referees</li>
						<li id="sel-Schedule" style="border-bottom: none;" class="selection-container">Games Schedule</li>
					</ul>
				</div>
				<div class="selection-panel-lift"></div>
			</div>
			<!-- Task area -->
			<div class="task-area-panel" style="display: table-cell;">
				<div id="task-console"></div>
				<?php
				
				include('DBConnect.php');
				include('AdminLogic.php');
				
				?>
			</div>
		</div>
	</div>
	<script src="scripts/AdminShell.js"></script>
	<div id="divlogout">
		<form action="index.php">
			<input id="logout" type="submit" value="Log Out" />
		</form>
	</div>
</body>
</html>