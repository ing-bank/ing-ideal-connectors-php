<?php

/**
 *  Implement this interface to get access to log messages at transport level.
 */
interface IIdealConnectorLog
{

    /**
     * @param iDEALException $exception
     * @return
     */
    public function logErrorResponse(iDEALException $exception);

    /**
     * @param iDEALConnectorException $exception
     */
    public function logException(iDEALConnectorException $exception);

    /**
     * @param string $method
     * @param iDEALAbstractRequest $request
     * @return
     */
    public function logAPICall($method, iDEALAbstractRequest $request);

    /**
     * @param string $method
     * @param iDEALAbstractResponse $response
     * @return
     */
    public function logAPIReturn($method, iDEALAbstractResponse $response);

    /**
     * @param string $xml
     * @return
     */
    public function logRequest($xml);

    /**
     * @param string $xml
     * @return
     */
    public function logResponse($xml);
}
