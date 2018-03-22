<?php
namespace iDEALConnector\Xml;

use DOMDocument;
use Exception;
use XMLSecurityKey;
use XMLSecurityDSig;
use iDEALConnector\Exceptions\SecurityException;

class XmlSecurity
{
    public function sign(DOMDocument $doc, $privateCertificatePath, $privateKeyPath, $passphrase)
    {
        $signature = new XMLSecurityDSig();
        $signature->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $signature->addReference($doc, XMLSecurityDSig::SHA256, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'), array('force_uri' => true));

        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type' => 'private'));

        $key->passphrase = $passphrase;
        $key->loadKey($privateKeyPath, TRUE);

        $signature->sign($key);

        $fingerprint = $this->getFingerprint($privateCertificatePath);

        $signature->addKeyInfoAndName($fingerprint);

        $signature->appendSignature($doc->documentElement);
        return $doc->saveXML();
    }

    public function verify(DOMDocument $doc, $certificatePath)
    {
        $signature = new XMLSecurityDSig();
        $sig = $signature->locateSignature($doc);
        if (!$sig)
            throw new SecurityException("Cannot locate Signature Node");

        //$signature->setCanonicalMethod(XMLSecurityDSig::EXC_C14N); //whitespaces are significant
        $signature->canonicalizeSignedInfo();

        try
        {
            $signature->validateReference();
        }
        catch(Exception $ex)
        {
            throw new SecurityException("Reference Validation Failed");
        }

        $key = $signature->locateKey();
        if (!$key)
            throw new SecurityException("Cannot locate the key.");

        $key->loadKey($certificatePath,true);

        return $signature->verify($key) == 1;
    }

    private function getFingerprint($path)
    {
        $contents = file_get_contents($path);

        if (is_null($contents))
            throw new SecurityException("Empty certificate.");

        $contents = str_replace('-----END CERTIFICATE-----', '', str_replace('-----BEGIN CERTIFICATE-----', '', $contents));
        $contents = base64_decode($contents);
        return sha1($contents);
    }
}
