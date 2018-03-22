<?php
use iDEALConnector\iDEALConnector;
use iDEALConnector\Configuration\DefaultConfiguration;
use iDEALConnector\Exceptions\SerializationException;

use iDEALConnector\Exceptions\SecurityException;
use iDEALConnector\Exceptions\ValidationException;
use iDEALConnector\Exceptions\iDEALException;

use iDEALConnector\Entities\Transaction;
use iDEALConnector\Entities\AcquirerTransactionResponse;

date_default_timezone_set('UTC');

require_once("Connector/iDEALConnector.php");

$config = new DefaultConfiguration("Connector/config.conf");
$actionType = "";

$errorCode = 0;
$errorMsg = "";
$consumerMessage = "";

$issuerId = "";
$purchaseId = "1234567890123456";
$amount = 10;
$description = "";
$entranceCode = "1234567890123456789012345678901234567890";
$merchantReturnUrl = $config->getMerchantReturnURL();
$expirationPeriod = $config->getExpirationPeriod();

$acquirerID = "";
$issuerAuthenticationURL = "";
$transactionID = "";

if (isset($_POST["submitted"]))
    $actionType = $_POST["submitted"];

if (isset($_POST["issuerId"]))
    $issuerId = $_POST["issuerId"];

if (isset($_POST["purchaseId"]))
    $purchaseId = $_POST["purchaseId"];

if (isset($_POST["amount"]))
    $amount = floatVal($_POST["amount"]);

if (isset($_POST["description"]))
    $description = $_POST["description"];

if (isset($_POST["entranceCode"]))
    $entranceCode = $_POST["entranceCode"];

if (isset($_POST["merchantReturnURL"]))
    $merchantReturnUrl = $_POST["merchantReturnURL"];

if (isset($_POST["expirationPeriod"]))
    $expirationPeriod = intVal($_POST["expirationPeriod"]);

if ($actionType == "Request Transaction") {
    $iDEALConnector = iDEALConnector::getDefaultInstance("Connector/config.conf");
    
    try
    {
        $response = $iDEALConnector->startTransaction(
            $issuerId,
            new Transaction(
                $amount,
                $description,
                $entranceCode,
                $expirationPeriod,
                $purchaseId,
                'EUR',
                'nl'
            ),
            $merchantReturnUrl
        );

        /* @var $response AcquirerTransactionResponse */
        $acquirerID = $response->getAcquirerID();
        $issuerAuthenticationURL = $response->getIssuerAuthenticationURL();
        $transactionID = $response->getTransactionID();
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
        $errorMsg = $ex->getMessage();
        $consumerMessage = $ex->getConsumerMessage();

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
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>IAC-PHP - Function: Get Transaction</title>
</head>
<script>
    window.onload = function(){
        document.getElementById("issuerAuthentication").onclick = function(){
            window.location.replace("<?php echo $issuerAuthenticationURL ?>");
        }
    }

</script>
<body>
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
        <td>startTransaction</td>
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
        <td><?php echo $config->getMerchantID(); ?>
        </td>
    </tr>
    <tr>
        <td width="200">Sub ID:</td>
        <td><?php echo $config->getSubID(); ?>
        </td>
    </tr>
    </tbody>
</table>
<br>

<form method="post">
    <table class="box" width="100%">
        <tbody>
        <tr>
            <td colspan="2"><i style="text-decoration: underline;">Function parameters:</i></td>
        </tr>
        <tr>
            <td width="200">Issuer ID:</td>
            <td><label>
                <input type="text" size="60" name="issuerId" value="<?php echo $issuerId; ?>">
            </label>
            </td>
        </tr>
        <tr>
            <td width="200">Purchase ID:</td>
            <td><label>
                <input type="text" size="60" name="purchaseId" value="<?php echo $purchaseId; ?>">
            </label>
            </td>
        </tr>
        <tr>
            <td width="200">Amount:</td>
            <td><label>
                <input type="text" size="60" name="amount" value="<?php echo $amount; ?>">
            </label>
            </td>
        </tr>
        <tr>
            <td width="200">Description:</td>
            <td><label>
                <input type="text" maxlength="32" size="60" name="description" value="<?php echo $description; ?>">
            </label>
            </td>
        </tr>
        <tr>
            <td width="200">Entrance Code:</td>
            <td><label>
                <input type="text" size="60" name="entranceCode" value="<?php echo $entranceCode; ?>">
            </label>
            </td>
        </tr>
        <tr>
            <td width="200">Merchant Return URL:</td>
            <td><label>
                <input type="text" size="60" name="merchantReturnURL"
                       value="<?php echo $merchantReturnUrl ?>">
            </label>
            </td>
        </tr>
        <tr>
            <td width="200">Expiration Period*:</td>
            <td><label>
                <input type="text" size="60" name="expirationPeriod"
                       value="<?php echo $expirationPeriod; ?>">
            </label>
            </td>
        </tr>
        </tbody>
    </table>
    <br>

    <table class="box" width="100%">
        <tbody>
        <tr>
            <td style="margin:0;padding:0">
                <div style="text-align: center;"><input type="submit" name="submitted" value="Request Transaction"></div>
            </td>
        </tr>
        </tbody>
    </table>
</form>
<br>

<table class="box" width="100%">
    <tbody>
    <tr>
        <td colspan="2"><i style="text-decoration: underline;">Result:</i></td>
    </tr>
    <?php  if ($errorCode != "") { ?>
    <tr>
        <td width="200">Error Code</td>
        <td><?php echo $errorCode; ?></td>
    </tr>
    <tr>
        <td>Error Message</td>
        <td><?php echo $errorMsg; ?></td>
    </tr>
    <tr>
        <td>Consumer Message</td>
        <td><?php echo $consumerMessage; ?></td>
    </tr>
        <?php } else { ?>
    <tr>
        <td width="200">Acquirer ID:</td>
        <td><?php echo $acquirerID; ?>
        </td>
    </tr>
    <tr>
        <td width="200">Transaction ID:</td>
        <td><?php echo $transactionID; ?>
        </td>
    </tr>
    <tr>
        <td width="200">Issuer Authentication URL:</td>
        <td><?php echo $issuerAuthenticationURL; ?>
        </td>
    </tr>
        <?php } ?>
    </tbody>
</table>
<br>

<table class="box" width="100%">
    <tbody>
    <tr>
        <td style="margin:0;padding:0">
            <form method="post">
                <div style="text-align: center;">
                <input id="issuerAuthentication" type="button" name="submitted" value="Issuer Authentication"
                       <?php if ($issuerAuthenticationURL == "") { ?>disabled="disabled"<?php } ?>>
                </div>
            </form>
        </td>
    </tr>
    </tbody>
</table>
<br>
</body>
</html>