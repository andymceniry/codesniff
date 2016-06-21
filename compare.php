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
<script src="jquery-3.0.0.min.js"></script>

<style>
.infopath a { float: right; }
.entry_row_dir { width: 100%; }
.entry_row_dir .date { width:130px; float:left; }
.entry_row_dir .hash { width:70px; float:left; }
.entry_row_dir .folder_link { width:425px; float:left; }
.entry_row_dir .folder_link2 { width: 408px; float: left; text-align: right; font-weight: bold;  }
.entry_row_dir { background:none; padding-left:5px;}
.entry_row_dir .folder_link { background-image: url(wcs_images/folder_stand.png); background-repeat: no-repeat; background-position-x: left; padding-left: 25px; min-height: 20px; }
.entry_row_dir .folder_link2 { background-image: url(wcs_images/folder_stand.png); background-repeat: no-repeat; background-position-x: right; padding-right: 25px; min-height: 20px; }
.nobg  { background:none !important; }
.entry_name { width: 100%; }
.entry_name .date { width:130px; float:left; }
.entry_name .hash { width:70px; float:left; }
.entry_name .date2 { text-align:right; }
.entry_name .hash2 { text-align:right; }
.entry_name .file { width:425px; float:left; }
.missing { background-color:#ffc !important; }
.mismatch { background-color:#fcc !important; }
</style>
</head>

<body>


<?php
if (!isset($_GET['path'])) {
die('<pre>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
} else {
    $dir = $_GET['path'];
}

if (!isset($_GET['path2'])) {
die('<pre>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
} else {
    $dir2 = $_GET['path2'];
}



if (!is_dir($dir)) {
echo '<pre>'.var_export($dir, TRUE).'</pre>';
    die('<pre>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
}
$handle = opendir($dir);
if (!$handle) {
    die('<pre>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
}



if (!is_dir($dir2)) {
echo '<pre>'.var_export($dir2, TRUE).'</pre>';
    die('<pre>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
}
$handle2 = opendir($dir2);
if (!$handle2) {
    die('<pre>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
}


echo '<div class="infopath clearfix"><p>' . str_replace('//', '/', str_replace('\\', '/', $dir)).'</p>';
$linkParts = getQueryStringAsArray();
$linkParts['path'] = getParentDir($linkParts['path']);
unset($linkParts['compare']);
$link = makeQueryStringFromArray($linkParts);
echo '<a href="?'.$link.'"><input type="image" src="wcs_images/back.png" class="submit_back" /></a>';
echo '</div>';

echo '<div class="infopath clearfix"><p>' . str_replace('//', '/', str_replace('\\', '/', $dir2)).'</p>';
$linkParts = getQueryStringAsArray();
$linkParts['path2'] = getParentDir($linkParts['path2']);
unset($linkParts['compare']);
$link2 = makeQueryStringFromArray($linkParts);

    echo '<a href="?'.$link2.'"><input type="image" src="wcs_images/back.png" class="submit_back" /></a>';

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
                $files[] = $entry;
            }
        }
    }
    while (false !== ($entry = readdir($handle2))) {
        if ($entry != "." && $entry != ".." && $entry != "webcodesniffer") {
            if (is_dir($dir2."/".$entry) === true) {
                $folders[] = $entry;
            } else {
                $files[] = $entry;
            }
        }
    }
    $folders = array_unique($folders);
    $files = array_unique($files);

    sort($folders);
    foreach($folders as $entry) {

        $exists = is_dir($dir."/".$entry);
        $exists2 = is_dir($dir2."/".$entry);

        ?>
        <div class='entry_row_dir'>
            <input type="hidden" name="dir" value="next" />
            <?php echo isset($_GET['showhash']) ? '<input type="hidden" name="showhash" value="" />' : ''; ?>
            <?php echo isset($_GET['showdate']) ? '<input type="hidden" name="showdate" value="" />' : ''; ?>
            <?php echo isset($_GET['filter']) ? '<input type="hidden" name="filter" value="'.$_GET['filter'].'" />' : ''; ?>

<?php
if($exists) {
    $linkParts = getQueryStringAsArray();
    $linkParts['path'] .= '/' . $entry;
    unset($linkParts['compare']);
    $link = makeQueryStringFromArray($linkParts);
    echo '<a class="folder_link" href="?'.$link.'"/>'.$entry.'</a>';
} else {
    echo '<div class="folder_link nobg"> &nbsp; </div>';
}
if($exists2) {
    $linkParts = getQueryStringAsArray();
    $linkParts['path2'] .= '/' . $entry;
    unset($linkParts['compare']);
    $link = makeQueryStringFromArray($linkParts);
    echo '<a class="folder_link2" href="?'.$link.'"/>'.$entry.'</a>';
} else {
    echo '<div class="folder_link2 nobg"> &nbsp; </div>';
}
?>
        </div>
        <?php



    }


if (!isset($_GET['compare'])) {
    echo '<a href="?'.$_SERVER['QUERY_STRING'].'&compare">COMPARE</a>';
    #echo '<pre>'.var_export($_SERVER, TRUE).'</pre>';
    die();
}

    $hashOfFileHashes = '';
    sort($files);
    foreach($files as $entry) {       
        $filename = $dir.'/'.$entry;
        $filename2 = $dir2.'/'.$entry;

        $exists = file_exists($filename);
        if($exists) { 
            $file_last_change = date("F d Y H:i:s.", filemtime($filename));
            $hash = substr(md5(file_get_contents($filename)), 0, 8);
        }
        $exists2 = file_exists($filename2);
        if($exists2) { 
            $file_last_change2 = date("F d Y H:i:s.", filemtime($filename2));
            $hash2 = substr(md5(file_get_contents($filename2)), 0, 8);
        }

        $classes = array();
        if(!$exists OR !$exists2) { 
            $classes[] = 'missing';
        } else {
            if($hash != $hash2) { 
                $classes[] = 'mismatch';
            }
        }
        ?>
        <div class='entry_row_filetosniff <?php echo implode(' ', $classes); ?>'>
            <div class='entry_name'>
                <span class="file"><?php echo $entry; ?></span>
                    <?php if($exists) { ?>
                    <a class="file_link" href=""/>
                    <?php
                    echo '<span class="date">' . date('Y.m.d H:i', strtotime($file_last_change)) . '</span>';
                    echo '<span class="hash">' . $hash . '</span>';
                    $hashOfFileHashes .= $hash;
                    } else {
                        echo '<span class="date"> &nbsp; </span><span class="hash"> &nbsp; </span>';
                    }
                    ?>
                </a>
                    <?php if($exists2) { ?>
                    <a class="file_link" href=""/>
                        <?php
                        echo '<span class="date date2">' . date('Y.m.d H:i', strtotime($file_last_change2)) . '</span>';
                        echo '<span class="hash hash2">' . $hash2 . '</span>';
                        $hashOfFileHashes .= $hash2;
                        } else {
                            echo '<span class="date"> &nbsp; </span><span class="hash"> &nbsp; </span>';
                        }
                        ?>
                </a>
            </div>
            <br style='clear:both;' />
        </div>
        <?php
    }

    if (count($folders) < 1 AND count($files) < 1) {
        echo '<p><b> &nbsp; &nbsp; &nbsp; no matching files or folders found.</b></p>';
    }

    echo '</div>';

    if(isset($_GET['showhash']) AND $hashOfFileHashes !== '') {
        echo '<p class="hash allfileshash">'.md5($hashOfFileHashes).'</p>';
    }



function getQueryStringAsArray() {
    $qs = $_SERVER['QUERY_STRING'];
    $array = array();
    $qsParts = explode('&', $qs);
    foreach($qsParts as $keyVal) {
        $keyValParts = explode('=', $keyVal); 
        if (!isset($keyValParts[1]) OR $keyValParts[1] === null) {
            $keyValParts[1] = '';
        }
        $array[$keyValParts[0]] = $keyValParts[1];
    }
    return $array;
}

function makeQueryStringFromArray($parts) {
    $qs = '';
    $array = array();
    foreach($parts as $key => $val) {
        $array[] = "$key=$val";
    }
    return implode('&', $array);
}

function getParentDir($currentDir) {

    $currentDir = trim($currentDir);
    $currentDir = str_replace('%3A', ':', $currentDir);
    $currentDir = str_replace('%2F', "/", $currentDir);

    $folders = array_filter(explode('/', urldecode($currentDir)));
    array_pop($folders);
    $parentDir = urlencode(implode('/', $folders));

    return $parentDir;
}


?>

<script src="dm_js.js"></script>
</body>
</html>