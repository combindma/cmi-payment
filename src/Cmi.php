<?php

namespace Combindma\Cmi;

use Combindma\Cmi\Exceptions\InvalidConfiguration;
use Combindma\Cmi\Exceptions\InvalidRequest;

class Cmi
{
    private string $baseUri;
    private string $clientId;
    private string $storeKey;
    private string $storeType;
    private string $tranType;
    private string $lang;
    private string $currency;
    private string $okUrl;
    private string $failUrl;
    private string $shopUrl;
    private string $callbackUrl;
    private bool $callbackResponse;
    private string $hashAlgorithm;
    private string $encoding;
    private bool $autoRedirect;
    private string $sessionTimeout;
    private string $rnd;
    private string $amount;
    private string $oid;
    private string $email;
    private string $billToName;
    private string $tel;
    private bool $currenciesList;
    private string $amountCur;
    private string $symbolCur;
    private string $description;
    private string $hash;

    public function __construct()
    {
        $this->baseUri = config('cmi-payment.baseUri');
        $this->clientId = config('cmi-payment.clientId');
        $this->storeKey = config('cmi-payment.storeKey');
        $this->storeType = config('cmi-payment.storeType');
        $this->tranType = config('cmi-payment.tranType');
        $this->lang = config('cmi-payment.lang');
        $this->currency = config('cmi-payment.currency');
        $this->okUrl = config('cmi-payment.okUrl');
        $this->failUrl = config('cmi-payment.failUrl');
        $this->shopUrl = config('cmi-payment.shopUrl');
        $this->callbackUrl = config('cmi-payment.callbackUrl');
        $this->callbackResponse = (bool)config('cmi-payment.callbackResponse');
        $this->hashAlgorithm = config('cmi-payment.hashAlgorithm');
        $this->encoding = config('cmi-payment.encoding');
        $this->autoRedirect = (bool)config('cmi-payment.autoRedirect');
        $this->sessionTimeout = config('cmi-payment.sessionTimeout');
        $this->rnd = (string)microtime();

        $this->guardAgainstInvalidConfiguration();
    }

    public function getBaseUri()
    {
        return $this->baseUri;
    }

    public function enableAutoRedirect()
    {
        $this->autoRedirect = true;
    }

    public function disableAutoRedirect()
    {
        $this->autoRedirect = false;
    }

    public function enableCallbackRespense()
    {
        $this->callbackResponse = true;
    }

    public function disableCallbackRespense()
    {
        $this->callbackResponse = false;
    }

    public function setSessionTimeout($seconds)
    {
        $this->sessionTimeout = (string)$seconds;
    }

    public function setOid($oid): void
    {
        $this->oid = (string)$oid;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setAmount($amount): void
    {
        $this->amount = (string)$amount;
    }

    public function setBillToName(string $billToName): void
    {
        $this->billToName = $billToName;
    }

    public function setTel($tel): void
    {
        $this->tel = (string)$tel;
    }

    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }

    public function setCurrency($currency): void
    {
        $this->currency = (string)$currency;
    }

    public function setCurrenciesList(bool $currenciesList): void
    {
        $this->currenciesList = $currenciesList;
    }

    public function setAmountCur($amountCur): void
    {
        $this->amountCur = (string)$amountCur;
    }

    public function setSymbolCur($symbolCur): void
    {
        $this->symbolCur = (string)$symbolCur;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setOkUrl(string $okUrl): void
    {
        $this->okUrl = $okUrl;
    }

    public function getHash(array $params = [])
    {
        $cmiData = $this->getCmiData($params);
        $plainText = $this->getPlainText($cmiData);
        $hash = base64_encode(pack('H*', hash('sha512', $plainText)));
        $this->hash = $hash;

        return $hash;
    }

    public function getCmiData(array $params = [])
    {
        $cmiParams = array_merge(get_object_vars($this), $params);
        $this->unsetData($cmiParams);

        return $cmiParams;
    }

    private function getPlainText(&$data)
    {
        $this->formatData($data);
        $data = array_merge($data, [
            'storekey' => $this->storeKey,
        ]);

        return implode('|', $data);
    }

    private function unsetData(&$data)
    {
        unset($data['storeKey'], $data['baseUri']);

        return $data;
    }

    private function formatData(&$data)
    {
        ksort($data);
        foreach ($data as $key => $value) {
            $data[$key] = trim(strtolower($value));
        }
    }

    /**
     * @throws InvalidRequest
     */
    public function guardAgainstInvalidRequest()
    {
        //amount
        if ($this->amount === null) {
            throw InvalidRequest::amountNotSpecified();
        }

        if (! preg_match('/^\d+(\.\d{2})?$/', $this->amount)) {
            throw InvalidRequest::amountValueInvalid();
        }

        //currency
        if ($this->currency === null) {
            throw InvalidRequest::currencyNotSpecified();
        }

        if (! is_string($this->currency) || strlen($this->currency) != 3) {
            throw InvalidRequest::currencyValueInvalid();
        }

        //oid
        if ($this->oid === null) {
            throw InvalidRequest::attributeNotSpecified('identifiant de la commande (oid)');
        }

        if (! is_string($this->oid) || preg_match('/\s/', $this->oid)) {
            throw InvalidRequest::attributeInvalidString('identifiant de la commande (oid)');
        }

        //email
        if ($this->email === null) {
            throw InvalidRequest::attributeNotSpecified('adresse électronique du client (email)');
        }

        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw InvalidRequest::emailValueInvalid();
        }

        //billToName
        if ($this->billToName === null) {
            throw InvalidRequest::attributeNotSpecified('nom du client (billToName)');
        }

        if (! is_string($this->billToName) || $this->billToName === '') {
            throw InvalidRequest::attributeInvalidString('nom du client (billToName)');
        }

        //tel
        if (isset($this->tel) && ! is_string($this->tel)) {
            throw InvalidRequest::attributeInvalidString('téléphone du client (tel)');
        }

        //amountCur
        if (isset($this->amountCur) && ! is_string($this->amountCur)) {
            throw InvalidRequest::attributeInvalidString('montant de coversion (amountCur)');
        }

        //symbolCur
        if (isset($this->symbolCur) && ! is_string($this->symbolCur)) {
            throw InvalidRequest::attributeInvalidString('symbole de la devise de conversion (symbolCur)');
        }

        //description
        if (isset($this->description) && ! is_string($this->description)) {
            throw InvalidRequest::attributeInvalidString('description');
        }
    }

