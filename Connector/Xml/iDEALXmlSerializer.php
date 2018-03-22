<?php

class iDEALXmlSerializer
{

    ///Serialize///

    public function serialize(iDEALAbstractRequest $input)
    {
        $doc = new DOMDocument('1.0', 'utf-8');

        $className = get_class($input);

        if ($className === "iDEALDirectoryRequest")
        {
            $element = $doc->createElement("DirectoryReq");
            $this->serializeAbstractRequest($element, $input);

            /* @var $input iDEALDirectoryRequest */
            $this->serializeDirectoryRequest($element, $input);
            $doc->appendChild($element);
        }
        else if ($className === "iDEALAcquirerTransactionRequest")
        {
            $element = $doc->createElement("AcquirerTrxReq");
            $this->serializeAbstractRequest($element, $input);

            /* @var $input iDEALAcquirerTransactionRequest */
            $this->serializeAcquirerTransactionRequest($element, $input);
            $doc->appendChild($element);
        }
        else if ($className === "iDEALAcquirerStatusRequest")
        {
            $element = $doc->createElement("AcquirerStatusReq");
            $this->serializeAbstractRequest($element, $input);

            /* @var $input iDEALAcquirerStatusRequest */
            $this->serializeAcquirerStatusRequest($element, $input);
            $doc->appendChild($element);
        }
        else
            throw new iDEALSerializationException('Given object type could not be serialized.');

        return $doc;
    }

    private function serializeAbstractRequest(DOMElement $element, iDEALAbstractRequest $request)
    {
        $element->appendChild(new DOMElement("createDateTimestamp", $request->getCreateDateTimestamp()->format("Y-m-d\TH:i:s\Z")));
        $element->setAttribute("version", $request->getVersion());

        $element->setAttribute("xmlns", "http://www.idealdesk.com/ideal/messages/mer-acq/3.3.1");
    }

    private function serializeDirectoryRequest(DOMElement $element, iDEALDirectoryRequest $request)
    {
        $merchant = $element->ownerDocument->createElement("Merchant");
        $this->serializeMerchant($merchant, $request->getMerchant());
        $element->appendChild($merchant);
    }

    private function serializeAcquirerTransactionRequest(DOMElement $element, iDEALAcquirerTransactionRequest $request)
    {
        $item = $element->ownerDocument->createElement("Issuer");
        $item->appendChild(new DOMElement("issuerID", $request->getIssuerID()));
        $element->appendChild($item);

        $item = $element->ownerDocument->createElement("Merchant");
        $this->serializeMerchant($item, $request->getMerchant(), true);
        $element->appendChild($item);

        $item = $element->ownerDocument->createElement("Transaction");
        $this->serializeTransaction($item, $request->getTransaction());
        $element->appendChild($item);
    }

    private function serializeAcquirerStatusRequest(DOMElement $element, iDEALAcquirerStatusRequest $request)
    {
        $item = $element->ownerDocument->createElement("Merchant");
        $this->serializeMerchant($item, $request->getMerchant());
        $element->appendChild($item);

        $item = $element->ownerDocument->createElement("Transaction");
        $item->appendChild(new DOMElement("transactionID", $request->getTransactionID()));
        $element->appendChild($item);
    }

    private function serializeMerchant(DOMElement $element, iDEALMerchant $merchant, $withUrl = false)
    {
        $element->appendChild(new DOMElement("merchantID", $merchant->getMerchantID()));
        $element->appendChild(new DOMElement("subID", $merchant->getSubID()));

        if ($withUrl)
            $element->appendChild(new DOMElement("merchantReturnURL", $merchant->getMerchantReturnURL()));
    }

    private function serializeTransaction(DOMElement $element, iDEALTransaction $transaction)
    {
        $element->appendChild(new DOMElement("purchaseID", $transaction->getPurchaseId()));
        $element->appendChild(new DOMElement("amount", number_format($transaction->getAmount(), 2, '.', '')));
        $element->appendChild(new DOMElement("currency", $transaction->getCurrency()));

        if ($transaction->getExpirationPeriod() === 60)
            $element->appendChild(new DOMElement("expirationPeriod", "PT1H"));
        else
            $element->appendChild(new DOMElement("expirationPeriod", "PT".$transaction->getExpirationPeriod()."M"));

        $element->appendChild(new DOMElement("language", $transaction->getLanguage()));
        $element->appendChild(new DOMElement("description", $transaction->getDescription()));
        $element->appendChild(new DOMElement("entranceCode", $transaction->getEntranceCode()));
    }

