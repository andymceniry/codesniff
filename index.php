<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('max_execution_time', 300);
include_once 'dm_functions.php';
if (file_exists('env.php')) {
    include 'env.php';
}

if (isset($env) AND array_key_exists('default_url', $env) AND $_SERVER['QUERY_STRING'] === '') {
    header('location: ' . $env['default_url']);
    die();
}

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
if (isset($_GET['filetosniff']) AND $_GET['filetosniff'] != '' AND isset($_GET['update']) AND $_GET['update'] != '') {
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


if (isset($_GET['filetosniff']) AND $_GET['filetosniff'] != '') {

    //  check for open file
    if (array_key_exists('view', $_GET) AND $_GET['view'] != '') {
        $aTmp = explode('&view=', 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $return_url = $aTmp[0];
        exec(urldecode($_GET['view']));
        header("location: $return_url");
        die();
    }
    $file_anchor = str_replace('\\', '/', str_replace('//', '/', $dir)).'/'.$_GET['filetosniff'];
    $file_link = "explorer+" . urlencode(str_replace('/', '\\', str_replace('//', '/', $dir . '\\' . $_GET['filetosniff'])));

    echo '<div class="infopath clearfix"><p><a href="http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'&view='.$file_link.'">' . $file_anchor .'</a></p>';
        ?>
        <form action="<?php echo basename(__FILE__); ?>" method="get" class="header-back-btn">
        <input type="hidden" name="path" value="<?php echo $dir; ?>" />
        <input type="hidden" name="dir" value="current" />
        <?php echo isset($_GET['showhash']) ? '<input type="hidden" name="showhash" value="" />' : ''; ?>
        <?php echo isset($_GET['filter']) ? '<input type="hidden" name="filter" value="'.$_GET['filter'].'" />' : ''; ?>
        <input type="image" src="wcs_images/back.png" class="submit_back" />
        </form>
    </div>

    <?php

    $_SERVER['argc'] = 3;
    $standard = '--standard=' . $_GET['standard'];
    if (array_key_exists('sniff_folder_summary', $_GET) AND $_GET['sniff_folder_summary'] == 'Y') {
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
echo '<div class="infopath clearfix"><p>' . str_replace('//', '/', str_replace('\\', '/', $dir)).'</p>';
    if ($dir != dirname(getcwd())) {
        ?>
        <form action="<?php echo basename(__FILE__); ?>" method="get" class="header-back-btn">
        <input type="hidden" name="path" value="<?php echo $dir; ?>" />
        <input type="hidden" name="dir" value="previous" />
        <?php echo isset($_GET['showhash']) ? '<input type="hidden" name="showhash" value="" />' : ''; ?>
        <?php echo isset($_GET['filter']) ? '<input type="hidden" name="filter" value="'.$_GET['filter'].'" />' : ''; ?>
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
                    if (substr($entry, -6) !== 'min.js' AND substr($entry, -7) !== 'min.css') {
                        $files[] = $entry;
                    }
                }
            }
        }
    }

    sort($folders);
    foreach($folders as $entry) {
        ?>
        <div class='entry_row_dir'>
            <input type="hidden" name="dir" value="next" />
            <?php echo isset($_GET['showhash']) ? '<input type="hidden" name="showhash" value="" />' : ''; ?>
            <?php echo isset($_GET['filter']) ? '<input type="hidden" name="filter" value="'.$_GET['filter'].'" />' : ''; ?>
            <a class="folder_link" href="?path=<?php echo $dir;?>&dir=next&dir_name=<?php echo $entry; ?><?php echo isset($_GET['showhash']) ? '&showhash' : ''; ?><?php echo isset($_GET['filter']) ? '&filter='.$_GET['filter'] : ''; ?>"/><?php echo $entry; ?></a>
        </div>
        <?php
    }

    $hashOfFileHashes = '';
    sort($files);
    foreach($files as $entry) {
        if(isset($_GET['filter']) AND strpos($entry, $_GET['filter']) === false) {
            continue;
        }
        $filename = $dir.'/'.$entry;
        $file_last_change = date("F d Y H:i:s.", filemtime($filename));
        ?>
        <div class='entry_row_filetosniff'>
            <div class='entry_name'>
                <a class="file_link" href="?path=<?php echo $dir;?>&standard=DM&sniff=TEST&dir=current&filetosniff=<?php echo $entry; ?><?php echo isset($_GET['showhash']) ? '&showhash' : ''; ?><?php echo isset($_GET['filter']) ? '&filter='.$_GET['filter'] : ''; ?>&update=30"/>
                    <?php
                    if(isset($_GET['showhash'])) {
                        $hash = substr(md5(file_get_contents($filename)), 0, 8);
                        echo '<span class="hash" title="edited: '.$file_last_change.'">' . $hash . '</span>';
                        $hashOfFileHashes .= $hash;
                    }
                    ?>
                    <?php echo $entry; ?>
                </a>
            </div>
            <div class="entry_history">
            <?php
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
    } else {
        if ($countGood === count($files)) {
            updateIcon('good');
        }
        if ($countBad > 0) {
            updateIcon('bad');
        }
    }

    echo '</div>';

    if(isset($_GET['showhash'])) {
        echo '<p class="hash allfileshash">'.md5($hashOfFileHashes).'</p>';
    }

} else {
    echo "<p>Invalid Directory '$dir'</p>";
    echo "<p>Redirecting...</p>";
}
?>

</body>
</html>