<?php
/**
 *  This exception occurs during security checks of transport messages.
 */
class iDEALSecurityException extends iDEALConnectorException
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
