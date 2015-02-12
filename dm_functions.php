<?php

//  returns the current query string excluding any specfied keys
function getQueryStringExcludingKeys( $aExcludedKeys = NULL ) {

    //  if no keys supplied then set to blank array
    if($aExcludedKeys == NULL) {
        $aExcludedKeys = array();
    }

    //  if string is supplied then convert to a single element array
    if(is_string($aExcludedKeys)) {
        $s = $aExcludedKeys;
        $aExcludedKeys = array();
        $aExcludedKeys[] = $s;
    }
    
    $aNewParts = array();
    $aOldParts = explode('&', $_SERVER['QUERY_STRING']);

    //  loop through each key of current URL
    foreach($aOldParts as $sOldPart) {
        $bUsePart = true;
        //  loop through each excluded key
        foreach($aExcludedKeys as $key) {
            //  if we have a match then set flag to false
            if(substr($sOldPart,0,strlen($key)) == $key) {
                $bUsePart = false;
            }
        }
        //  add key to new array if flag not set to false
        if($bUsePart == true) {
            $aNewParts[] = $sOldPart;
        }
    }
    //  put together new query string from array and return
    $aNewParts = implode('&', $aNewParts);
    return $aNewParts;

}




function returnTokenTypeCount($tokens, $type) {

    $kount = 0;
    foreach($tokens as $aaToken) {
        if($aaToken['type'] == $type) {
            $kount++;
        }
    }

    return $kount;

}



function getLogFilename($filename) {
    $log_filename = $filename;
    $log_filename = str_replace('\\', '_', $log_filename);
    $log_filename = str_replace('/', '_', $log_filename);
    $log_filename = str_replace('___', '_', $log_filename);
    $log_filename = str_replace(':', '', $log_filename);
    $log_filename = str_replace('__', '_', $log_filename);
    $log_filename = urlencode($log_filename);
    return $log_filename;
}

function logResults($filename, $errors, $warnings, $log_folder = 'Logs' ) {
    $log_filename = getLogFilename($filename);
    $fp = fopen($log_folder.'/'.$log_filename, 'w');
    $file_last_change = date("F d Y H:i:s.", filemtime($filename));
    $file_error_count = $errors;
    $file_warning_count = $warnings;
    $log_content = "$file_last_change\n$file_error_count\n$file_warning_count";
    fwrite($fp, $log_content);

}


function includeJslintFiles( $url) {

?>
<div class="report_filename"><p></p><input type="image" src="wcs_images/refresh.png" class="submit_back sniff-refresh" onclick="location.reload();"></div><div class="report_summary"> &nbsp; </div></pre></div>

<script src="jslint/web_jslint.js"></script>
<script src="jquery-1.10.2.min.js"></script>
<script src="jslint/lint_remote_file.js"></script>

<div id="JSLINT_" style="display:none;">
    <div id=JSLINT_EDITION></div>
    <div id=JSLINT_SOURCE><textarea></textarea></div>
    <div id=JSLINT_BUTTON></div>
    <div id=JSLINT_ERRORS></div>
    <div id=JSLINT_REPORT></div>
    <div id=JSLINT_PROPERTIES><textarea></textarea></div>
    <div id=JSLINT_OPTIONS></div>
    <input id=JSLINT_INDENT>
    <input id=JSLINT_MAXLEN>
    <input id=JSLINT_MAXERR>
    <textarea id=JSLINT_PREDEF></textarea>
    <div id=JSLINT_JSLINT><textarea></textarea></div>
    <script>ADSAFE.id("JSLINT_");</script>
    <script src="jslint/init_ui.js"></script>
    <script>
    ADSAFE.go("JSLINT_", function (dom, lib) {
        'use strict';
        lib.init_ui(dom);
        <?php
        echo 'lintExternalFile(lib, "'.$url.'");'."\n";
        ?>
    });
    </script>
</div>
<?php

}
?>
