<?php
require_once("Configuration/IIdealConnectorConfiguration.php");
require_once("Configuration/iDEALDefaultConfiguration.php");
require_once("Log/IIdealConnectorLog.php");
require_once("Log/iDEALDefaultLog.php");
require_once("Log/iDEALLogLevel.php");

require_once("Entities/iDEALAbstractRequest.php");
require_once("Entities/iDEALDirectoryRequest.php");
require_once("Entities/iDEALAcquirerTransactionRequest.php");
require_once("Entities/iDEALAcquirerStatusRequest.php");

require_once("Entities/iDEALAbstractResponse.php");
require_once("Entities/iDEALDirectoryResponse.php");
require_once("Entities/iDEALAcquirerTransactionResponse.php");
require_once("Entities/iDEALAcquirerStatusResponse.php");

require_once('Exceptions/iDEALConnectorException.php');
require_once('Exceptions/iDEALException.php');
require_once('Exceptions/iDEALSerializationException.php');
require_once('Exceptions/iDEALSecurityException.php');
require_once('Exceptions/iDEALValidationException.php');

require_once("Validation/iDEALEntityValidator.php");

require_once("Xml/iDEALXmlSerializer.php");
require_once("Xml/iDEALXmlSecurity.php");

require_once("Http/WebRequest.php");

require_once("Libraries/xmlseclibs.php");

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
     * @param IIdealConnectorConfiguration $configuration An instance of a implementation of IConnectorConfiguration
     * @param IIdealConnectorLog $log An instance of a implementation of IConnectorLog
     */
    public function __construct(IIdealConnectorConfiguration $configuration, IIdealConnectorLog $log)
    {
        $this->log = $log;
        $this->configuration = $configuration;

        $this->serializer = new iDEALXmlSerializer();
        $this->signer = new iDEALXmlSecurity();
        $this->validator = new iDEALEntityValidator();

        $this->merchant = new iDEALMerchant($this->configuration->getMerchantID(), $this->configuration->getSubID(), $this->configuration->getMerchantReturnURL());
    }

    /**
     * This is a conveninence method to create an instance of iDEALConnector using the default implementations of IConnectorConfiguration and IConnector Log
     * @param string $configurationPath The path of your config.conf file
     * @return iDEALConnector
     */
    public static function getDefaultInstance($configurationPath)
    {
        $config = new iDEALDefaultConfiguration($configurationPath);
        return new  iDEALConnector($config, new iDEALDefaultLog($config->getLogLevel(),$config->getLogFile()));
    }


    /**
     * Get directory listing.
     *
     * @return iDEALDirectoryResponse
     * @throws iDEALSerializationException
     * @throws iDEALException
     * @throws iDEALValidationException
     * @throws iDEALSecurityException
     */
    public function getIssuers()
    {
        try{
            $request = new iDEALDirectoryRequest($this->merchant);

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
        catch(iDEALValidationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(iDEALSerializationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(iDEALSecurityException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
    }

    /**
     * Start a transaction.
     *
     * @param $issuerID
     * @param iDEALTransaction $transaction
     * @param null $merchantReturnUrl
     * @throws iDEALSerializationException
     * @throws iDEALException
     * @throws iDEALValidationException
     * @throws iDEALSecurityException
     * @return iDEALAcquirerTransactionResponse
     */
    public function startTransaction($issuerID, iDEALTransaction $transaction,  $merchantReturnUrl = null)
    {
        try{
            $merchant = $this->merchant;

            if (!is_null($merchantReturnUrl))
                $merchant = new iDEALMerchant($this->configuration->getMerchantID(), $this->configuration->getSubID(), $merchantReturnUrl);

            $request = new iDEALAcquirerTransactionRequest($issuerID, $merchant, $transaction);

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
        catch(iDEALValidationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(iDEALSerializationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(iDEALSecurityException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
    }

    /**
     * Get a transaction status.
     *
     * @param $transactionID
     * @throws iDEALSerializationException
     * @throws iDEALException
     * @throws iDEALValidationException
     * @throws iDEALSecurityException
     * @return iDEALAcquirerStatusResponse
     */
    public function getTransactionStatus($transactionID)
    {
        try{
            $request = new iDEALAcquirerStatusRequest($this->merchant, $transactionID);

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
        catch(iDEALValidationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(iDEALSerializationException $ex)
        {
            $this->log->logException($ex);
            throw $ex;
        }
        catch(iDEALSecurityException $ex)
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
          throw new iDEALSerializationException('Response was empty');

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($response);


        $verified = $this->signer->verify($doc, $this->configuration->getAcquirerCertificatePath());

        if (!$verified)
            throw new iDEALSecurityException('Response message signature check fails.');

        return $this->serializer->deserialize($doc);
    }
}

