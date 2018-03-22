<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>IAC-PHP</title>
    </head>
    <body>
        <table border="0" width="100%">
            <tbody>
            <tr>
                <td width="120">
                    <img src="./icons/ideal_logo.gif" alt="">
                </td>
                <td>
                    <span style="font: bold 24pt arial">Advanced Connector - PHP</span><br>
                    <span style="font: 18pt arial">**Test Page**</span>
                </td>
            </tr>
    </tbody>
        </table>
        <br />
        Please <a target="_blank" href="./Connector/configurationCheck.php">check</a> your installation configuration before testing!
        <br /><br />
For a successfull iDEAL payment the following flow should be followed.
        <br>
        <br>
        <table cellpadding="4" cellspacing="0" border="1">
            <tbody>
            <tr>
                <td><b>Step</b></td>
                <td><b>Description</b></td>
                <td><b>Action</b></td>
            </tr>
            <tr>
                <td align="center">1</td>
                <td>Requests a list* of issuers.</td>
                <td>(Function: <a href="./getIssuerList.php">getIssuers</a>)</td>
            </tr>
            <tr>
                <td align="center">2</td>
                <td>Select an issuer.</td>
                <td><i>User action</i></td>
            </tr>
            <tr>
                <td align="center">3</td>
                <td>Start a new transaction.</td>
                <td>(Function: <a href="./requestTransaction.php">startTransaction</a>)</td>
            </tr>
            <tr>
                <td align="center">4</td>
                <td>Authenticate transaction.</td>
                <td><i>User/Acceptant action</i></td>
            </tr>
            <tr>
                <td align="center">5</td>
                <td>Request transaction status.</td>
                <td>(Function: <a href="./requestTransactionStatus.php">getTransactionStatus</a>)</td>
            </tr>
    </tbody>
        </table>
        <br>
This API provides functionality for performing steps
        <b>1, 3</b> &amp;
        <b>5</b>.
        <br>
        <br>
        <i>* For optimal performance the retrieved list could be cached.<br></i>
    </tr>
    </body>
</html>