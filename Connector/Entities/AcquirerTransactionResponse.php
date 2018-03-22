<?php
namespace iDEALConnector\Entities;
use DateTime;
use InvalidArgumentException;

/**
 *
 */
class AcquirerTransactionResponse extends AbstractResponse
{
    private $acquirerID;
    private $issuerAuthenticationURL;

    private $transactionID;
    private $transactionTimestamp;
    private $purchaseID;

    /**
     * @param string $acquirerID
     * @param string $issuerAuthenticationURL
     * @param string $purchaseID
     * @param string $transactionID
     * @param DateTime $transactionTimestamp
     * @param DateTime $createdTimestamp
     * @throws InvalidArgumentException
     */
    function __construct($acquirerID, $issuerAuthenticationURL, $purchaseID, $transactionID, DateTime $transactionTimestamp, DateTime $createdTimestamp)
    {
        parent::__construct($createdTimestamp);

        if(!is_string($acquirerID))
            throw new InvalidArgumentException("Parameter 'acquirerID' must be of type string.");

        if(!is_string($issuerAuthenticationURL))
            throw new InvalidArgumentException("Parameter 'issuerAuthenticationURL' must be of type string.");

        if(!is_string($purchaseID))
            throw new InvalidArgumentException("Parameter 'purchaseID' must be of type string.");

        if(!is_string($transactionID))
            throw new InvalidArgumentException("Parameter 'transactionID' must be of type string.");

        $this->acquirerID = $acquirerID;
        $this->issuerAuthenticationURL = $issuerAuthenticationURL;
        $this->purchaseID = $purchaseID;
        $this->transactionID = $transactionID;
        $this->transactionTimestamp = $transactionTimestamp;
    }

    /**
     * @return string
     */
    public function getPurchaseID()
    {
        return $this->purchaseID;
    }

    /**
     * @return string
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }

    /**
     * @return DateTime
     */
    public function getTransactionTimestamp()
    {
        return $this->transactionTimestamp;
    }

    /**
     * @return string
     */
    public function getAcquirerID()
    {
        return $this->acquirerID;
    }

    /**
     * @return string
     */
    public function getIssuerAuthenticationURL()
    {
        return $this->issuerAuthenticationURL;
    }


}
