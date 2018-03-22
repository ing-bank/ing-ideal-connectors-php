<?php
namespace iDEALConnector\Exceptions;


/**
 *  This exception occurs during validation of entities.
 */
class ValidationException extends ConnectorException
{
    function __construct($message)
    {
        parent::__construct($message);
    }
}