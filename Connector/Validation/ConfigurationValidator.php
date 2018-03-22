<?php

require_once 'Configuration/IConnectorConfiguration.php';
require_once 'Log/LogLevel.php';
require_once 'Configuration/DefaultConfiguration.php';
require_once 'Exceptions/ConnectorException.php';

use iDEALConnector\Configuration\DefaultConfiguration;
use iDEALConnector\Configuration\IConnectorConfiguration;
use iDEALConnector\Log\LogLevel;

/**
 * ConfigurationValidator checks PHP server settings and the settings from config.conf file.
 */
class ConfigurationValidator {

    static $emptyString = "", $naString = "n/a", $requiredValidationMessage = "Parameter missing from the configuration file. ", $phpVersionRequired = '5.3';

    /**
     * @return bool
     */
    public static function isPhpVersionEnabled() 
    {
        $status = false;
                       
        if (version_compare(phpversion(), ConfigurationValidator::$phpVersionRequired, '>=')) {
            $status = true;
        }

        return $status;
    }

    /**
    * @return bool
    */
    public static function isPhpLowerVersionInstalled() 
    {
        $status = false;
                       
        if (version_compare(phpversion(), ConfigurationValidator::$phpVersionRequired, '<')) {
            $status = true;
        }

        return $status;
    }

    /**
     * @return bool
     */
    public static function isCurlEnabled() 
    {
        $status = false;

        if (function_exists('curl_exec')) {
            $status = true;
        }

        return $status;
    }

    /**
     * @return bool
     */
    public static function isOpenSslEnabled() 
    {
        $status = false;

        if (extension_loaded('openssl')) {
            $status = true;
        }

        return $status;
    }

    /**
     * @return array
     */
    public static function getConfigFileStatus($path) 
    {      
        $conf = NULL;          
        $errors = NULL;

        try
        {
            // Load config file and read settings       
            $conf = new DefaultConfiguration($path, false);
        }        
        catch (Exception $ex) // handle errors on loading the configuration file
        {
            $errors = array(
                $path => $ex->getMessage()
            );    
            return $errors;
        }

        // Validate required fields
        $errors = array(
            'MERCHANTID' => self::checkPattern("/^[0-9]{9}$/", $conf->getMerchantID()),
            'SUBID'=> "",
            'MERCHANTRETURNURL' => self::checkUrl($conf->getMerchantReturnURL()),
            'PRIVATEKEY' => self::checkFile($conf->getPrivateKeyPath()),
            'PRIVATEKEYPASS' => self::getCertificatePrivateKeyStatus($conf->getCertificatePath(), $conf->getPrivateKeyPath(), $conf->getPassphrase()),
            'PRIVATECERT' => self::checkFile($conf->getCertificatePath()) . self::getCertificateStatus($conf->getCertificatePath()),            

            'ACQUIRERURL' => self::checkUrl($conf->getAcquirerTransactionURL()),
            'ACQUIRERTIMEOUT' => "",
            'CERTIFICATE0' => self::checkFile($conf->getAcquirerCertificatePath()),

            'EXPIRATIONPERIOD' => "",            

            'LOGFILE' => "",
            'TRACELEVEL' => "",
            'PROXY' => "",
            'PROXYACQURL' => ""
        );

        // Validate optional fields
        if (!is_null($conf->getSubIDString())) {
            $errors['SUBID'] = self::checkInt($conf->getSubIDString(), 999999);
        }

        if (!is_null($conf->getTimeoutString())) {
            $errors['ACQUIRERTIMEOUT'] = self::checkInt($conf->getTimeoutString());
        }

        if (!is_null($conf->getExpirationPeriodString())) {
            $errors['EXPIRATIONPERIOD'] = self::checkPattern("/^PT\d{1,2}(M|H)$/", $conf->getExpirationPeriodString());
        }

        if (!is_null($conf->getLogFile())) {
            $errors['LOGFILE'] = self::checkFile($conf->getLogFile());
        }

        if (!is_null($conf->getProxy())) {
            $errors['PROXY'] = self::checkUrl($conf->getProxy());
        }

        if (!is_null($conf->getProxyUrl())) {
            $errors['PROXYACQURL'] = self::checkUrl($conf->getProxyUrl());
        }

        if (!is_null($conf->getLogLevelString())) {
            $errors['TRACELEVEL'] = self::checkPattern("/^(DEBUG|ERROR)$/", $conf->getLogLevelString());
        }

        return $errors;
    }

    /**
     * @return string
     */
    private static function getCertificateStatus($certificateFile) 
    {        
        $signatureAlgorithm = "sha256WithRSAEncryption";                       
        $keySize = "2048";        

        if ($certificateFile == self::$emptyString)
            return self::$emptyString;

        $cer = file_get_contents($certificateFile);
             
        if (!$cer)
            return self::$naString;
         
        $res = openssl_x509_read($cer);
        openssl_x509_export($res, $out, FALSE);            

        if (preg_match('/^\s+RSA Public Key: \(\s*(.*) bit\)\s*$/m', $out, $match) && $keySize != $match[1]) {
                return "The certificate should have a 2048-bit key length. ";            
        }

        if (preg_match('/^\s+Signature Algorithm:\s*(.*)\s*$/m', $out, $match) && $signatureAlgorithm != $match[1]) { 
            return "The certificate doesn't have support for SHA256 signature algorithm. ";
        }
      
        return self::$emptyString;
    }

    /**
     * @return string
     */
    private static function getCertificatePrivateKeyStatus($certificateFile, $privateKeyFile, $privateKeyPwd) 
    {        
        $fileProtocol = "file://";
        $privateKey = @openssl_pkey_get_private($fileProtocol . $privateKeyFile, $privateKeyPwd);       

        $isValid = @openssl_x509_check_private_key($fileProtocol . $certificateFile, $privateKey);
        if (!$isValid) {
            return "The private key file is invalid and/or the private key password doesn't correspond to the acceptor's certificate. ";
        }

        return self::$emptyString;
    }

    /**
     * @return string
     */
    private static function checkPattern($pattern, $subject) 
    {
        if ($subject == null) {
            return self::$requiredValidationMessage;
        }

        if (!preg_match($pattern, $subject)) {
            return "'$subject' is not valid. ";
        }        
        
        return self::$emptyString;        
    }

    /**
     * @return string
     */
    private static function checkUrl($url) 
    {
        if ($url == null) {
            return self::$requiredValidationMessage;
        }
        
        if (!filter_var($url, FILTER_VALIDATE_URL) || strlen($url > 512)) {
            return "'$url' is not a valid URL.";
        }
        
        return self::$emptyString;
    }

    /**
     * @return string
     */
    private static function checkInt($number, $maxSize = null) 
    {
        if ($number == null) {
            return self::$requiredValidationMessage;
        }

        if (!is_numeric($number))
            return "'$number' is not a valid number value. ";
        else {
            if ($maxSize != null) {
                $number = intval($number);
                if ($number < 0 || $number > $maxSize)
                    return"'$number' is not between 0 and 999999 interval. ";
            }
        }

        return self::$emptyString;
    }

    /**
     * @return string
     */
    private static function checkFile($file) 
    {
        if ($file == null) {            
            return self::$requiredValidationMessage;
        }

        $handle = @fopen($file, 'r');
        if (!$handle) {
            return "The '$file' file is not readable. ";
        }
        else {
            @fclose($handle);
            return self::$emptyString;
        }

        return self::$naString;
    }
}
