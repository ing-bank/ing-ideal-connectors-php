<?php
namespace iDEALConnector\Entities;

use InvalidArgumentException;
use DateTime;

/**
 *
 */
class AcquirerStatusResponse extends AbstractResponse
{
    private $acquirerID;
    private $transactionID;
    private $status;
    private $statusTimestamp;
    private $consumerName;
    private $consumerIBAN;
    private $consumerBIC;
    private $amount;
    private $currency;

    /**
     * @param string $acquirerID
     * @param float $amount
     * @param string $consumerBIC
     * @param string $consumerIBAN
     * @param string $consumerName
     * @param DateTime $createdTimestamp
     * @param string $currency
     * @param string $status
     * @param DateTime $statusTimestamp
     * @param string $transactionID
     */
    function __construct($acquirerID, $amount, $consumerBIC, $consumerIBAN, $consumerName, DateTime $createdTimestamp, $currency, $status, DateTime $statusTimestamp, $transactionID)
    {
        parent::__construct($createdTimestamp);

        if(!is_string($acquirerID))
            throw new InvalidArgumentException("Parameter 'acquirerID' must be of type string.");

        if(!is_string($transactionID))
            throw new InvalidArgumentException("Parameter 'transactionID' must be of type string.");

        if(!is_string($status))
            throw new InvalidArgumentException("Parameter 'status' must be of type string.");
        
        if ($status == "Success")
        {
            if(!is_float($amount))
                throw new InvalidArgumentException("Parameter 'amount' must be of type float.");

            if(!is_string($consumerBIC))
                throw new InvalidArgumentException("Parameter 'consumerBIC' must be of type string.");

            if(!is_string($consumerName))
                throw new InvalidArgumentException("Parameter 'consumerName' must be of type string.");

            if(!is_string($consumerIBAN))
                throw new InvalidArgumentException("Parameter 'consumerIBAN' must be of type string.");

            if(!is_string($currency))
                throw new InvalidArgumentException("Parameter 'currency' must be of type string.");
        }

        $this->acquirerID = $acquirerID;
        $this->amount = $amount;
        $this->consumerBIC = $consumerBIC;
        $this->consumerIBAN = $consumerIBAN;
        $this->consumerName = $consumerName;
        $this->currency = $currency;
        $this->status = $status;
        $this->statusTimestamp = $statusTimestamp;
        $this->transactionID = $transactionID;
    }

    /**
     * @return string
     */
    public function getAcquirerID()
    {
        return $this->acquirerID;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getConsumerBIC()
    {
        return $this->consumerBIC;
    }

    /**
     * @return string
     */
    public function getConsumerIBAN()
    {
        return $this->consumerIBAN;
    }

    /**
     * @return string
     */
    public function getConsumerName()
    {
        return $this->consumerName;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getStatusTimestamp()
    {
        return $this->statusTimestamp;
    }

    /**
     * @return string
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }


}
