<?php
/**
 *
 */
class iDEALAcquirerStatusRequest extends iDEALAbstractRequest
{
    private $merchant;
    private $transactionID;

    /**
     * @param iDEALMerchant $merchant
     * @param string $transactionID
     * @throws InvalidArgumentException
     */
    public function __construct(iDEALMerchant $merchant, $transactionID)
    {
        if(!is_string($transactionID))
            throw new InvalidArgumentException("Parameter 'transactionID' must be of type string.");
     
        parent::__construct();
   
        $this->merchant = $merchant;
        $this->transactionID = $transactionID;
    }

    /**
     * @return iDEALMerchant
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
