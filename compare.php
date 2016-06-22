<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('max_execution_time', 300);
include_once 'dm_functions.php';
if (file_exists('env.php')) {
    include 'env.php';
}

if (isset($env) AND array_key_exists('default_compare_url', $env) AND $_SERVER['QUERY_STRING'] === '') {
    header('location: ' . $env['default_compare_url']);
    die();
}
if ($_SERVER['QUERY_STRING'] === '') {
    $message = 'Missing dir1 and dir2 values in the URL.  Suggest adding a "default_compare_url" setting in your env file.';
    die('<pre>'.$message.'<br/>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
}

//  DIRECTORY ONE
if (!isset($_GET['dir1'])) {
    $message = 'dir1 not found in URL';
    die('<pre>'.$message.'<br/>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
}
$dir1 = $_GET['dir1'];
if (!is_dir($dir1)) {
    $message = 'Directory ' . $dir1 . ' not found';
    die('<pre>'.$message.'<br/>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
}
$handle = @opendir($dir1);
if (!$handle) {
    $message = $dir1 . ' is not a valid directory';
    die('<pre>'.$message.'<br/>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
}
//  DIRECTORY TWO
if (!isset($_GET['dir2'])) {
    $message = 'dir2 not found in URL';
    die('<pre>'.$message.'<br/>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
} else {
    $dir2 = $_GET['dir2'];
}
if (!is_dir($dir2)) {
    $message = 'Directory ' . $dir2 . ' not found';
    die('<pre>'.$message.'<br/>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
}
$handle2 = @opendir($dir2);
if (!$handle2) {
    $message = $dir2 . ' is not a valid directory';
    die('<pre>'.$message.'<br/>Exit at Line '.__LINE__.' of <span title="'.__FILE__.'">'.str_replace(array(__DIR__, '\\'), '', __FILE__).'</span> @ '.date('H:i:s'));
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
</head>

<body id="compare">

<?php

//  create parent links
$linkParts1 = getQueryStringAsArray();
$linkParts1['dir1'] = getParentDir($linkParts1['dir1']);
unset($linkParts1['compare']);
$link1 = makeQueryStringFromArray($linkParts1);
$linkParts2 = getQueryStringAsArray();
$linkParts2['dir2'] = getParentDir($linkParts2['dir2']);
unset($linkParts2['compare']);
$link2 = makeQueryStringFromArray($linkParts2);

//  display current directories
echo '<div class="infopath clearfix"><p>' . str_replace('//', '/', str_replace('\\', '/', $dir1)).'</p>';
echo '<a href="?'.$link1.'"><input type="image" src="wcs_images/back.png" class="submit_back" /></a>';
echo '<br/><br/>';
echo '<p>' . str_replace('//', '/', str_replace('\\', '/', $dir2)).'</p>';
echo '<a href="?'.$link2.'"><input type="image" src="wcs_images/back.png" class="submit_back" /></a>';
echo '</div>';

//  walk through each folder adding contents to releveant array then reduce down to remove duplicates
$files = array();
$folders = array();
while (false !== ($entry = readdir($handle))) {
    if ($entry != "." && $entry != ".." && $entry != "webcodesniffer") {
        if (is_dir($dir1."/".$entry) === true) {
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
sort($files);

$hash = array();

//  output rows
echo '<div class="entry_row_holder">';

//  loop through folders
foreach ($folders as $entry) {
    $exists1 = is_dir($dir1."/".$entry);
    $exists2 = is_dir($dir2."/".$entry);

    if ($exists1) {
        $linkParts1 = getQueryStringAsArray();
        $linkParts1['dir1'] .= '/' . $entry;
        unset($linkParts1['compare']);
        $link1 = makeQueryStringFromArray($linkParts1);
        $hash['eveything']['dir1'] .= md5($entry);
    }
    if ($exists2) {
        $linkParts2 = getQueryStringAsArray();
        $linkParts2['dir2'] .= '/' . $entry;
        unset($linkParts2['compare']);
        $link2 = makeQueryStringFromArray($linkParts2);
        $hash['eveything']['dir2'] .= md5($entry);
    }
    ?>
    <?php

    if ($exists1) {
        echo '<div class="entry_row_dir"><a class="folder_link" href="?' . $link1 . '"/>' . $entry . '</a>';
    } else {
        echo '<div class="entry_row_dir nobg"><div class="folder_link nobg"> &nbsp; </div>';
    }
    if ($exists2) {
        echo '<a class="folder_link2" href="?' . $link2 . '"/>' . $entry . '</a>';
    } else {
        echo '<div class="folder_link2 nobg"> &nbsp; </div>';
    }
    ?>
    </div>
    <?php
}



foreach ($files as $entry) {
    $filename1 = $dir1 . '/' . $entry;
    $filename2 = $dir2 . '/' . $entry;

    $exists1 = file_exists($filename1);
    if ($exists1) {
        $file_last_change1 = date("F d Y H:i:s.", filemtime($filename1));
        $hash1 = substr(md5(file_get_contents($filename1)), 0, 8);
    }
    $exists2 = file_exists($filename2);
    if ($exists2) {
        $file_last_change2 = date("F d Y H:i:s.", filemtime($filename2));
        $hash2 = substr(md5(file_get_contents($filename2)), 0, 8);
    }

    $classes = array();
    if (isset($_GET['compare'])) {
        if (!$exists1 OR !$exists2) {
            $classes[] = 'missing';
        } else {
            if ($hash1 != $hash2) {
                $classes[] = 'mismatch';
            }
        }
    } else {
        if (!$exists1) {
            $classes[] = 'nobgimg';
        }
    }
    ?>
    <div class='entry_row_filetosniff <?php echo implode(' ', $classes); ?>'>
        <div class='entry_name'>
    <?php
        //  browse mode
        if (!isset($_GET['compare'])) {
            if ($exists1) {
                    echo '<a class="file_link" href="?' . $link1 . '"/><span class="file">' . $entry . '</span></a>';
            } else {
                echo '<div class="file_link"><span class="file nobg"> &nbsp; </span></div>';
            }
            if ($exists2) {
                    echo '<a class="file_link file_link_r" href="?' . $link2 . '"/><span class="file">' . $entry . '</span></a>';
            } else {
                echo '<div class="file_link file_link_r"><span class="file nobg"> &nbsp; </span></div>';
            }
        } else {
            //  compare mode
            ?>
            <span class="file"><?php echo $entry; ?></span>
            <?php if ($exists1) { ?>
                <a class="file_link" href=""/>
                <?php
                    echo '<span class="date">' . date('Y.m.d H:i', strtotime($file_last_change1)) . '</span>';
                    echo '<span class="hash">' . $hash1 . '</span>';
                    if ($exists1 AND $exists2) {
                        $hash['common']['dir1'] .= $hash1;
                    }
            } else {
                echo '<span class="date"> &nbsp; </span><span class="hash"> &nbsp; </span>';
            }
            $hash['eveything']['dir1'] .= $hash1;
            $hash['files']['dir1'] .= $hash1;
            ?>
            </a>
                <?php if ($exists2) { ?>
                <a class="file_link" href=""/>
                <?php
                echo '<span class="date date2">' . date('Y.m.d H:i', strtotime($file_last_change2)) . '</span>';
                echo '<span class="hash hash2">' . $hash2 . '</span>';
                $hashOfFileHashes2 .= $hash2;
                if ($exists1 AND $exists2) {
                    $hash['common']['dir2'] .= $hash2;
                }
                    } else {
                        echo '<span class="date"> &nbsp; </span><span class="hash"> &nbsp; </span>';
                    }
                    $hash['eveything']['dir2'] .= $hash2;
                    $hash['files']['dir2'] .= $hash2;
                        ?>
                </a>
        <?php
        }
        ?>
        </div>
        <br style='clear:both;' />
    </div>
    <?php
    }

    //  if no files or folders
    if (count($folders) < 1 AND count($files) < 1) {
        echo '<p><b> &nbsp; &nbsp; &nbsp; no matching files or folders found.</b></p>';
    }

echo '</div>';


if (!isset($_GET['compare'])) {
    echo '<a class="docompare" href="?'.$_SERVER['QUERY_STRING'].'&compare">COMPARE</a>';
} else {
    $class = $hash['eveything']['dir1'] === $hash['eveything']['dir2'] ? 'match' : '';
    echo '<div class="hashtotals clearfix '.$class.'">';
    echo '<p class="hashtotalstitle">EVERYTHING</p>';
    echo '<p class="hash allfileshash">'.md5($hash['eveything']['dir1']).'</p>';
    echo '<p class="hash allfileshash">'.md5($hash['eveything']['dir2']).'</p>';
    echo '</div>';
    $class = $hash['files']['dir1'] === $hash['files']['dir2'] ? 'match' : '';
    echo '<div class="hashtotals clearfix '.$class.'">';
    echo '<p class="hashtotalstitle">FILES</p>';
    echo '<p class="hash allfileshash">'.md5($hash['files']['dir1']).'</p>';
    echo '<p class="hash allfileshash">'.md5($hash['files']['dir2']).'</p>';
    echo '</div>';
    $class = $hash['common']['dir1'] === $hash['common']['dir2'] ? 'match' : '';
    echo '<div class="hashtotals clearfix '.$class.'">';
    echo '<p class="hashtotalstitle">COMMON FILES</p>';
    echo '<p class="hash allfileshash">'.md5($hash['common']['dir1']).'</p>';
    echo '<p class="hash allfileshash">'.md5($hash['common']['dir2']).'</p>';
    echo '</div>';
}



function getQueryStringAsArray()
{
    $qs = $_SERVER['QUERY_STRING'];
    $array = array();
    $qsParts = explode('&', $qs);
    foreach ($qsParts as $keyVal) {
        $keyValParts = explode('=', $keyVal);
        if (!isset($keyValParts[1]) OR $keyValParts[1] === null) {
            $keyValParts[1] = '';
        }
        $array[$keyValParts[0]] = $keyValParts[1];
    }
    return $array;

}

function makeQueryStringFromArray($parts)
{
    $qs = '';
    $array = array();
    foreach ($parts as $key => $val) {
        $array[] = "$key=$val";
    }
    return implode('&', $array);

}

function getParentDir($currentDir)
{
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