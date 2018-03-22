<?php

/**
 *
 */
class iDEALEntityValidator
{

    public function validate($request)
    {
        $className = get_class($request);

        if ($className === "iDEALDirectoryRequest")
        {
            /** @var $request iDEALDirectoryRequest */
            $this->validateMerchant($request->getMerchant());
        }
        else if ($className === "iDEALAcquirerTransactionRequest")
        {
            /** @var $request iDEALAcquirerTransactionRequest */
            $this->validateAcquirerTransactionRequest($request);
        }
        else if ($className === "iDEALAcquirerStatusRequest")
        {
            /** @var $request iDEALAcquirerStatusRequest */
            $this->validateAcquirerStatusRequest($request);
        }
        else if ($className === "iDEALDirectoryResponse")
        {
            /** @var $request iDEALDirectoryResponse */
            $this->validateDirectoryResponse($request);
        }
        else if ($className === "iDEALAcquirerTransactionResponse")
        {
            /** @var $request iDEALAcquirerTransactionResponse */
            $this->validateAcquirerTransactionResponse($request);
        }
        else if ($className === "iDEALAcquirerStatusResponse")
        {
            /** @var $request iDEALAcquirerStatusResponse */
            $this->validateAcquirerStatusResponse($request);
        }
        else
            throw new iDEALValidationException('Given object type could not be validated.');
    }

    private function validateAcquirerStatusRequest(iDEALAcquirerStatusRequest $input)
    {
        if(strlen($input->getTransactionID()) !== 16)
            throw new iDEALValidationException("Transaction.transactionID length not 16.");

        $length = preg_match('/[0-9]+/', $input->getTransactionID(), $matches);
        if ($length !== 1 || $matches[0] !== $input->getTransactionID())
            throw new iDEALValidationException("Transaction.transactionID does not match format.");

        $this->validateMerchant($input->getMerchant());
    }

    private function validateAcquirerTransactionRequest(iDEALAcquirerTransactionRequest $input)
    {

        $length = preg_match('/[A-Z]{6,6}[A-Z2-9][A-NP-Z0-9]([A-Z0-9]{3,3}){0,1}/', $input->getIssuerID(), $matches);
        if ($length !== 1 || $matches[0] !== $input->getIssuerID())
            throw new iDEALValidationException("Issuer.issuerID does not match format.");

        $this->validateMerchant($input->getMerchant());
        $this->validateTransaction($input->getTransaction());
    }

    private function validateMerchant(iDEALMerchant $merchant)
    {
        if (strlen($merchant->getMerchantID()) !== 9)
            throw new iDEALValidationException("Merchant.merchantID length is not 9");

        $length = preg_match('/[0-9]+/',$merchant->getMerchantID(), $matches);
        if ($length !== 1 || $matches[0] !== $merchant->getMerchantID())
            throw new iDEALValidationException("Merchant.merchantID does not match format.");

        if ($merchant->getSubID() > 999999 || $merchant->getSubID() < 0)
            throw new iDEALValidationException("Merchant.subID value must be between 0 and 999999.");

        if (strlen($merchant->getMerchantReturnURL()) > 512)
            throw new iDEALValidationException("Merchant.merchantReturnURL length is to large.");

    }

    private function validateTransaction(iDEALTransaction $transaction){

        if ($transaction->getAmount() < 0 || $transaction->getAmount() >= 1000000000000)
            throw new iDEALValidationException("Transaction.amount outside value range.");

        if($transaction->getCurrency() !== "EUR")
            throw new iDEALValidationException("Transaction.currency does not match format.");

        if($transaction->getExpirationPeriod() < 1 || $transaction->getExpirationPeriod() > 60)
            throw new iDEALValidationException("Transaction.expirationPeriod length outside range('PT1M', 'PT1H').");

        if(strlen($transaction->getLanguage()) !== 2)
            throw new iDEALValidationException("Transaction.language length not 2.");

        if (strlen($transaction->getDescription()) < 1 || strlen($transaction->getDescription()) > 35)
            throw new iDEALValidationException("Transaction.description length outside range(1, 35).");

        if(strlen($transaction->getEntranceCode()) < 1 || strlen($transaction->getEntranceCode()) > 40)
            throw new iDEALValidationException("Transaction.entranceCode length outside range(1, 35).");

        $length = preg_match('/[a-z]+/', $transaction->getLanguage(), $matches);
        if ($length !== 1 || $matches[0] !== $transaction->getLanguage())
            throw new iDEALValidationException("Transaction.language does not match format.");

        $length = preg_match('/[a-zA-Z0-9]+/',$transaction->getEntranceCode(), $matches);
        if ($length !== 1 || $matches[0] !== $transaction->getEntranceCode())
            throw new iDEALValidationException("Transaction.entranceCode does not match format.");

        $length = preg_match('/[a-zA-Z0-9]+/',$transaction->getPurchaseId(), $matches);
        if ($length !== 1 || $matches[0] !== $transaction->getPurchaseId())
            throw new iDEALValidationException("Transaction.purchaseId does not match format.");

    }

