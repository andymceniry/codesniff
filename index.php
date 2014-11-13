<?php
error_reporting(E_ALL ^ E_NOTICE);
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
<body>

<br style='clear:both;' />

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

echo '<div class="infopath">' . str_replace('\\', '/', $dir) . '</div>';

if (isset($_GET['filetosniff']) AND $_GET['filetosniff'] !='') {
    ?>
    <form action="<?php echo basename(__FILE__); ?>" method="get">
    <input type="hidden" name="path" value="<?php echo $dir; ?>" />    
    <input type="hidden" name="dir" value="current" />
    <input type="image" src="wcs_images/back.png" class="submit_back" />
    </form>

    <div class='report_header'>
    <form action="<?php echo basename(__FILE__); ?>" method="get">
    <input type="hidden" name="standard" value="<?php echo $_GET['standard']; ?>" />
    <input type="hidden" name="path" value="<?php echo $_GET['path']; ?>" />
    <input type="hidden" name="filetosniff" value="<?php echo $_GET['filetosniff']; ?>" />
    <input type="hidden" name="dir" value="current" />
    <?php echo (isset($_GET['sniff_folder_summary']) AND $_GET['sniff_folder_summary'] == 'Y') ? '<input type="hidden" name="sniff_folder_summary" value="Y"/>' : ''; ?>
    <label for="showSources">show sources<input type="checkbox" name="showSources" id="showSources" value="Y" <?php echo isset($_GET['showSources']) ? ' checked="checked"' : ''; ?>/></label>
    <input type="submit" value="RE-SNIFF" name="resniff" class="submit_sniff" /><br />
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
    $r = include 'phpcs.php';
    echo '</pre></div>';

    exit;
}

if ($handle = opendir($dir)) {

    if ($dir != dirname(getcwd())) {
        ?>
        <form action="<?php echo basename(__FILE__); ?>" method="get">
        <input type="hidden" name="path" value="<?php echo $dir; ?>" />    
        <input type="hidden" name="dir" value="previous" />
        <input type="image" src="wcs_images/back.png" class="submit_back" />
        </form>
        <?php
    }

    $extensionstosniff = array('php','css');
    $typepicture = array('bmp','gif','png','jpg');
    
    while (false !== ($entry = readdir($handle))) {
        ?>
        <form class="form_row" action="<?php echo basename(__FILE__); ?>" method="get">
        <input type="hidden" name="path" value="<?php echo $dir; ?>" />
        <?php
        if ($entry != "." && $entry != ".." && $entry != "webcodesniffer") {
            if (is_dir($dir."/".$entry) === true) {
                ?>
                <div class='entry_row_dir'>
                    <input type="hidden" name="dir" value="next" />
                    <a class="folder_link" href="?path=<?php echo $dir;?>&dir=next&dir_name=<?php echo $entry; ?>"/><?php echo $entry; ?></a>
                    <div class="entry_commandline">
                        
                        <span class='standard'>sniff type:</span>
                        <select name='sniff_folder_summary'>
                            <option value="Y" selected="selected">Summary</option>
                            <option value="N">Full Sniff</option>
                        </select>
                        
                        <input type="submit" value="TEST" name="sniff" class="submit_sniff" />
                    </div>
                    <input type="hidden" name="standard" value="DM"/>
                    <input type="hidden" name="filetosniff" value="<?php echo $entry; ?>" />
                    <input type="hidden" name="dir" value="current" />
                </div>
                <?php            
            } else {
                
                if (in_array(pathinfo($dir."/".$entry, PATHINFO_EXTENSION), $extensionstosniff)) {
                    ?>
                    <div class='entry_row_filetosniff'>
                        <div class='entry_name'><?php echo $entry; ?></div>
                        <div class='entry_commandline'>
                            <span class='standard hide'>Standard:</span><select name='standard' class='hide'>
                                <option value="DM" selected="selected">DM</option>
                            </select>
                            <input type="submit" value="TEST" name="sniff" class="submit_sniff" />
                            <input type="hidden" name="filetosniff" value="<?php echo $entry; ?>" />
                            <input type="hidden" name="dir" value="current" />
                        </div>
                        <br style='clear:both;' />
                    </div>
                    <?php
                } else {
                
                    if (in_array(pathinfo($dir."/".$entry, PATHINFO_EXTENSION), $typepicture)) {
                        ?>
                        <div class='entry_row_file_picture'><div class='entry_name'><?php echo $entry; ?></div><br style='clear:both;' /></div>
                        <?php
                    } else {
                        ?>
                        <div class='entry_row_file_generic'><div class='entry_name'><?php echo $entry; ?></div><br style='clear:both;' /></div>
                        <?php                    
                    }
                }
            }
        }
        ?>
        </form>
        <?php
    }
    closedir($handle);
}
?>

</body>
</html>