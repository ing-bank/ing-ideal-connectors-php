<?php
namespace iDEALConnector\Entities;

use InvalidArgumentException;
/**
 *
 */
class AcquirerStatusRequest extends AbstractRequest
{
    private $merchant;
    private $transactionID;

    /**
     * @param Merchant $merchant
     * @param string $transactionID
     */
    public function __construct(Merchant $merchant, $transactionID)
    {
        if(!is_string($transactionID))
            throw new InvalidArgumentException("Parameter 'transactionID' must be of type string.");
     
        parent::__construct();
   
        $this->merchant = $merchant;
        $this->transactionID = $transactionID;
    }

    /**
     * @return Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @return string
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }
}
