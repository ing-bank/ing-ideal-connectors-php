<?php
namespace iDEALConnector\Entities;

require_once("Merchant.php");
require_once("AbstractRequest.php");
/**
 * The DirectoryRequest object used for the directory request call.
 */
class DirectoryRequest extends AbstractRequest
{
    private $merchant;

    /**
     * @param Merchant $merchant
     */
    public function __construct(Merchant $merchant)
    {
        parent::__construct();
        $this->merchant = $merchant;
    }

    /**
     * @return Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }
}
