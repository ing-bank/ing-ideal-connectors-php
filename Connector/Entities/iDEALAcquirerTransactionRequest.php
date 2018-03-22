<?php
require_once("iDEALTransaction.php");
/**
 *
 */
class iDEALAcquirerTransactionRequest extends iDEALAbstractRequest
{
    private $issuerID;
    private $merchant;
    private $transaction;

    /**
     * @param string $issuerID
     * @param iDEALMerchant $merchant
     * @param iDEALTransaction $transaction
     */
    public function __construct($issuerID, iDEALMerchant $merchant, iDEALTransaction $transaction)
    {
        parent::__construct();

        $this->issuerID = $issuerID;
        $this->merchant = $merchant;
        $this->transaction = $transaction;
    }

    /**
     * @return string
     */
    public function getIssuerID()
    {
        return $this->issuerID;
    }

    /**
     * @return iDEALMerchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @return iDEALTransaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
