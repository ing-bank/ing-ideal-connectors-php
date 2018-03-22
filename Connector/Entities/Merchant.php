<?php
namespace iDEALConnector\Entities;

use InvalidArgumentException;
/**
 *  The Merchant description.
 */
class Merchant
{
    private $merchantID;
    private $subID;
    private $merchantReturnURL;

    /**
     * @param string $merchantID
     * @param int $subID
     * @param string $merchantReturnURL
     */
    public function __construct($merchantID, $subID, $merchantReturnURL)
    {
        if(!is_string($merchantID))
            throw new InvalidArgumentException("Parameter 'merchantID' must be of type string.");
        
        if(!is_int($subID))
            throw new InvalidArgumentException("Parameter 'subID' must be of type int.");

        $this->merchantID = $merchantID;
        $this->merchantReturnURL = $merchantReturnURL;
        $this->subID = $subID;
    }

    /**
     * @return string
     */
    public function getMerchantID()
    {
        return $this->merchantID;
    }

    /**
     * @return int
     */
    public function getSubID()
    {
        return $this->subID;
    }

    /**
     * @return string
     */
    public function getMerchantReturnURL()
    {
        return $this->merchantReturnURL;
    }
}