    ///Deserialize///


    /**
     * @param DOMDocument $xml
     * @return IdealAbstractResponse
     */
    public function deserialize(DOMDocument $xml)
    {
        $this->checkForErrorMessage($xml);
        return $this->deserializeResponse($xml->documentElement);
    }

    private function checkForErrorMessage(DOMDocument $doc)
    {
        if ($doc->documentElement->tagName === "AcquirerErrorRes")
        {
            $code = null;
            $message = null;
            $details = '';
            $action = '' ;
            $consumerMessage = '' ;

            if($doc->documentElement->hasChildNodes())
            {
                $elements = $doc->documentElement->getElementsByTagName("Error");

                if ($elements->length === 1)
                {
                    try
                    {
                        /* @var $element DOMElement */
                        $element = $elements->item(0);

                        $code = $element->getElementsByTagName("errorCode")->item(0)->nodeValue;
                        $message = $element->getElementsByTagName("errorMessage")->item(0)->nodeValue;

                        $nodes = $element->getElementsByTagName("errorDetail");
                        if ($nodes->length === 1)
                            $details = $nodes->item(0)->nodeValue;

                        $nodes = $element->getElementsByTagName("suggestedAction");
                        if ($nodes->length === 1)
                            $action = $nodes->item(0)->nodeValue;

                        $nodes = $element->getElementsByTagName("consumerMessage");
                        if ($nodes->length === 1)
                            $consumerMessage = $nodes->item(0)->nodeValue;
                    }
                    catch(Exception $e)
                    {
                        //Pass-through to exception throwing if minimum requirements are not met.
                    }
                }

                if(is_null($code) || is_null($message))
                    throw new iDEALSerializationException("Invalid format of error response.");

                throw new iDEALException($code, $message, $details, $action, $consumerMessage);
            }

            throw new iDEALSerializationException("Error response missing content.");
        }
    }

    private function deserializeResponse(DOMElement $xml)
    {
        $timestamp = $xml->getElementsByTagName("createDateTimestamp");

        if ($timestamp->length != 1)
            throw new iDEALSerializationException('Element "createDateTimestamp" should be present once.');

        $timestamp = new DateTime($timestamp->item(0)->nodeValue);
        $timestamp->setTimezone(new DateTimeZone('UTC'));

        if ($xml->tagName === "DirectoryRes")
        {
            return $this->deserializeDirectoryResponse($xml, $timestamp);
        }
        else if ($xml->tagName === "AcquirerTrxRes")
        {
            return $this->deserializeAcquirerTransactionResponse($xml, $timestamp);
        }
        else if ($xml->tagName === "AcquirerStatusRes")
        {
            return $this->deserializeAcquirerStatusResponse($xml, $timestamp);
        }

        throw new iDEALSerializationException('Could not deserialize response.');
    }

    private function deserializeDirectoryResponse(DOMElement $xml, DateTime $createdTimestamp)
    {
        /* @var $nodes DOMNodeList */
        /* @var $node DOMElement */
        $nodes = $this->getChildren($xml, "Acquirer");
        $node = $nodes->item(0);
        $acquirerID = $this->getFirstValue($node, "acquirerID","Acquirer.acquirerID");

        $nodes = $this->getChildren($xml, "Directory");
        $node = $nodes->item(0);
        $timestamp = new DateTime(
            $this->getFirstValue($node, "directoryDateTimestamp","Directory.directoryDateTimestamp"));
        $timestamp->setTimezone(new DateTimeZone('UTC'));

        $countryElements = $this->getChildren($node, "Country", "Directory.Country", -1);

        $countries = array();

        /* @var $country DOMElement */
        foreach($countryElements as $country)
        {
            $names = $this->getFirstValue($country, "countryNames","Directory.Country.countryNames");

            $subNodes = $this->getChildren($country, "Issuer", "Directory.Country.Issuer", -1);
            $issuers = array();

            /* @var $issuer DOMElement */
            foreach($subNodes as $issuer)
            {
                $id = $this->getFirstValue($issuer, "issuerID","Directory.Country.Issuer.issuerID");
                $name = $this->getFirstValue($issuer, "issuerName","Directory.Country.Issuer.issuerName");

                array_push($issuers, new iDEALIssuer($id, $name));
            }

            array_push($countries, new iDEALCountry($names, $issuers));
        }

        return new iDEALDirectoryResponse($createdTimestamp, $timestamp, $acquirerID, $countries);
    }

