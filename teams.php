<html>
<head>
    <title>TBD Schedule</title> 
   
    <?php

    include("header.php");
    include("TeamsLogic.php");
    
    $vc = getTeamViewController(isset($_POST['sub-team-id']));
    $vc->buildPage();
        
    include("footer.php");
    
    ?>