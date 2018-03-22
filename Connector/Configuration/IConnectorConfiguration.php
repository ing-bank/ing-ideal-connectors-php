<?php
namespace iDEALConnector\Configuration;

/**
 *  Implement current interface to create custom configuration providers.
 */
interface IConnectorConfiguration
{
    /**
     * @return string
     */
    public function getAcquirerCertificatePath();

    /**
     * @return string
     */
    public function getAcquirerDirectoryURL();

    /**
     * @return string
     */
    public function getAcquirerStatusURL();

    /**
     * @return string
     */
    public function getAcquirerTransactionURL();

    /**
     * @return string
     */
    public function getCertificatePath();

    /**
     * @return int
     */
    public function getExpirationPeriod();

    /**
     * @return string
     */
    public function getMerchantID();

    /**
     * @return string
     */
    public function getPassphrase();

    /**
     * @return string
     */
    public function getPrivateKeyPath();

    /**
     * @return string
     */
    public function getMerchantReturnURL();

    /**
     * @return int
     */
    public function getSubID();

    /**
     * @return int
     */
    public function getAcquirerTimeout();

    /**
     * @return string
     */
    public function getProxy();

    /**
     * @return string
     */
    public function getProxyUrl();

    /**
     * @return string
     */
    public function getLogFile();

    /**
     * @return string
     */
    public function getLogLevel();
}