    private function validateDirectoryResponse(iDEALDirectoryResponse $response)
    {
        $i = 0;
        /** @var $country iDEALCountry */
        foreach ($response->getCountries() as $country)
        {
            $length = strlen($country->getCountryNames());

            if ($length < 1 || $length > 128)
                throw new iDEALValidationException("Country.issuerID does not match format.");

            $j = 0;
            /** @var $issuer iDEALIssuer */
            foreach ($country->getIssuers() as $issuer) {

                $length = preg_match('/[A-Z]{6,6}[A-Z2-9][A-NP-Z0-9]([A-Z0-9]{3,3}){0,1}/', $issuer->getId(), $matches);

                if ($length !== 1 || $matches[0] !== $issuer->getId())
                    throw new iDEALValidationException("Country[$i].Issuer[$j].issuerID does not match format.");

                $length = strlen($issuer->getName());

                if ($length < 1 || $length > 35)
                    throw new iDEALValidationException("Country[$i].Issuer[$j].issuerName does not match format.");

                $j++;
            }

            $i++;
        }
    }

    private function validateAcquirerTransactionResponse(iDEALAcquirerTransactionResponse $response)
    {
        $length = strlen ($response->getAcquirerID());

        if ($length !== 4)
            throw new iDEALValidationException("Acquirer.acquirerID does not match length.");

        $length = preg_match('/[0-9]+/', $response->getAcquirerID(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getAcquirerID())
            throw new iDEALValidationException("Acquirer.acquirerID does not match format.");


        $length = strlen ($response->getIssuerAuthenticationURL());

        if ($length > 512)
            throw new iDEALValidationException("Issuer.issuerAuthenticationURL exceeds length.");

        $length = strlen ($response->getTransactionID());

        if ($length !== 16)
            throw new iDEALValidationException("Transaction.transactionID exceeds length.");

        $length = preg_match('/[0-9]+/', $response->getTransactionID(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getTransactionID())
            throw new iDEALValidationException("Transaction.transactionID does not match format.");

        $length = strlen ($response->getPurchaseID());
        if ($length < 1 || $length > 35)
            throw new iDEALValidationException("Transaction.purchaseID length not in range(1,35).");

        $length = preg_match('/[a-zA-Z0-9]+/', $response->getPurchaseID(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getPurchaseID())
            throw new iDEALValidationException("Transaction.purchaseID does not match format.");

    }

    private function validateAcquirerStatusResponse(iDEALAcquirerStatusResponse $response)
    {
        $length = strlen ($response->getAcquirerID());

        if ($length !== 4)
            throw new iDEALValidationException("Acquirer.acquirerID does not match length.");

        $length = preg_match('/[0-9]+/', $response->getAcquirerID(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getAcquirerID())
            throw new iDEALValidationException("Acquirer.acquirerID does not match format.");

        $length = strlen ($response->getTransactionID());

        if ($length !== 16)
            throw new iDEALValidationException("Transaction.transactionID exceeds length.");

        $length = preg_match('/[0-9]+/', $response->getTransactionID(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getTransactionID())
            throw new iDEALValidationException("Transaction.transactionID does not match format.");

        $length = preg_match('/Open|Success|Failure|Expired|Cancelled/', $response->getStatus(), $matches);
        if ($length !== 1 || $matches[0] !== $response->getStatus())
            throw new iDEALValidationException("Transaction.status does not match format.");
    }
}
