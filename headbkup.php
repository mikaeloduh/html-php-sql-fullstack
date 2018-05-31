    <link rel="stylesheet" href="main.css">
    <link href='http://fonts.googleapis.com/css?family=Iceland' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Audiowide' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="images\icon.ico">
    <script type="text/javascript" src="scripts/PHPProxy.js"></script>
    <script type="text/javascript" src="scripts/park.js"></script>
    <?php require('DBConnect.php'); ?>
</head>
<body>
    <div id="body">
        <div id="header">
            <form id="login">
                    <label for="username">Username:&nbsp</label>
                    <input id="username" type="text" /> 
                    <br />
                    <label id="pass" for="password">Password:&nbsp</label>
                    <input id="password" type="password" />
                <input id="round" class="subarrow" type="submit" value=">>">
            </form>
            <a href="index.php"><div id="logo"></div></a>
            <div id="links">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="schedule.php">Schedule</a></li>
                    <li><a href="standings.php">Standings</a></li>
		            <li>
                        <?php
                        
                        // Connect to database
                        $dbc = DBConnect();
                        
                        // Query DB for teams
                        $teams_query = 'SELECT * FROM Team;';
                        $teams = $dbc->query($teams_query);
                        
                        // Create teams link and drop down, populate with team names
                        if ($teams->num_rows) {
                            echo '<a href="teams.php" onmouseover="showTeams()" onmouseout="closeTeams()">Teams</a>'."\n";
                            echo '                        <div id="teams" onmouseover="showTeams()" onmouseout="closeTeams()">'."\n";
                            while ($team = mysqli_fetch_array($teams, MYSQLI_ASSOC)) {
                                echo '                            '
                                    . '<div class="dd-link" id="'.$team['TID']
                                    .'" onmouseover="ddLinkHilite(this);" onmouseout="ddLinkUnhilite(this);" onclick="ddLinkToPage(this);">'
                                    .$team['name'].'</div>'."\n";
                            }
                            echo "                        </div>\n";
                        } else {
                            echo '<a href="teams.php">Teams</a>'."\n";
                        }
                        
                        // Clean up and close connection
                        mysqli_free_result($teams);
                        $dbc->close();
                            
                        ?>
                    </li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <span></span>
            </div>
        </div>