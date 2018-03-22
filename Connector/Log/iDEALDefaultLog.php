<?php

class iDEALDefaultLog implements IIdealConnectorLog
{
    private $logPath;
    private $logLevel;

    function __construct($logLevel, $logPath)
    {
        $this->logLevel = $logLevel;
        $this->logPath = $logPath;
    }

    public function logAPICall($method, iDEALAbstractRequest $request)
    {
        if ($this->logLevel === 0)
            $this->log("Entering[".$method."]", $request);
    }

    public function logAPIReturn($method, iDEALAbstractResponse $response)
    {
        if ($this->logLevel === 0)
            $this->log("Exiting[".$method."]", $response);
    }

    public function logRequest($xml)
    {
        if ($this->logLevel === 0)
            $this->log("Request", $xml);
    }

    public function logResponse($xml)
    {
        if ($this->logLevel === 0)
            $this->log("Response", $xml);
    }

    public function logErrorResponse(iDEALException $exception)
    {
        $this->log("ErrorResponse", $exception);
    }

    public function logException(iDEALConnectorException $exception)
    {
        $this->log("Exception", $exception);
    }

    private function log($message, $value)
    {
        $now = new DateTime();

        file_put_contents($this->logPath, $now->format('Y-m-d H:i:s').' '.$message."\n".serialize($value)."\n\n", FILE_APPEND);
    }
}
