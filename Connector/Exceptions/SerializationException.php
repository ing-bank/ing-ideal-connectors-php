<?php
namespace iDEALConnector\Exceptions;


/**
 *  This exception occurs during the serialization of entities.
 */
class SerializationException extends ConnectorException
{
    private $xml;

    function __construct($message)
    {
        parent::__construct($message);
    }

    public function getXml()
    {
        return $this->xml;
    }

}
