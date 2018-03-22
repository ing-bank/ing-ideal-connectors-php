<?php
namespace iDEALConnector\Exceptions;
/**
 *  This exception occurs during security checks of transport messages.
 */
class SecurityException extends ConnectorException
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
