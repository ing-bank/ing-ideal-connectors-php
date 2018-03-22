<?php
namespace iDEALConnector\Entities;

use DateTime;

/**
 * The abstract used for all response objects.
 */
class AbstractResponse
{
    private $createDateTimestamp;

    /**
     * @param DateTime $createDateTimestamp
     */
    function __construct(DateTime $createDateTimestamp)
    {
        $this->createDateTimestamp = $createDateTimestamp;
    }

    /**
     * @return DateTime
     */
    public function getCreateDateTimestamp()
    {
        return $this->createDateTimestamp;
    }

}
