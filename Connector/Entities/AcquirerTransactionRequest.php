<?php
namespace iDEALConnector\Entities;


require_once("Transaction.php");
/**
 *
 */
class AcquirerTransactionRequest extends AbstractRequest
{
    private $issuerID;
    private $merchant;
    private $transaction;

    /**
     * @param string $issuerID
     * @param Merchant $merchant
     * @param Transaction $transaction
     */
    public function __construct($issuerID, Merchant $merchant, Transaction $transaction)
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
     * @return Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
