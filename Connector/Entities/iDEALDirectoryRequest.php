<?php
require_once("iDEALMerchant.php");
require_once("iDEALAbstractRequest.php");
/**
 * The DirectoryRequest object used for the directory request call.
 */
class iDEALDirectoryRequest extends iDEALAbstractRequest
{
    private $merchant;

    /**
     * @param iDEALMerchant $merchant
     */
    public function __construct(iDEALMerchant $merchant)
    {
        parent::__construct();
        $this->merchant = $merchant;
    }

    /**
     * @return iDEALMerchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }
}
