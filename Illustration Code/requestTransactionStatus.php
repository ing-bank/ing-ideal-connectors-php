<?php
use iDEALConnector\iDEALConnector;
use iDEALConnector\Exceptions\SerializationException;
use iDEALConnector\Configuration\DefaultConfiguration;

use iDEALConnector\Exceptions\SecurityException;
use iDEALConnector\Exceptions\ValidationException;

use iDEALConnector\Exceptions\iDEALException;

use iDEALConnector\Entities\AcquirerStatusResponse;

date_default_timezone_set('UTC');

require_once("Connector/iDEALConnector.php");

$config = new DefaultConfiguration("Connector/config.conf");
$actionType = "";

$errorCode = 0;
$errorMsg = "";
$consumerMessage = "";
$transactionID = "";

$acquirerID = "";
$consumerName = "";
$consumerIBAN = "";
$consumerBIC = "";
$amount = "";
$currency = "";
$statusDateTime = null;
$status = "";

if (isset($_POST["submitted"]))
    $actionType = $_POST["submitted"];

if (isset($_GET["trxid"]))
    $transactionID = $_GET["trxid"];

if (isset($_POST["transactionId"]))
    $transactionID = $_POST["transactionId"];

if ($actionType == "Request Transaction Status") {
    $iDEALConnector = iDEALConnector::getDefaultInstance("Connector/config.conf");

    try
    {
        $response = $iDEALConnector->getTransactionStatus($transactionID);

        /* @var $response AcquirerStatusResponse */
        $acquirerID = $response->getAcquirerID();
        $consumerName = $response->getConsumerName();
        $consumerIBAN = $response->getConsumerIBAN();
        $consumerBIC = $response->getConsumerBIC();
        $amount = $response->getAmount();
        $currency = $response->getCurrency();
        $statusDateTime = $response->getStatusTimestamp();
        $transactionID = $response->getTransactionID();
        $status = $response->getStatus();
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
    <title>IAC-PHP - Function: Get Transaction Status</title>
</head>
<body>
<form method="post">
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
            <td>getTransactionStatus</td>
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

    <table class="box" width="100%">
        <tbody>
        <tr>
            <td colspan="2"><i style="text-decoration: underline;">Function parameters:</i></td>
        </tr>
        <tr>
            <td width="200">Transaction ID:</td>
            <td><label>
                <input type="text" size="60" name="transactionId" value="<?php echo $transactionID; ?>">
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
                <div style="text-align: center;"><input type="submit" name="submitted" value="Request Transaction Status"></div>
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
            <td><?php echo $acquirerID;?>
            </td>
        </tr>
        <tr>
            <td width="200">Transaction_Status:</td>
            <td><?php
                echo $status;
                /*
                if ($status === IDEAL_TX_STATUS_SUCCESS) {
                    print("Success");
                }
                if ($status === IDEAL_TX_STATUS_CANCELLED) {
                    print("Cancelled");
                }
                if ($status === IDEAL_TX_STATUS_EXPIRED) {
                    print("Expired");
                }
                if ($status === IDEAL_TX_STATUS_FAILURE) {
                    print("Failure");
                }
                if ($status === IDEAL_TX_STATUS_OPEN) {
                    print("Open");
                }
                */
                ?>
            </td>
        </tr>
        <tr>
            <td width="200">Consumer IBAN:</td>
            <td><?php echo $consumerIBAN;?>
            </td>
        </tr>
        <tr>
            <td width="200">Consumer BIC:</td>
            <td><?php echo $consumerBIC;?>
            </td>
        </tr>
        <tr>
            <td width="200">Status date timestamp:</td>
            <td><?php if ($statusDateTime!=null) echo $statusDateTime->format('Y-m-d H:i:s');?>
            </td>
        </tr>
        <tr>
            <td width="200">Consumer Name:</td>
            <td><?php echo $consumerName;?>
            </td>
        </tr>        
            <?php } ?>
        </tbody>
    </table>
    <br>
</form>
</body>
</html>
