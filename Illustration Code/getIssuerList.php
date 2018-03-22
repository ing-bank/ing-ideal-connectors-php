<?php
    
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

use iDEALConnector\iDEALConnector;
use iDEALConnector\Exceptions\ValidationException;
use iDEALConnector\Exceptions\SecurityException;
use iDEALConnector\Exceptions\SerializationException;
use iDEALConnector\Configuration\DefaultConfiguration;

use iDEALConnector\Exceptions\iDEALException;

use iDEALConnector\Entities\DirectoryResponse;

date_default_timezone_set('UTC');

require_once("Connector/iDEALConnector.php");

$config = new DefaultConfiguration("Connector/config.conf");
$errorCode = 0;
$errorMsg = "";
$consumerMessage = "";

$issuerList = "";
$acquirerID = "";
$responseDatetime = null;

$actionType = "";

if (isset($_POST["submitted"]))
    $actionType = $_POST["submitted"];

if ($actionType == "Get Issuers"){

    try
    {
        $iDEALConnector = iDEALConnector::getDefaultInstance("Connector/config.conf");
        $response = $iDEALConnector->getIssuers();

        /* @var $response DirectoryResponse*/
        foreach ($response->getCountries() as $country)
        {
            $issuerList .= "<optgroup label=\"" . $country->getCountryNames() . "\">";

            foreach ($country->getIssuers() as $issuer) {
                $issuerList .= "<option value=\"" . $issuer->getId() . "\">"
                    . $issuer->getName() . "</option>";
            }
            $issuerList .= "</optgroup>";

            $acquirerID = $response->getAcquirerID();
            $responseDatetime = $response->getDirectoryDate();
        }
    }
    catch (SerializationException $ex)
    {
        echo '<b style="color:red">Serialization:'.$ex->getMessage().'</b>';
    }
    catch (SecurityException $ex)
    {
        echo '<b style="color:red">Security:'.$ex->getMessage().'</b>';
    }
    catch(ValidationException $ex)
    {
        echo '<b style="color:red">Validation:'.$ex->getMessage().'</b>';
    }
    catch (iDEALException $ex)
    {
        $errorCode = $ex->getErrorCode();
        $consumerMessage = $ex->getConsumerMessage();
        $errorMsg = $ex->getMessage();

        echo $ex->getErrorCode()." - ".$ex->getMessage();
    }
    catch (Exception $ex)
    {
        echo '<b style="color:red">Exception:'.$ex->getMessage().'</b>';
    }
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <style>
        .center {
            text-align: center;
        }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>IAC-PHP - Function: Get Issuers</title>
    <script type="text/javascript">
        var BodyOnLoad = function(){
            var select = document.getElementById("IssuerIDs");
            select.onchange = function () {
                document.getElementById("issuerId").value = select.value;
            };
            document.getElementById("issuerId").value = select.value;
        }
    </script>
</head>
<body onload="BodyOnLoad()">
    <table border="0" width="100%">
        <tbody>
        <tr>
            <td width="120"><a href="index.php"><img src="./icons/ideal_logo.gif" alt=""></a></td>
            <td><span style="font: bold 24pt arial">Advanced Connector - PHP</span><br>
                <span style="font: 18pt arial">**Test Page**</span></td>
        </tr>
        </tbody>
    </table>
    <table class="box" width="100%">
        <tbody>
        <tr>
            <td width="200"><i style="text-decoration: underline;">Function:</i></td>
            <td>getIssuers</td>
        </tr>
        </tbody>
    </table>
    <br>
    <table class="box" width="100%">
        <tbody>
        <tr>
            <td colspan="2"><i style="text-decoration: underline;">Function parameters:</i></td>
        </tr>
        <tr>
            <td colspan="2">(none)</td>
        </tr>
        </tbody>
    </table>
    <br>
    <table class="box" width="100%">
        <tbody>
        <tr>
            <td colspan="2"><i style="text-decoration: underline;">Configuration parameters:</i></td>
        </tr>
        <tr>
            <td width="200">Merchant ID:</td>
            <td><?php echo $config->getMerchantID() ?>
            </td>
        </tr>
        <tr>
            <td width="200">Sub ID:</td>
            <td><?php echo $config->getSubID() ?>
            </td>
        </tr>
        <tr>
            <td width="200">Acquirer URL:</td>
            <td><?php echo $config->getAcquirerDirectoryURL(); ?>
            </td>
        </tr>
        </tbody>
    </table>
    <br>
    <table class="box" width="100%">
        <tbody>
        <tr>
            <td style="margin:0;padding:0">
                <form class="center" method="post">
                    <input type="submit" name="submitted" value="Get Issuers">
                </form>
            </td>
        </tr>
        </tbody>
    </table>
    <br>
    <table class="box" width="100%">
        <tbody>
        <tr>
            <td colspan="2"><i style="text-decoration: underline;">Result:</i></td>
        </tr>
        <?php  if($errorCode != "") { ?>
        <tr>
            <td width="200">Error Code</td><td><?php echo $errorCode; ?></td>
        </tr>
        <tr>
            <td>Error Message</td><td><?php echo $errorMsg; ?></td>
        </tr>
        <tr>
            <td>Consumer Message</td><td><?php echo $consumerMessage; ?></td>
        </tr>
            <?php } else { ?>
        <tr>
            <td width="200">DateTimeStamp:</td>
            <td><?php if (!is_null($responseDatetime)) echo $responseDatetime->format('Y-m-d H:i:s'); ?></td>
        </tr>
        <tr>
            <td width="200">Issuer List:</td>
            <td><label><select id="IssuerIDs"><?php echo $issuerList; ?></select></label></td>
        </tr>
            <?php } ?>
        </tbody>
    </table>
    <br>
    <table class="box" width="100%">
        <tbody>
        <tr>
            <td style="margin:0;padding:0">
                <form class="center" method="post" action="./requestTransaction.php">
                    <input type="hidden" value="RANDOM28976" id="issuerId" name="issuerId" />
                    <input id="transactionRequest" type="submit" name="submitted"
                                           value="Transaction Request" <?php if ($issuerList == "") { ?>
                                           disabled="disabled" <?php } ?>>
                </form>
            </td>
        </tr>
        </tbody>
    </table>
    <br>
</body>
</html>