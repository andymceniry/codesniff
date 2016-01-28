<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('max_execution_time', 300);
include_once('dm_functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>WebCodeSniffer</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="shortcut icon" href="wcs_images/favicon.ico" />
<link rel="stylesheet" href="wcs_styles.css" type="text/css" />
<link rel="stylesheet" href="dm_styles.css" type="text/css" />
<?php
if (isset($_GET['filetosniff']) AND $_GET['filetosniff'] !='' AND isset($_GET['update']) AND $_GET['update'] !='') {
    $_GET['update'] = intval($_GET['update']) < 30 ? 30 : $_GET['update'];
    echo '<meta http-equiv="refresh" content="'.$_GET['update'].'">';
}
?>
</head>

<body>


<?php
if (isset($_GET['dir'])) {
    if ($_GET['dir'] == 'previous') {
        $dir = dirname($_GET['path']);    
    } elseif ($_GET['dir'] == 'current') {
        $dir = $_GET['path'];    
    } elseif ($_GET['dir'] == 'next') {    
        $dir = $_GET['path'] . '/' . $_GET['dir_name'];
    }
} else {
    $dir = dirname(getcwd());
}


if (isset($_GET['filetosniff']) AND $_GET['filetosniff'] !='') {
    echo '<div class="infopath clearfix"><p>' . str_replace('\\', '/', $dir).'/'.$_GET['filetosniff'].'</p>';
        ?>
        <form action="<?php echo basename(__FILE__); ?>" method="get" class="header-back-btn">
        <input type="hidden" name="path" value="<?php echo $dir; ?>" />    
        <input type="hidden" name="dir" value="current" />
        <input type="image" src="wcs_images/back.png" class="submit_back" />
        </form>
    </div>

    <?php
    
    $_SERVER['argc'] = 3;
    $standard = '--standard=' . $_GET['standard'];
    if(array_key_exists('sniff_folder_summary', $_GET) AND $_GET['sniff_folder_summary'] == 'Y') {
        $standard = '--report=summary';
    }
    $url = $_GET['path'] . '/' . $_GET['filetosniff'];
    $_SERVER['argv'] = array("phpcs.php",$standard,$url);
    
    echo '<div class="report"><pre>';
    if (pathinfo($url, PATHINFO_EXTENSION) == 'js') {
        includeJslintFiles($url);
        die();
    } else {
        include 'phpcs.php';
    }
    

    exit;
}

if (is_dir($dir) AND $handle = opendir($dir)) {
echo '<div class="infopath clearfix"><p>' . str_replace('\\', '/', $dir).'</p>';
    if ($dir != dirname(getcwd())) {
        ?>
        <form action="<?php echo basename(__FILE__); ?>" method="get" class="header-back-btn">
        <input type="hidden" name="path" value="<?php echo $dir; ?>" />    
        <input type="hidden" name="dir" value="previous" />
        <input type="image" src="wcs_images/back.png" class="submit_back" />
        </form>
        <?php
    }
    echo '</div>';

    echo '<div class="entry_row_holder">';

    $extensionstosniff = array('php','css', 'js');
    $typepicture = array('bmp','gif','png','jpg');
    
    $folders = array();
    $files = array();
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && $entry != "webcodesniffer") {
            if (is_dir($dir."/".$entry) === true) {
                    $folders[] = $entry;
                } else {
                    if (in_array(pathinfo($dir."/".$entry, PATHINFO_EXTENSION), $extensionstosniff)) {
                        $files[] = $entry;
                    }
            }
        }
    }


    sort($folders);
    foreach($folders as $entry) {
        ?>
        <div class='entry_row_dir'>
            <input type="hidden" name="dir" value="next" />
            <a class="folder_link" href="?path=<?php echo $dir;?>&dir=next&dir_name=<?php echo $entry; ?>"/><?php echo $entry; ?></a>
        </div>
        <?php 
    }


    sort($files);
    foreach($files as $entry) {
            ?>
            <div class='entry_row_filetosniff'>
                <div class='entry_name'><a class="file_link" href="?path=<?php echo $dir;?>&standard=DM&sniff=TEST&dir=current&filetosniff=<?php echo $entry; ?>&update=30"/><?php echo $entry; ?></a></div>
                <div class="entry_history">
                <?php
                $filename = $dir.'/'.$entry;
                $file_last_change = date("F d Y H:i:s.", filemtime($filename));
                $log_filename = getLogFilename($filename);
                $log_filename = 'Codesniffer/Logs/'.urlencode($log_filename);

                if (file_exists($log_filename)) {
                    $fp = fopen($log_filename, 'r');
                    $fp_content = array();
                    while(! feof($fp)) {
                        $fp_content[] = fgets($fp);
                    }
                    fclose($fp);
                    if (date('U', strtotime($fp_content[0])) != date('U', strtotime($file_last_change))) {
                        echo '<span class="out-of-date">out of date</span>';
                    } else {
                        if ($fp_content[1] == 0 AND $fp_content[2] == 0) {
                            echo '<span class="all-good">all good</span>';
                        } else {
                            echo '<span class="warning">'.$fp_content[2].'</span>';
                            echo '<span class="error">'.$fp_content[1].'</span>';
                        }
                    }
                } else {
                    echo '<span class="not-tested">not tested</span>';
                }

                ?>
                </div>
                <br style='clear:both;' />
            </div>
            <?php
    }

    if (count($folders) < 1 AND count($files) < 1) {
        echo '<p><b> &nbsp; &nbsp; &nbsp; no matching files or folders found.</b></p>';
    }

    echo '</div>';
} else {
    echo "<p>Invalid Directory '$dir'</p>";
    echo "<p>Redirecting...</p>";
}
?>

</body>
</html>