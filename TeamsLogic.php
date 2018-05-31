<?php

abstract class ITeamViewController
{
    protected $dbc;
    protected $teams;
    
    abstract public function buildPage();
    
    public function getDbc() {
        return $this->dbc;
    }
    
    public function getTeams() {
        return $this->teams;
    }
    
    protected function initBaseMembers() {
        // Initialize database connection and get teams
        $this->dbc = DBConnect();
        $getTeams = "SELECT * FROM Team;";
        $result = $this->dbc->query($getTeams);
        $this->teams = array();
        while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            $this->teams[$row['TID']] = $row['name'];
        }
        
        // Clean up
        mysqli_free_result($result);
    }
    
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
        if ($isfirst) {
            echo $tab . "var "; // If first in list then start var list
        } else {
            echo $tab . '    '; // Otherwise indent to keep aligned with rest of variable list
        }
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
        }
        else {
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
        echo "<div style='width: 100px;' class='team-content-main'>\n";
        include($template);
        echo "</div>\n";
        echo "<script type='text/javascript' src='scripts/jquery-2.1.0.js'></script>\n";
        echo "<script type='text/javascript' src='scripts/$initScript'></script>\n";
    }
    
    function __destruct() {
        // Note: Objects are always destroyed after page is served
        // Close connection
        $this->dbc->close();
    }
}

class TVCDefault extends ITeamViewController
{
    function __construct() {
        $this->initBaseMembers();
    }
    
    public function buildPage() {
        // Set main stylesheet
        $this->setCSS('Teams.css');
        
        /* TODO: Change all of this to use base class' teams list, dataset embed function */
        
        // Query database for teams data
        $query = "SELECT * FROM Team;";
        $result = $this->dbc->query($query);

        // Process result set and build javascript teams array
        echo "<script type='text/javascript'>\n";
        echo "  var teams = {\n";
        for ($i = 1; $i <= $result->num_rows; $i++) {
            $team = mysqli_fetch_array($result, MYSQL_ASSOC);
            $out  = '\'' . $team['TID'] . '\': \'' . $team['name'] . '\'';
            $out .= ($i < $result->num_rows) ? ",\n" : "\n";
            echo '        ' . $out;
        }
        echo "  };\n";
        mysqli_free_result($result);
        
        // If team brief requested, build data array for javascript page builder
        if (isset($_POST['sub-sel-team-id'])) {
            $query  = 'SELECT';
            $query .= ' home_court, division, founded ';
            $query .= 'FROM';
            $query .= ' Team ';
            $query .= 'WHERE';
            $query .= ' Team.TID = \'' . $_POST['sub-sel-team-id'] . '\';';
            $result = $this->dbc->query($query);
            
            // Process result set and build javascript team data array
            echo "  var teamData = {\n";
            $out = '';
            $teamData = mysqli_fetch_array($result, MYSQL_ASSOC);
            $colCount = 1;
            foreach ($teamData as $field => $value) {
                $out .= "'$field': '$value'";
                $out .= ($colCount++ < $result->field_count) ? ', ' : '';
            }
            echo "        $out\n";
            echo "      },\n";
            mysqli_free_result($result);
            
            // Get coach data
            $query  = 'SELECT';
            $query .= ' CONCAT(first_name, \' \', last_name) AS coach_name, email ';
            $query .= 'FROM';
            $query .= ' Team, Coach ';
            $query .= 'WHERE';
            $query .= ' Team.TID = \'' . $_POST['sub-sel-team-id'] . '\' AND';
            $query .= ' Coach.TID = Team.TID;';
            $result = $this->dbc->query($query);
            
            // Process result set and build javascript coaches array
            echo "      coaches = [\n";
            for ($i = 1; $i <= $result->num_rows; $i++) {
                $coach = mysqli_fetch_array($result, MYSQL_ASSOC);
                $out = '{ ';
                $colCount = 1;
                foreach ($coach as $field => $value) {
                    $out .= "'$field': '$value'";
                    $out .= ($colCount++ < $result->field_count) ? ', ' : '';
                }
                $out .= ' }';
                $out .= ($i < $result->num_rows) ? ",\n" : "\n";
                echo "        $out";
            }
            echo "      ],\n";
            mysqli_free_result($result);
            
            // Indicate selected page to client script
            echo "      selected = '" . $_POST['sub-sel-team-id'] . "',\n";
            
            // Query database for scores data
            $query =  'SELECT';
            $query .= ' home_TID, HTeam.name AS home_team, home_score,';
            $query .= ' away_TID, ATeam.name AS away_team, away_score,';
            $query .= ' date ';
            $query .= 'FROM';
            $query .= ' Schedule, Team AS HTeam, Team AS ATeam ';
            $query .= 'WHERE';
            $query .= ' (home_TID=\'' . $_POST['sub-sel-team-id'] . '\' OR';
            $query .= ' away_TID=\'' . $_POST['sub-sel-team-id'] . '\') AND';
            $query .= ' home_TID = HTeam.TID AND away_TID = ATeam.TID;';
            $result = $this->dbc->query($query);
            
            // Process result set and build javascript game stats array
            $stats = array();
            while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            	array_push($stats, $row);
            }
            $this->embedDataSet('    ', 'gameStats', $stats, FALSE, TRUE);
            mysqli_free_result($result);            
        }
        echo "</script>\n";
        
