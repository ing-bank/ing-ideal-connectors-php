<?php
namespace iDEALConnector\Entities;

use InvalidArgumentException;

/**
 * The Transaction description as found in the startTransaction request.
 */
class Transaction
{
    private $purchaseId;
    private $amount;
    private $currency;
    private $expirationPeriod;
    private $language;
    private $description;
    private $entranceCode;

    /**
     * @param float $amount
     * @param string $description
     * @param string $entranceCode
     * @param int $expirationPeriod
     * @param string $purchaseID
     * @param string $currency
     * @param string $language
     * @throws InvalidArgumentException
     */
    function __construct($amount, $description, $entranceCode, $expirationPeriod, $purchaseID, $currency = 'EUR', $language = 'nl')
    {
        if(!is_float($amount))
            throw new InvalidArgumentException("Parameter 'amount' must be of type decimal.");

        if(!is_string($description))
            throw new InvalidArgumentException("Parameter 'description' must be of type string.");

        if(!is_string($entranceCode))
            throw new InvalidArgumentException("Parameter 'entranceCode' must be of type string.");

        if(!is_int($expirationPeriod))
            throw new InvalidArgumentException("Parameter 'expirationPeriod' must be of type int.");

        if(!is_string($language))
            throw new InvalidArgumentException("Parameter 'language' must be of type string.");

        if(!is_string($purchaseID))
            throw new InvalidArgumentException("Parameter 'purchaseId' must be of type string.");

        $this->amount = $amount;
        $this->currency = $currency;
        $this->description = $description;
        $this->entranceCode = $entranceCode;
        $this->expirationPeriod = $expirationPeriod;
        $this->language = $language;
        $this->purchaseId = $purchaseID;
    }

    /**
     * Amount
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Currency
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Entrance code
     * @return string
     */
    public function getEntranceCode()
    {
        return $this->entranceCode;
    }

    /**
     * Expiration period
     * @return int
     */
    public function getExpirationPeriod()
    {
        return $this->expirationPeriod;
    }

    /**
     * Language
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Purchase number
     * @return string
     */
    public function getPurchaseId()
    {
        return $this->purchaseId;
    }
}
