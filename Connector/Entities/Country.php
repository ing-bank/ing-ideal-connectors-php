<?php
namespace iDEALConnector\Entities;

use InvalidArgumentException;

require_once("Issuer.php");

/**
 *  The Country class specific to the directoryResponse.
 */
class Country
{
    private $countryNames;
    private $issuers;

    /**
     * @param string $countryNames
     * @param Issuer[] $issuers
     * @throws InvalidArgumentException
     */
    public function __construct($countryNames, $issuers)
    {
        if(!is_array($issuers))
            throw new InvalidArgumentException("Parameter 'issuers' must be array.");

        if(!is_string($countryNames))
            throw new InvalidArgumentException("Parameter 'countryNames' must be of type string.");

        $this->countryNames = $countryNames;
        $this->issuers = $issuers;
    }

    /**
     * @return string
     */
    public function getCountryNames()
    {
        return $this->countryNames;
    }

    /**
     * @return Issuer[]
     */
    public function getIssuers()
    {
        return $this->issuers;
    }

}
