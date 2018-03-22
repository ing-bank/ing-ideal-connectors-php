<?php
namespace iDEALConnector;
require_once("Configuration/IConnectorConfiguration.php");
require_once("Configuration/DefaultConfiguration.php");
require_once("Log/IConnectorLog.php");
require_once("Log/DefaultLog.php");
require_once("Log/LogLevel.php");

require_once("Entities/AbstractRequest.php");
require_once("Entities/DirectoryRequest.php");
require_once("Entities/AcquirerTransactionRequest.php");
require_once("Entities/AcquirerStatusRequest.php");

require_once("Entities/AbstractResponse.php");
require_once("Entities/DirectoryResponse.php");
require_once("Entities/AcquirerTransactionResponse.php");
require_once("Entities/AcquirerStatusResponse.php");

require_once('Exceptions/ConnectorException.php');
require_once('Exceptions/iDEALException.php');
require_once('Exceptions/SerializationException.php');
require_once('Exceptions/SecurityException.php');
require_once('Exceptions/ValidationException.php');

require_once("Validation/EntityValidator.php");

require_once("Xml/XmlSerializer.php");
require_once("Xml/XmlSecurity.php");

require_once("Http/WebRequest.php");

require_once("Libraries/xmlseclibs.php");

use DOMDocument;

use iDEALConnector\Configuration\IConnectorConfiguration;
use iDEALConnector\Configuration\DefaultConfiguration;

use iDEALConnector\Log\IConnectorLog;
use iDEALConnector\Log\EntityValidator;
use iDEALConnector\Log\DefaultLog;

use iDEALConnector\Exceptions\iDEALException;
use iDEALConnector\Exceptions\ValidationException;
use iDEALConnector\Exceptions\SerializationException;
use iDEALConnector\Exceptions\SecurityException;

use iDEALConnector\Xml\XmlSerializer;
use iDEALConnector\Xml\XmlSecurity;

use iDEALConnector\Http\WebRequest;

use iDEALConnector\Entities\AcquirerStatusRequest;
use iDEALConnector\Entities\DirectoryRequest;
use iDEALConnector\Entities\AcquirerTransactionRequest;
use iDEALConnector\Entities\Transaction;
use iDEALConnector\Entities\Merchant;

/**
 *  iDEALConnector Library v2.0
 */
class iDEALConnector
{
    private $serializer;
    private $signer;
    private $validator;
    private $configuration;
    private $log;
    private $merchant;

        /**
     * Constructs an instance of iDEALConnector.
     *
     * @param IConnectorConfiguration $configuration An instance of a implementation of IConnectorConfiguration
     * @param IConnectorLog $log An instance of a implementation of IConnectorLog
     */
    public function __construct(IConnectorConfiguration $configuration, IConnectorLog $log)
    {
        $this->log = $log;
        $this->configuration = $configuration;

        $this->serializer = new XmlSerializer();
        $this->signer = new XmlSecurity();
        $this->validator = new EntityValidator();

        $this->merchant = new Merchant($this->configuration->getMerchantID(), $this->configuration->getSubID(), $this->configuration->getMerchantReturnURL());
    }

    /**
     * This is a conveninence method to create an instance of iDEALConnector using the default implementations of IConnectorConfiguration and IConnector Log
     * @param string $configurationPath The path of your config.conf file
     * @return iDEALConnector
     */
    public static function getDefaultInstance($configurationPath)
    {
        $config = new DefaultConfiguration($configurationPath);
        return new  iDEALConnector($config, new DefaultLog($config->getLogLevel(),$config->getLogFile()));
    }


    /**
     * Get directory listing.
     *
     * @return Entities\DirectoryResponse
     * @throws Exceptions\SerializationException
     * @throws Exceptions\iDEALException
     * @throws Exceptions\ValidationException
     * @throws Exceptions\SecurityException
     */
    public function getIssuers()
    {
        try{
            $request = new DirectoryRequest($this->merchant);

            $this->log->logAPICall("getIssuers()", $request);
            $this->validator->validate($request);

            $response = $this->sendRequest($request, $this->configuration->getAcquirerDirectoryURL());

            $this->validator->validate($response);
            $this->log->logAPIReturn("getIssuers()", $response);

            return $response;
        }
        catch(iDEALException $ex)
        {
            $this->log->logErrorResponse($ex);
            throw $ex;
        }
        catch(ValidationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(SerializationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(SecurityException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
    }

    /**
     * Start a transaction.
     *
     * @param $issuerID
     * @param Entities\Transaction $transaction
     * @param null $merchantReturnUrl
     * @throws Exceptions\SerializationException
     * @throws Exceptions\iDEALException
     * @throws Exceptions\ValidationException
     * @throws Exceptions\SecurityException
     * @return Entities\AcquirerTransactionResponse
     */
    public function startTransaction($issuerID, Transaction $transaction,  $merchantReturnUrl = null)
    {
        try{
            $merchant = $this->merchant;

            if (!is_null($merchantReturnUrl))
                $merchant = new Merchant($this->configuration->getMerchantID(), $this->configuration->getSubID(), $merchantReturnUrl);

            $request = new AcquirerTransactionRequest($issuerID, $merchant, $transaction);

            $this->log->logAPICall("startTransaction()", $request);
            $this->validator->validate($request);

            $response = $this->sendRequest($request, $this->configuration->getAcquirerTransactionURL());

            $this->validator->validate($response);
            $this->log->logAPIReturn("startTransaction()", $response);

            return $response;
        }
        catch(iDEALException $iex)
        {
            $this->log->logErrorResponse($iex);
            throw $iex;
        }
        catch(ValidationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(SerializationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(SecurityException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
    }

    /**
     * Get a transaction status.
     *
     * @param $transactionID
     * @throws Exceptions\SerializationException
     * @throws Exceptions\iDEALException
     * @throws Exceptions\ValidationException
     * @throws Exceptions\SecurityException
     * @return Entities\AcquirerStatusResponse
     */
    public function getTransactionStatus($transactionID)
    {
        try{
            $request = new AcquirerStatusRequest($this->merchant, $transactionID);

            $this->log->logAPICall("startTransaction()", $request);
            $this->validator->validate($request);

            $response = $this->sendRequest($request, $this->configuration->getAcquirerStatusURL());

            $this->validator->validate($response);
            $this->log->logAPIReturn("startTransaction()", $response);

            return $response;
        }
        catch(iDEALException $iex)
        {
            $this->log->logErrorResponse($iex);
            throw $iex;
        }
        catch(ValidationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(SerializationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(SecurityException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
    }
    
    /*
     * Returns the assigned configuration.
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    private function sendRequest($request, $url)
    {
        $xml = $this->serializer->serialize($request);

        $this->signer->sign(
            $xml,
            $this->configuration->getCertificatePath(),
            $this->configuration->getPrivateKeyPath(),
            $this->configuration->getPassphrase()
        );

        $request = $xml->saveXML();

        $this->log->logRequest($request);

        if(!is_null($this->configuration->getProxy()))
            $response = WebRequest::post($url, $request, $this->configuration->getProxy());
        else
            $response = WebRequest::post($url, $request);

        $this->log->logResponse($response);
            
        if(empty($response))
          throw new SerializationException('Response was empty');

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($response);


        $verified = $this->signer->verify($doc, $this->configuration->getAcquirerCertificatePath());

        if (!$verified)
            throw new SecurityException('Response message signature check fails.');

        return $this->serializer->deserialize($doc);
    }
}

