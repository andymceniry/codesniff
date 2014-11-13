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


?>
