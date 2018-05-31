<?php

function DBConnect() {
    $dbconnect = @mysqli_connect('courses', 'cs566306', '5gPfMK2KV', 'cs566306') or die ('Could not connect to MySQL: '.mysqli_connect_error());
    mysqli_set_charset($dbconnect, 'utf-8');
    return $dbconnect;
}

?>