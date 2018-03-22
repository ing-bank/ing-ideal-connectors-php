<?php
namespace iDEALConnector\Log;
use iDEALConnector\Entities\Merchant;
use iDEALConnector\Entities\AcquirerStatusResponse;
use iDEALConnector\Entities\AcquirerTransactionResponse;
use iDEALConnector\Entities\DirectoryResponse;
use iDEALConnector\Entities\DirectoryRequest;
use iDEALConnector\Entities\AcquirerStatusRequest;
use iDEALConnector\Entities\AcquirerTransactionRequest;
use iDEALConnector\Entities\Transaction;
use iDEALConnector\Exceptions\ValidationException;

/**
 *
 */
class EntityValidator
{

    public function validate($request)
    {
        $className = get_class($request);

        if ($className === "iDEALConnector\Entities\DirectoryRequest")
        {
            /* @var $request DirectoryRequest */
            $this->validateMerchant($request->getMerchant());
        }
        else if ($className === "iDEALConnector\Entities\AcquirerTransactionRequest")
        {
            /* @var $request AcquirerTransactionRequest */
            $this->validateAcquirerTransactionRequest($request);
        }
        else if ($className === "iDEALConnector\Entities\AcquirerStatusRequest")
        {
            /* @var $request AcquirerStatusRequest */
            $this->validateAcquirerStatusRequest($request);
        }
        else if ($className === "iDEALConnector\Entities\DirectoryResponse")
        {
            /* @var $request DirectoryResponse */
            $this->validateDirectoryResponse($request);
        }
        else if ($className === "iDEALConnector\Entities\AcquirerTransactionResponse")
        {
            /* @var $request AcquirerTransactionResponse */
            $this->validateAcquirerTransactionResponse($request);
        }
        else if ($className === "iDEALConnector\Entities\AcquirerStatusResponse")
        {
            /* @var $request AcquirerStatusResponse */
            $this->validateAcquirerStatusResponse($request);
        }
        else
            throw new ValidationException('Given object type could not be validated.');
    }

    private function validateAcquirerStatusRequest(AcquirerStatusRequest $input)
    {
        if(strlen($input->getTransactionID()) !== 16)
            throw new ValidationException("Transaction.transactionID length not 16.");

        $length = preg_match('/[0-9]+/', $input->getTransactionID(), $matches);
        if ($length !== 1 || $matches[0] !== $input->getTransactionID())
            throw new ValidationException("Transaction.transactionID does not match format.");

        $this->validateMerchant($input->getMerchant());
    }

    private function validateAcquirerTransactionRequest(AcquirerTransactionRequest $input)
    {

        $length = preg_match('/[A-Z]{6,6}[A-Z2-9][A-NP-Z0-9]([A-Z0-9]{3,3}){0,1}/', $input->getIssuerID(), $matches);
        if ($length !== 1 || $matches[0] !== $input->getIssuerID())
            throw new ValidationException("Issuer.issuerID does not match format.");

        $this->validateMerchant($input->getMerchant());
        $this->validateTransaction($input->getTransaction());
    }

    private function validateMerchant(Merchant $merchant)
    {
        if (strlen($merchant->getMerchantID()) !== 9)
            throw new ValidationException("Merchant.merchantID length is not 9");

        $length = preg_match('/[0-9]+/',$merchant->getMerchantID(), $matches);
        if ($length !== 1 || $matches[0] !== $merchant->getMerchantID())
            throw new ValidationException("Merchant.merchantID does not match format.");

        if ($merchant->getSubID() > 999999 || $merchant->getSubID() < 0)
            throw new ValidationException("Merchant.subID value must be between 0 and 999999.");

        if (strlen($merchant->getMerchantReturnURL()) > 512)
            throw new ValidationException("Merchant.merchantReturnURL length is to large.");

    }