    /**
     * @throws InvalidConfiguration
     */
    private function guardAgainstInvalidConfiguration()
    {
        //clientId
        if (! $this->clientId) {
            throw InvalidConfiguration::clientIdNotSpecified();
        }

        if (! is_string($this->clientId) || preg_match('/\s/', $this->clientId)) {
            throw InvalidConfiguration::clientIdInvalid();
        }

        //storeKey
        if (! $this->storeKey) {
            throw InvalidConfiguration::storeKeyNotSpecified();
        }

        if (! is_string($this->storeKey) || preg_match('/\s/', $this->storeKey)) {
            throw InvalidConfiguration::storeKeyInvalid();
        }

        //storeType
        if (! $this->storeType) {
            throw InvalidConfiguration::attributeNotSpecified('modèle du paiement du marchand (storeType)');
        }

        if (! is_string($this->storeType) || preg_match('/\s/', $this->storeType)) {
            throw InvalidConfiguration::attributeInvalidString('modèle du paiement du marchand (storeType)');
        }

        //tranType
        if (! $this->tranType) {
            throw InvalidConfiguration::attributeNotSpecified('Type de la transaction (tranType)');
        }

        if (! is_string($this->tranType) || preg_match('/\s/', $this->tranType)) {
            throw InvalidConfiguration::attributeInvalidString('Type de la transaction (tranType)');
        }

        //lang
        if (! in_array($this->lang, ['fr', 'ar', 'en'])) {
            throw InvalidConfiguration::langValueInvalid();
        }

        //baseUri
        if (! $this->baseUri) {
            throw InvalidConfiguration::attributeNotSpecified('gateway de paiement (baseUri)');
        }

        if (! is_string($this->baseUri) || preg_match('/\s/', $this->baseUri)) {
            throw InvalidConfiguration::attributeInvalidString('gateway de paiement (baseUri)');
        }

        if (! preg_match("/\b(?:(?:https):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->baseUri)) {
            throw InvalidConfiguration::attributeInvalidUrl('gateway de paiement (baseUri)');
        }

        //okUrl
        if (! $this->okUrl) {
            throw InvalidConfiguration::attributeNotSpecified('okUrl');
        }

        if (! is_string($this->okUrl) || preg_match('/\s/', $this->okUrl)) {
            throw InvalidConfiguration::attributeInvalidString('okUrl');
        }

        if (! preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->okUrl)) {
            throw InvalidConfiguration::attributeInvalidUrl('okUrl');
        }

        //failUrl
        if (! $this->failUrl) {
            throw InvalidConfiguration::attributeNotSpecified('failUrl');
        }

        if (! is_string($this->failUrl) || preg_match('/\s/', $this->failUrl)) {
            throw InvalidConfiguration::attributeInvalidString('failUrl');
        }

        if (! preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->failUrl)) {
            throw InvalidConfiguration::attributeInvalidUrl('failUrl');
        }

        //shopUrl
        if (! $this->shopUrl) {
            throw InvalidConfiguration::attributeNotSpecified('shopUrl');
        }

        if (! is_string($this->failUrl) || preg_match('/\s/', $this->failUrl)) {
            throw InvalidConfiguration::attributeInvalidString('shopUrl');
        }

        if (! preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->shopUrl)) {
            throw InvalidConfiguration::attributeInvalidUrl('shopUrl');
        }

        //callbackUrl
        if (! $this->callbackUrl) {
            throw InvalidConfiguration::attributeNotSpecified('callbackUrl');
        }

        if (! is_string($this->callbackUrl) || preg_match('/\s/', $this->callbackUrl)) {
            throw InvalidConfiguration::attributeInvalidString('callbackUrl');
        }

        if (! preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->callbackUrl)) {
            throw InvalidConfiguration::attributeInvalidUrl('callbackUrl');
        }

        //hashAlgorithm
        if (! $this->hashAlgorithm) {
            throw InvalidConfiguration::attributeNotSpecified('version du hachage (hashAlgorithm)');
        }

        if (! is_string($this->hashAlgorithm) || preg_match('/\s/', $this->hashAlgorithm)) {
            throw InvalidConfiguration::attributeInvalidString('version du hachage (hashAlgorithm)');
        }

        //encoding
        if (! $this->encoding) {
            throw InvalidConfiguration::attributeNotSpecified('encodage des données (encoding)');
        }

        if (! is_string($this->encoding) || preg_match('/\s/', $this->encoding)) {
            throw InvalidConfiguration::attributeInvalidString('encodage des données (encoding)');
        }

        //sessionTimeout
        if (! $this->sessionTimeout) {
            throw InvalidConfiguration::attributeNotSpecified('délai d\'expiration de la session (sessionTimeout)');
        }

        if (! is_string($this->sessionTimeout) || (int)$this->sessionTimeout < 30 || (int)$this->sessionTimeout > 2700) {
            throw InvalidConfiguration::sessionimeoutValueInvalid();
        }
    }
}
