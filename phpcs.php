<?php
/**
 * PHP_CodeSniffer tokenises PHP code and detects violations of a
 * defined set of coding standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

error_reporting(E_ALL | E_STRICT);

// Optionally use PHP_Timer to print time/memory stats for the run.
// Note that the reports are the ones who actually print the data
// as they decide if it is ok to print this data to screen.
@include_once 'PHP/Timer.php';
if (class_exists('PHP_Timer', false) === true) {
    PHP_Timer::start();
}

if (is_file(dirname(__FILE__).'/../CodeSniffer/CLI.php') === true) {
    include_once dirname(__FILE__).'/../CodeSniffer/CLI.php';
} else {
    //include_once 'PHP/CodeSniffer/CLI.php';
    include_once 'CodeSniffer/CLI.php';
}

$phpcs = new PHP_CodeSniffer_CLI();
$phpcs->checkRequirements();

$numErrors = $phpcs->process();

if ($numErrors === 0) {
    $icoName = 'tick';
    #exit(0);
} else {
    $icoName = 'cross';
    #exit(1);
}

?>
<script>
(function() {
    var link = document.createElement('link');
    link.type = 'image/x-icon';
    link.rel = 'shortcut icon';
    link.href = 'wcs_images/<?php echo $icoName; ?>.ico';
    document.getElementsByTagName('head')[0].appendChild(link);
}());
</script>