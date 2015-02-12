<?php

switch($_GET['action']) {

    case 'read':
        $file = $_GET['url'];
        $fp = fopen($file, 'r');
        echo file_get_contents($file);
        fclose($fp);
        break;

    case 'log':
        $filename = $_GET['f'];
        $errors = $_GET['e'];
        $warnings  = $_GET['w'];
        include_once('../dm_functions.php');
        logResults($filename, $errors, $warnings, '../CodeSniffer/Logs');
        break;

}
?>