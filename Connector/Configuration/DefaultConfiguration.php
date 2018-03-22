<?php
namespace iDEALConnector\Configuration;

use iDEALConnector\Exceptions as AppExceptions;
use iDEALConnector\Log\LogLevel;

/**
 *
 */
class DefaultConfiguration implements IConnectorConfiguration
{
    private $certificate = null;//"";
    private $privateKey = null;//"";
    private $passphrase = null;//"";

    private $acquirerCertificate = null;//"";

    private $merchantID = null;//"";
    private $subID = null;//0;
    private $subIDString = null;//0;
    private $returnURL = null;//"";

    private $expirationPeriod = null;//60;
    private $expirationPeriodString = null;//
    private $acquirerDirectoryURL = null;//"";
    private $acquirerTransactionURL = null;//"";
    private $acquirerStatusURL = null;//"";
    private $timeout = null;//10;
    private $timeoutString = null;//10;

    private $proxy = null;
    private $proxyUrl = null;//"";

    private $logFile = null;//"logs/connector.log";
    private $logLevel = null;//LogLevel::Error;
    private $logLevelString = null;//LogLevel::Error;

    function __construct($path, $defaults = true)
    {
        $this->loadFromFile($path, $defaults);
    }

    private function loadFromFile($path, $defaults)
    {
        $file = @fopen($path,'r');

        if (!$file) {
            throw new AppExceptions\ConnectorException("The configuration file is missing.");    
        }

        $config_data = array();

        if ($file) {
            while (!feof($file)) {
                $buffer = fgets($file);

                /* @var $buffer array() */
                $buffer = trim($buffer);

                if (!empty($buffer)) {

                    if ($buffer[0] != '#')
                    {
                        $pos = strpos($buffer, '=');
                        if ($pos > 0 && $pos != (strlen($buffer) - 1)) {
                            $dumb = trim(substr($buffer, 0, $pos));
                            if (!empty($dumb)) {
                                // Populate the configuration array
                                $config_data[strtoupper(substr($buffer, 0, $pos))] = substr($buffer, $pos + 1);
                            }
                        }
                    }
                }
            }
        }
        fclose($file);

        if(isset($config_data['MERCHANTID']))
            $this->merchantID = $config_data['MERCHANTID'];
        if(isset($config_data['SUBID']))
        {
            $this->subID = intval($config_data['SUBID']);
            $this->subIDString = $config_data['SUBID'];
        }
        if(isset($config_data['MERCHANTRETURNURL']))
            $this->returnURL = $config_data['MERCHANTRETURNURL'];


        if(isset($config_data['ACQUIRERURL']))
        {
            $this->acquirerDirectoryURL = $config_data['ACQUIRERURL'];
            $this->acquirerStatusURL = $config_data['ACQUIRERURL'];
            $this->acquirerTransactionURL = $config_data['ACQUIRERURL'];
        }
        if(isset($config_data['ACQUIRERTIMEOUT']))
        {
            $this->timeout = intval($config_data['ACQUIRERTIMEOUT']);
            $this->timeoutString = $config_data['ACQUIRERTIMEOUT'];
        }
        if(isset($config_data['EXPIRATIONPERIOD']))
        {
            if ($config_data['EXPIRATIONPERIOD'] === "PT1H")
                $this->expirationPeriod = 60;
            else
            {
                $this->expirationPeriodString = $config_data['EXPIRATIONPERIOD'];
                $value = substr($config_data['EXPIRATIONPERIOD'], 2, strlen($config_data['EXPIRATIONPERIOD']) - 3);
                if (is_numeric($value))
                    $this->expirationPeriod = intval($value);
            }
        }

        if(isset($config_data['CERTIFICATE0']))
            $this->acquirerCertificate = $config_data['CERTIFICATE0'];
        if(isset($config_data['PRIVATECERT']))
            $this->certificate = $config_data['PRIVATECERT'];
        if(isset($config_data['PRIVATEKEY']))
            $this->privateKey = $config_data['PRIVATEKEY'];
        if(isset($config_data['PRIVATEKEYPASS']))
            $this->passphrase = $config_data['PRIVATEKEYPASS'];

        if(isset($config_data['PROXY']))
            $this->proxy = $config_data['PROXY'];

        if(isset($config_data['PROXYACQURL']))
            $this->proxyUrl = $config_data['PROXYACQURL'];

        if(isset($config_data['LOGFILE']))
            $this->logFile = $config_data['LOGFILE'];

        if(isset($config_data['TRACELEVEL']))
        {
            $level = $config_data['TRACELEVEL'];

            if ($level === "DEBUG")
                $this->logLevel = LogLevel::Debug;
            else if ($level === "ERROR")
                $this->logLevel = LogLevel::Error;
                $this->logLevelString = $level;
        }
        
        if ($defaults)
        {
            if(!isset($config_data['EXPIRATIONPERIOD']))
                    $this->expirationPeriod = 60;
            
            if(!isset($config_data['ACQUIRERTIMEOUT']))
                    $this->timeout = 10;
            
            if(!isset($config_data['SUBID']))
                $this->subID = 0;
            
            if(!isset($config_data['TRACELEVEL']))
                $this->logLevel = LogLevel::Error;
            
            if(isset($config_data['LOGFILE']))
                $this->logFile = "logs/connector.log";
        }
    }

    public function getAcquirerCertificatePath()
    {
        return $this->acquirerCertificate;
    }

    public function getCertificatePath()
    {
        return $this->certificate;
    }

    public function getExpirationPeriod()
    {
        return $this->expirationPeriod;
    }

    public function getMerchantID()
    {
        return $this->merchantID;
    }

    public function getPassphrase()
    {
        return $this->passphrase;
    }

    public function getPrivateKeyPath()
    {
        return $this->privateKey;
    }

    public function getMerchantReturnURL()
    {
        return $this->returnURL;
    }

    public function getSubID()
    {
        return $this->subID;
    }

    public function getAcquirerTimeout()
    {
        return $this->timeout;
    }

    public function getAcquirerDirectoryURL()
    {
        return $this->acquirerDirectoryURL;
    }

    public function getAcquirerStatusURL()
    {
        return $this->acquirerStatusURL;
    }

    public function getAcquirerTransactionURL()
    {
        return $this->acquirerTransactionURL;
    }


    /**
     * @return string
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @return string
     */
    public function getProxyUrl()
    {
        return $this->proxyUrl;
    }

    /**
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * @return int
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }

    /**
     * @return string
     */
    public function getExpirationPeriodString()
    {
        return $this->expirationPeriodString;
    }
    
    /**
     * @return string
     */
    public function getSubIDString() {
        return $this->subIDString;
    }

    /**
     * @return string
     */
    public function getTimeoutString() {
        return $this->timeoutString;
    }

    /**
     * @return string
     */
    public function getLogLevelString() {
        return $this->logLevelString;
    }    
}
