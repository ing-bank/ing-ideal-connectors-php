<?php   

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

$yesString = "Yes";
$noString = "No";
$okString = "Ok";
$isPhpLowerVersionInstalled = FALSE; 

if (version_compare(phpversion(), '5.3', '<')) {
    $isPhpLowerVersionInstalled = TRUE;     
} else {
    require_once("Validation\ConfigurationValidator.php");
    $errors = ConfigurationValidator::getConfigFileStatus("config.conf");       
}
?>
<html lang="en">
    <!-- This page will serve as a pre-instalation check for iDEAL Advanced PHP Connector -->
    <head>
        <title> iDEAL Settings Check </title>
        <style>
            .yes:after{
                content:"Yes";
                color: green !important;
            }
            .no:after{
                content:"No";
                color: red !important;
            }
            .lowerPhpVersion:after{
                content:"A lower PHP version has been detected, but PHP Version 5.3.x or greater is required for the iDEAL connector.";
                color: red !important;
            }
            .ok:after {
                content: "Ok";
                color: green !important;
            }
            #logo {
                width:            100px;
                height:           100px;
                background-image: url('data:image/gif;base64,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');
                border-image: none;
            }
           
            .col1 {
                 width: 300px;
            }
            .col2 {
                 min-width: 80px;               
                 max-width: 200px;
            }
        </style>
    </head>
    <body>

        <table border="0" style="width: 100%;">
    <tbody>
    <tr>
        <td style="width: 120px;">
            <div id="logo"></div>
        </td>
        <td>
            <span style="font: bold 24pt arial">Advanced Connector - PHP</span><br>
            <span style="font: 18pt arial">**Installation Configuration Check**</span>
        </td>
    </tr>
    </tbody>
</table>
        <p>In order to integrate the iDEAL Advanced PHP connector into the acceptor's system, <br/> the following settings and configuration are required to be properly configured. <br/>Please check the installation manual for more details.</p>
        <p><b>System requirements</b> check: </p>
        <table class="table" border="1">
            <tbody>
                <tr>
                    <td class="col1"><b>Feature</b></td>
                    <td class="col2"><b>Installed</b></td>
                </tr>
<?php if ($isPhpLowerVersionInstalled == TRUE) { ?>
                <tr>
                    <td>PHP Version 5.3.x or greater</td>
                    <td class="lowerPhpVersion"></td>
                </tr>
<?php } else { ?>
                <tr>
                    <td>PHP Version 5.3.x or greater</td>
                    <td class="<?php echo ConfigurationValidator::isPhpVersionEnabled() ?  $yesString : $noString ?>"></td>
                </tr>
                <tr>
                    <td>cURL Library</td>
                    <td class="<?php echo ConfigurationValidator::isCurlEnabled() ?  $yesString : $noString ?>"></td>
                </tr>
                <tr>
                    <td>OpenSSL Extension</td>
                    <td class="<?php echo ConfigurationValidator::isOpenSslEnabled() ? $yesString : $noString ?>"></td>
                </tr>   
<?php } ?>             
            </tbody>
        </table>
	
<?php if ($isPhpLowerVersionInstalled == FALSE) { ?>
        <p><b>Configuration file</b> check (config.conf): </p>	
        <table class="table" border="1">
            <tbody>
                <tr>
                    <td class="col1"><b>Setting</b></td>
                    <td class="col2"><b>Status</b></td>
                </tr>
                <?php
                foreach ($errors as $key => $val) {
                    echo "<tr>"
                    . "<td>" . $key . "</td>"
                    . "<td style='color:red' " . (empty($val) ? 'class="Ok"' : '' ) . "'>" . $val . "</td>"
                    . "</tr>";
                }
                ?>
            </tbody>
        </table>   
<?php } ?>

    </body>
</html>