<?php
namespace iDEALConnector\Entities;

use InvalidArgumentException;
use DateTime;

require_once("Country.php");

/**
 * The DirectoryResponse object received from the directory request call.
 */
class DirectoryResponse extends AbstractResponse
{
    private $directoryDate;
    private $acquirerID;
    private $countries;

    /**
     * @param DateTime $date
     * @param DateTime $directoryDate
     * @param string $acquirerID
     * @param Country[] $countries
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
     * @return Country[]
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