        // Import default page template and insert into html stream
        $this->importPageTemplate('TeamsDefaultTemplate.php', 'TeamsInitDefault.js');
    }    
}

class TVCTeam extends ITeamViewController
{
    private $team;
    private $teamId;
    
    function __construct($teamId) {
        $this->initBaseMembers();
        $this->team = array($teamId => $this->teams[$teamId]);
        $this->teamId = $teamId;
    }
    
    public function buildPage() {
        // Set main CSS
        $this->setCSS('Teams.css');
        
        echo "<script type='text/javascript'>\n"; // Begin html script tag
        
        // Embed team id, name
        $team = [ 'team_id' => $this->teamId, 'name' => $this->team[$this->teamId] ];
        $this->embedDataSet('    ', 'team', $team, TRUE, FALSE);
        
        // Query database for coach info
        $query  = 'SELECT';
        $query .= ' first_name, last_name, phone, email ';
        $query .= 'FROM';
        $query .= ' Coach, Team ';
        $query .= 'WHERE';
        $query .= ' Coach.TID = Team.TID AND';
        $query .= " Team.TID = '$this->teamId';";
        $result = $this->dbc->query($query);
        
        // Build dataset and embed in html stream
        $coaches = array();
        while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            array_push($coaches, $row);
        }
        $this->embedDataSet('    ', 'coaches', $coaches, FALSE, FALSE);
        mysqli_free_result($result);
        
        // Query database for team practice times
        $query  = 'SELECT';
        $query .= ' pr_day, pr_time ';
        $query .= 'FROM';
        $query .= ' Practice, Team ';
        $query .= 'WHERE';
        $query .= ' Practice.TID = Team.TID AND';
        $query .= " Team.TID = '$this->teamId';";
        $result = $this->dbc->query($query);
        
        // Build dataset and embed in html stream
        $practice = array();
        while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            array_push($practice, $row);
        }
        $this->embedDataSet('    ', 'practice', $practice, FALSE, FALSE);
        mysqli_free_result($result);
        
        // Query database for announcements
        $query  = 'SELECT';
        $query .= ' ann_time, message ';
        $query .= 'FROM';
        $query .= ' Announcements, Team ';
        $query .= 'WHERE';
        $query .= ' Announcements.TID = Team.TID AND';
        $query .= " Team.TID = '$this->teamId' ";
        $query .= 'ORDER BY';
        $query .= ' ann_time DESC';
        $result = $this->dbc->query($query);
        
        // Build dataset and embed in html stream
        $announcements = array();
        while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            array_push($announcements, $row);
        }
        $this->embedDataSet('    ', 'announcements', $announcements, FALSE, FALSE);
        mysqli_free_result($result);
        
        // Query database for players
        $query  = 'SELECT';
        $query .= ' CONCAT(Player.first_name, \' \', Player.last_name) AS player_name, number,';
        $query .= ' CONCAT(Parent.first_name, \' \', Parent.last_name) AS parent_name, phone ';
        $query .= 'FROM';
        $query .= ' Player, Parent ';
        $query .= 'WHERE';
        $query .= " TID = '$this->teamId' AND Player.PID = Parent.PID ";
        $query .= 'ORDER BY';
        $query .= ' number;';
        $result = $this->dbc->query($query);
        
        // Build dataset and embed in html stream
        $players = array();
        while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        	array_push($players, $row);
        }
        $this->embedDataSet('    ', 'players', $players, FALSE, TRUE);
        mysqli_free_result($result);
        
        echo "</script>\n";     // End html script tag
        
        // Import team view home template and insert into html stream
        $this->importPageTemplate('TeamViewHomeTemplate.php', 'TeamViewHomeInit.js');
    }    
}

function getTeamViewController($notDefaultView) {
    if ($notDefaultView) {
        return new TVCTeam($_POST['sub-team-id']);
    } else {
        return new TVCDefault();
    }
}

?>