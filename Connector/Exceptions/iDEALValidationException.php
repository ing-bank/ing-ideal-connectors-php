<?php

/**
 *  This exception occurs during validation of entities.
 */
class iDEALValidationException extends iDEALConnectorException
{
    function __construct($message)
    {
        parent::__construct($message);
    }
}