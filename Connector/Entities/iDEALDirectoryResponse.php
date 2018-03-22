<?php
require_once("iDEALCountry.php");

/**
 * The DirectoryResponse object received from the directory request call.
 */
class iDEALDirectoryResponse extends iDEALAbstractResponse
{
    private $directoryDate;
    private $acquirerID;
    private $countries;

    /**
     * @param DateTime $date
     * @param DateTime $directoryDate
     * @param string $acquirerID
     * @param iDEALCountry[] $countries
     * @throws InvalidArgumentException
     */
    public function __construct(DateTime $date, DateTime $directoryDate, $acquirerID, $countries)
    {
        if(!is_string($acquirerID))
            throw new InvalidArgumentException("Parameter 'acquirerID' should be of type string.");

        if(!is_array($countries))
            throw new InvalidArgumentException("Parameter 'countries' should be an array.");

        parent::__construct($date);

        $this->directoryDate = $directoryDate;
        $this->acquirerID = $acquirerID;
        $this->countries = $countries;
    }

    /**
     * @return string
     */
    public function getAcquirerID()
    {
        return $this->acquirerID;
    }

    /**
     * @return iDEALCountry[]
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * @return DateTime
     */
    public function getDirectoryDate()
    {
        return $this->directoryDate;
    }

}