    private function validateTransaction(Transaction $transaction){

        if ($transaction->getAmount() < 0 || $transaction->getAmount() >= 1000000000000)
            throw new ValidationException("Transaction.amount outside value range.");

        if($transaction->getCurrency() !== "EUR")
            throw new ValidationException("Transaction.currency does not match format.");

        if($transaction->getExpirationPeriod() < 1 || $transaction->getExpirationPeriod() > 60)
            throw new ValidationException("Transaction.expirationPeriod length outside range('PT1M', 'PT1H').");

        if(strlen($transaction->getLanguage()) !== 2)
            throw new ValidationException("Transaction.language length not 2.");

        if (strlen($transaction->getDescription()) < 1 || strlen($transaction->getDescription()) > 35)
            throw new ValidationException("Transaction.description length outside range(1, 35).");

        if(strlen($transaction->getEntranceCode()) < 1 || strlen($transaction->getEntranceCode()) > 40)
            throw new ValidationException("Transaction.entranceCode length outside range(1, 35).");

        $length = preg_match('/[a-z]+/', $transaction->getLanguage(), $matches);
        if ($length !== 1 || $matches[0] !== $transaction->getLanguage())
            throw new ValidationException("Transaction.language does not match format.");

        $length = preg_match('/[a-zA-Z0-9]+/',$transaction->getEntranceCode(), $matches);
        if ($length !== 1 || $matches[0] !== $transaction->getEntranceCode())
            throw new ValidationException("Transaction.entranceCode does not match format.");

        $length = preg_match('/[a-zA-Z0-9]+/',$transaction->getPurchaseId(), $matches);
        if ($length !== 1 || $matches[0] !== $transaction->getPurchaseId())
            throw new ValidationException("Transaction.purchaseId does not match format.");

    }

    private function validateDirectoryResponse(DirectoryResponse $response)
    {
        $i = 0;
        foreach ($response->getCountries() as $country)
        {
            $length = strlen($country->getCountryNames());

            if ($length < 1 || $length > 128)
                throw new ValidationException("Country.issuerID does not match format.");

            $j = 0;
            foreach ($country->getIssuers() as $issuer) {

                $length = preg_match('/[A-Z]{6,6}[A-Z2-9][A-NP-Z0-9]([A-Z0-9]{3,3}){0,1}/', $issuer->getId(), $matches);

                if ($length !== 1 || $matches[0] !== $issuer->getId())
                    throw new ValidationException("Country[$i].Issuer[$j].issuerID does not match format.");

                $length = strlen($issuer->getName());

                if ($length < 1 || $length > 35)
                    throw new ValidationException("Country[$i].Issuer[$j].issuerName does not match format.");

                $j++;
            }

            $i++;
        }
    }

    private function validateAcquirerTransactionResponse(AcquirerTransactionResponse $response)
    {
        $length = strlen ($response->getAcquirerID());

        if ($length !== 4)
            throw new ValidationException("Acquirer.acquirerID does not match length.");

        $length = preg_match('/[0-9]+/', $response->getAcquirerID(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getAcquirerID())
            throw new ValidationException("Acquirer.acquirerID does not match format.");


        $length = strlen ($response->getIssuerAuthenticationURL());

        if ($length > 512)
            throw new ValidationException("Issuer.issuerAuthenticationURL exceeds length.");

        $length = strlen ($response->getTransactionID());

        if ($length !== 16)
            throw new ValidationException("Transaction.transactionID exceeds length.");

        $length = preg_match('/[0-9]+/', $response->getTransactionID(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getTransactionID())
            throw new ValidationException("Transaction.transactionID does not match format.");

        $length = strlen ($response->getPurchaseID());
        if ($length < 1 || $length > 35)
            throw new ValidationException("Transaction.purchaseID length not in range(1,35).");

        $length = preg_match('/[a-zA-Z0-9]+/', $response->getPurchaseID(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getPurchaseID())
            throw new ValidationException("Transaction.purchaseID does not match format.");

    }

    private function validateAcquirerStatusResponse(AcquirerStatusResponse $response)
    {
        $length = strlen ($response->getAcquirerID());

        if ($length !== 4)
            throw new ValidationException("Acquirer.acquirerID does not match length.");

        $length = preg_match('/[0-9]+/', $response->getAcquirerID(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getAcquirerID())
            throw new ValidationException("Acquirer.acquirerID does not match format.");

        $length = strlen ($response->getTransactionID());

        if ($length !== 16)
            throw new ValidationException("Transaction.transactionID exceeds length.");

        $length = preg_match('/[0-9]+/', $response->getTransactionID(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getTransactionID())
            throw new ValidationException("Transaction.transactionID does not match format.");

        $length = preg_match('/Open|Success|Failure|Expired|Cancelled/', $response->getStatus(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getStatus())
            throw new ValidationException("Transaction.status does not match format.");
    }
}
