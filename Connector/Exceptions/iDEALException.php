<?php
namespace iDEALConnector\Exceptions;

use ErrorException;

/**
 *  This exception occurs when the Acquirer returns an error message.
 */
class iDEALException extends ConnectorException
{
    private $errorCode;
    private $errorDetail;
    private $suggestedAction;
    private $consumerMessage;

    public function __construct($code, $message, $errorDetail = '', $suggestedAction = '', $consumerMessage = '')
    {
        if (!(is_string($code) && is_string($message) && is_string($consumerMessage)))
            throw new ErrorException("Wrong parameter types.");

        parent::__construct($message);

        $this->errorCode = $code;
        $this->errorDetail = $errorDetail;
        $this->suggestedAction = $suggestedAction;
        $this->consumerMessage = $consumerMessage;
    }

    public function getConsumerMessage()
    {
        return $this->consumerMessage;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getErrorDetail()
    {
        return $this->errorDetail;
    }

    public function getSuggestedAction()
    {
        return $this->suggestedAction;
    }

}