    private function deserializeAcquirerTransactionResponse(DOMElement $xml, DateTime $createdTimestamp)
    {
        /* @var $nodes DOMNodeList */
        /* @var $node DOMElement */
        $nodes = $this->getChildren($xml, "Acquirer");
        $node = $nodes->item(0);

        $acquirerID = $this->getFirstValue($node, "acquirerID","Acquirer.acquirerID");

        $nodes = $this->getChildren($xml, "Issuer");
        $node = $nodes->item(0);

        $issuerAuthenticationUrl =
            $this->getFirstValue($node, "issuerAuthenticationURL","Issuer.issuerAuthenticationURL");

        $nodes = $this->getChildren($xml, "Transaction");
        $node = $nodes->item(0);
        $transactionId = $this->getFirstValue($node, "transactionID","Transaction.transactionID");

        $transactionCreateDateTimestamp =
            new DateTime($this->getFirstValue(
                $node,
                "transactionCreateDateTimestamp",
                "Transaction.transactionCreateDateTimestamp"));
        $transactionCreateDateTimestamp->setTimezone(new DateTimeZone('UTC'));

        $purchaseID = $this->getFirstValue($node, "purchaseID","Transaction.purchaseID");

        return new iDEALAcquirerTransactionResponse(
            $acquirerID,
            $issuerAuthenticationUrl,
            $purchaseID,
            $transactionId,
            $transactionCreateDateTimestamp,
            $createdTimestamp);
    }

    private function deserializeAcquirerStatusResponse(DOMElement $xml, DateTime $createdTimestamp)
    {
        /* @var $nodes DOMNodeList */
        /* @var $node DOMElement */
        $nodes = $this->getChildren($xml, "Acquirer");
        $node = $nodes->item(0);

        $acquirerID = $this->getFirstValue($node, "acquirerID","Acquirer.acquirerID");

        $nodes = $this->getChildren($xml, "Transaction");
        $node = $nodes->item(0);
        $transactionId = $this->getFirstValue($node, "transactionID","Transaction.transactionID");
        $status = $this->getFirstValue($node, "status","Transaction.status");
        $timestamp = new DateTime($this->getFirstValue($node, "statusDateTimestamp","Transaction.statusDateTimestamp"));
        $timestamp->setTimezone(new DateTimeZone('UTC'));

        $consumerName = $this->getFirstValue($node, "consumerName","Transaction.consumerName", 0);
        $consumerIBAN = $this->getFirstValue($node, "consumerIBAN","Transaction.consumerIBAN", 0);
        $consumerBIC = $this->getFirstValue($node, "consumerBIC","Transaction.consumerBIC", 0);
        $amount = floatval($this->getFirstValue($node, "amount","Transaction.amount", 0));
        $currency = $this->getFirstValue($node, "currency","Transaction.currency", 0);

        return new iDEALAcquirerStatusResponse(
            $acquirerID,
            $amount,
            $consumerBIC,
            $consumerIBAN,
            $consumerName,
            $createdTimestamp,
            $currency,
            $status,
            $timestamp,
            $transactionId);
    }

    private function getChildren(DOMElement $element, $tag, $key = null, $occurs = 1)
    {
        if(is_null($key))
            $key = $tag;

        $nodes = $element->getElementsByTagName($tag);

        if ($occurs === 0 && $nodes->length != 1)
                throw new iDEALSerializationException("Element '$key' should be present once.");

        if ($occurs === 1 && $nodes->length < 1)
                throw new iDEALSerializationException("Element '$key' should be present at least once.");

        return $nodes;
    }

    private function getFirstValue(DOMElement $node, $tag, $key, $occurs = 1)
    {
        /* @var $nodes DOMNodeList */
        $nodes = $node->getElementsByTagName($tag);

        if ($nodes->length === 0)
            return null;

        if ($occurs === 1 && $nodes->length != 1)
            throw new iDEALSerializationException("Element '$key' should be present once.");

        if ($occurs === -1 && $nodes->length < 1)
            throw new iDEALSerializationException("Element '$key' should be present at least once.");

        return $nodes->item(0)->nodeValue;
    }
}
