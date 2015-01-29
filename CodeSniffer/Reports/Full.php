<?php
error_reporting(E_ALL ^ E_NOTICE);
/**
 * Full report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Full report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.5
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Reports_Full implements PHP_CodeSniffer_Report
{


    /**
     * Prints all errors and warnings for each file processed.
     *
     * Errors and warnings are displayed together, grouped by file.
     *
     * @param array   $report      Prepared report.
     * @param boolean $showSources Show sources?
     * @param int     $width       Maximum allowed lne width.
     * @param boolean $toScreen    Is the report being printed to screen?
     *
     * @return string
     */
    public function generate(
        $report,
        $showSources=false,
        $width=80,
        $toScreen=true
    ) {

        $errorsShown = 0;
        $width       = max($width, 90);
        foreach ($report['files'] as $filename => $file) {


            echo PHP_EOL;
            echo '<div class="report_filename">';            
            echo '<p>FILE: ';
            $theFile = basename($filename);
            if (strlen($theFile) <= ($width - 9)) {
                echo $theFile;
            } else {
                echo '...'.substr($theFile, (strlen($theFile) - ($width - 9)));
            }
            echo '</p>';
            echo '<input type="image" src="wcs_images/refresh.png" class="submit_back sniff-refresh" onclick="location.reload();" />';
            echo '</div>';
            #echo PHP_EOL;

            if (empty($file['messages']) === true) {
                continue;
            }

            #echo str_repeat('-', $width).PHP_EOL;
            echo '<div class="report_summary">';

            echo ' FOUND '.$file['errors'].' ERROR(S) ';
            if ($file['warnings'] > 0) {
                echo 'AND '.$file['warnings'].' WARNING(S) ';
            }

            echo 'AFFECTING '.count($file['messages']).' LINE(S)'.PHP_EOL;
            #echo str_repeat('-', $width).PHP_EOL;
            echo '</div>';


            logResults($filename, $file['errors'], $file['warnings']);


            // Work out the max line number for formatting.
            $maxLine = 0;
            foreach ($file['messages'] as $line => $lineErrors) {
                if ($line > $maxLine) {
                    $maxLine = $line;
                }
            }

            $maxLineLength = strlen($maxLine);

            // The length of the word ERROR or WARNING; used for padding.
            if ($file['warnings'] > 0) {
                $typeLength = 7;
            } else {
                $typeLength = 5;
            }

            // The padding that all lines will require that are
            // printing an error message overflow.
            $paddingLine2  = str_repeat(' ', ($maxLineLength + 1));
            $paddingLine2 .= '  ';
            #$paddingLine2 .= '   ';
            #$paddingLine2 .= str_repeat(' ', $typeLength);
            #$paddingLine2 .= '   ';

            // The maxium amount of space an error message can use.
            $maxErrorSpace = ($width - strlen($paddingLine2) - 1);
$maxErrorSpace = 128;
            foreach ($file['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $message = $error['message'];
                        if ($showSources === true) {
                            #$message .= ' <span class="error-source">('.$error['source'].')</span>';
                        }

                        // The padding that goes on the front of the line.
                        $padding  = ($maxLineLength - strlen($line));
                        $errorMsg = wordwrap(
                            $message,
                            $maxErrorSpace,
                            PHP_EOL.$paddingLine2
                        );
                        echo '<span class="line-number '.strtolower($error['type']).'" title="'.$error['source'].'">'.str_repeat(' ', $padding).$line.'</span> ';
                        echo $errorMsg.PHP_EOL;
                        $errorsShown++;
                    }//end foreach
                }//end foreach
            }//end foreach

            #echo str_repeat('-', $width).PHP_EOL.PHP_EOL;
        }//end foreach

        if ($toScreen === true
            && PHP_CODESNIFFER_INTERACTIVE === false
            && class_exists('PHP_Timer', false) === true
        ) {
            echo PHP_Timer::resourceUsage().PHP_EOL.PHP_EOL;
        }

        return $errorsShown;

    }//end generate()


}//end class

?>
