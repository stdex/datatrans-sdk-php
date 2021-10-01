<?php
/**
 * StatusResponse
 *
 * PHP version 7.2
 *
 * @category Class
 * @package  Datatrans\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * Datatrans API Reference
 *
 * Welcome to the Datatrans API reference. This document is meant to be used in combination with https://docs.datatrans.ch. All the parameters used in the curl and web samples are described here. Reach out to support@datatrans.ch if something is missing or unclear.  Last updated: 09.09.21 - 07:44 UTC  # Payment Process The following steps describe how transactions are processed with Datatrans. We separate payments in three categories: Customer-initiated payments, merchant-initiated payments and after the payment.  ## Customer Initiated Payments We have three integrations available: [Redirect](https://docs.datatrans.ch/docs/redirect-lightbox), [Lightbox](https://docs.datatrans.ch/docs/redirect-lightbox) and [Secure Fields](https://docs.datatrans.ch/docs/secure-fields).  ### Redirect & Lightbox - Send the required parameters to initialize a `transactionId` to the [init](#operation/init) endpoint. - Let the customer proceed with the payment by redirecting them to the correct link - or showing them your payment form.   - Redirect: Redirect the browser to the following URL structure     ```     https://pay.sandbox.datatrans.com/v1/start/transactionId     ```   - Lightbox: Load the JavaScript library and initialize the payment form:     ```js     <script src=\"https://pay.sandbox.datatrans.com/upp/payment/js/datatrans-2.0.0.js\">     ```     ```js     payButton.onclick = function() {       Datatrans.startPayment({         transactionId:  \"transactionId\"       });     };     ``` - Your customer proceeds with entering their payment information and finally hits the pay or continue button. - For card payments, we check the payment information with your acquirers. The acquirers check the payment information with the issuing parties. The customer proceeds with 3D Secure whenever required. - Once the transaction is completed, we return all relevant information to you (check our [Webhook section](#section/Webhook) for more details), and the status of the transaction. The browser will be redirected to the success, cancel or error page with our `transactionId` in the response.  ### Secure Fields - Send the required parameters to initialize a transactionId to our [secureFieldsInit](#operation/secureFieldsInit) endpoint. - Load the Secure Fields JavaScript libarary and initialize Secure Fields:   ```js   <script src=\"https://pay.sandbox.datatrans.com/upp/payment/js/secure-fields-2.0.0.js\">   ```   ```js   var secureFields = new SecureFields();   secureFields.init(     {{transactionId}}, {         cardNumber: \"cardNumberPlaceholder\",         cvv: \"cvvPlaceholder\",     });   ``` - Handle the success event of the secureFields.submit() call. - If 3D authentication is required for a specific transaction, the `redirect` property inside the `data` object will indicate the URL that the customer needs to be redirected to. - Use the [Authorize an authenticated transaction](#operation/authorize-split)endpoint to fully authorize the Secure Fields transaction. This is required to finalize the authorization process with Secure Fields.  ## Merchant Initiated Payments Once you have processed a customer-initiated payment or registration you can call our API to process recurring payments. Check our [authorize](#operation/authorize) endpoint to see how to create a recurring payment or our [validate](#operation/validate) endpoint to validate your customers’ saved payment details.  ## After the payment Use the `transactionId` to check the [status](#operation/status) and to [settle](#operation/settle), [cancel](#operation/cancel) or [refund](#operation/credit) a transaction.  # Idempotency  To retry identical requests with the same effect without accidentally performing the same operation more than needed, you can add the header `Idempotency-Key` to your requests. This is useful when API calls are disrupted or you did not receive a response. In other words, retrying identical requests with our idempotency key will not have any side effects. We will return the same response for any identical request that includes the same idempotency key.  If your request failed to reach our servers, no idempotent result is saved because no API endpoint processed your request. In such cases, you can simply retry your operation safely. Idempotency keys remain stored for 60 minutes. After 60 minutes have passed, sending the same request together with the previous idempotency key will create a new operation.  Please note that the idempotency key has to be unique for each request and has to be defined by yourself. We recommend assigning a random value as your idempotency key and using UUID v4. Idempotency is only available for `POST` requests.  Idempotency was implemented according to the [\"The Idempotency HTTP Header Field\" Internet-Draft](https://tools.ietf.org/id/draft-idempotency-header-01.html)  |Scenario|Condition|Expectation| |:---|:---|:---| |First time request|Idempotency key has not been seen during the past 60 minutes.|The request is processed normally.| |Repeated request|The request was retried after the first time request completed.| The response from the first time request will be returned.| |Repeated request|The request was retried before the first time request completed.| 409 Conflict. It is recommended that clients time their retries using an exponential backoff algorithm.| |Repeated request|The request body is different than the one from the first time request.| 422 Unprocessable Entity.|  Example: ```sh curl -i 'https://api.sandbox.datatrans.com/v1/transactions' \\     -H 'Authorization: Basic MTEwMDAwNzI4MzpobDJST1NScUN2am5EVlJL' \\     -H 'Content-Type: application/json; charset=UTF-8' \\     -H 'Idempotency-Key: e75d621b-0e56-4b71-b889-1acec3e9d870' \\     -d '{     \"refno\" : \"58b389331dad\",     \"amount\" : 1000,     \"currency\" : \"CHF\",     \"paymentMethods\" : [ \"VIS\", \"ECA\", \"PAP\" ],     \"option\" : {        \"createAlias\" : true     } }' ```  # Authentication Authentication to the APIs is performed with HTTP basic authentication. Your `merchantId` acts as the username. To get the password, login to the <a href='https://admin.sandbox.datatrans.com/' target='_blank'>dashboard</a> and navigate to the security settings under `UPP Administration > Security`.  Create a base64 encoded value consisting of merchantId and password (most HTTP clients are able to handle the base64 encoding automatically) and submit the Authorization header with your requests. Here’s an example:  ``` base64(merchantId:password) = MTAwMDAxMTAxMTpYMWVXNmkjJA== ```  ``` Authorization: Basic MTAwMDAxMTAxMTpYMWVXNmkjJA== ````  All API requests must be done over HTTPS with TLS >= 1.2.   <!-- ReDoc-Inject: <security-definitions> -->  # Errors Datatrans uses HTTP response codes to indicate if an API call was successful or resulted in a failure. HTTP `2xx` status codes indicate a successful API call whereas HTTP `4xx` status codes indicate client errors or if something with the transaction went wrong - for example a decline. In rare cases HTTP `5xx` status codes are returned. Those indicate errors on Datatrans side.  Here’s the payload of a sample HTTP `400` error, showing that your request has wrong values in it ``` {   \"error\" : {     \"code\" : \"INVALID_PROPERTY\",     \"message\" : \"init.initRequest.currency The given currency does not have the right format\"   } } ```  # Webhook After each authorization Datatrans tries to call the configured Webhook (POST) URL. The Webhook URL can be configured within the <a href='https://admin.sandbox.datatrans.com/' target='_blank'>dashboard</a>. The Webhook payload contains the same information as the response of a [Status API](#operation/status) call.  ## Webhook signing If you want your webhook requests to be signed, setup a HMAC key in your merchant configuration. To get your HMAC key, login to our dashboard and navigate to the Security settings in your merchant configuration to view your server to server security settings. Select the radio button `Important parameters will be digitally signed (HMAC-SHA256) and sent with payment messages`. Datatrans will use this key to sign the webhook payload and will add a `Datatrans-Signature` HTTP request header:  ```sh Datatrans-Signature: t=1559303131511,s0=33819a1220fd8e38fc5bad3f57ef31095fac0deb38c001ba347e694f48ffe2fc ```  On your server, calculate the signature of the webhook payload and finally compare it to `s0`. `timestamp` is the `t` value from the Datatrans-Signature header, `payload` represents all UTF-8 bytes from the body of the payload and finally `key` is the HMAC key you configured within the dashboard. If the value of `sign` is equal to `s0` from the `Datatrans-Signature` header, the webhook payload is valid and was not tampered.  **Java**  ```java // hex bytes of the key byte[] key = Hex.decodeHex(key);  // Create sign with timestamp and payload String algorithm = \"HmacSha256\"; SecretKeySpec macKey = new SecretKeySpec(key, algorithm); Mac mac = Mac.getInstance(algorithm); mac.init(macKey); mac.update(String.valueOf(timestamp).getBytes()); byte[] result = mac.doFinal(payload.getBytes()); String sign = Hex.encodeHexString(result); ```  **Python**  ```python # hex bytes of the key key_hex_bytes = bytes.fromhex(key)  # Create sign with timestamp and payload sign = hmac.new(key_hex_bytes, bytes(str(timestamp) + payload, 'utf-8'), hashlib.sha256) ```  # Release notes <details>   <summary>Details</summary>    ### 2.0.22 - 21.07.2021 * Added full support for Swisscom Pay `ESY` * The `marketplace` object now accepts an array of splits.  ### 2.0.21 - 21.05.2021 * Updated idempotency handling. See the details here https://api-reference.datatrans.ch/#section/Idempotency  ### 2.0.20 - 18.05.2021 * In addition to `debit` and `credit` the Status API now also returns `prepaid` in the `card.info.type` property. * paysafecard - Added support for `merchantClientId`   ### 2.0.19 - 03.05.2021 * Fixed `PAP.orderTransactionId` to be a string * Added support for `PAP.fraudSessionId` (PayPal FraudNet)  ### 2.0.18 - 21.04.2021 * Added new `POST /v1/transactions/screen` API to check a customer's credit score before sending an actual authorization request. Currently only `INT` (Byjuno) is supported.  ### 2.0.17 - 20.04.2021 * Added new `GET /v1/aliases` API to receive more information about a particular alias.  ### 2.0.16 - 13.04.2021 * Added support for Migros Bank E-Pay <code>MDP</code>  ### 2.0.15 - 24.03.2021 * Byjuno - renamed `subPaymentMethod` to `subtype` (`subPaymentMethod` still works) * Klarna - Returning the `subtype` (`pay_now`, `pay_later`, `pay_over_time`, `direct_debit`, `direct_bank_transfer`) from the Status API  ### 2.0.14 - 09.03.2021 * Byjuno - Added support for `customData` and `firstRateAmount` * Returning the `transactionId` (if available) for a failed Refund API call.  ### 2.0.13 - 15.02.2021 * The Status and Webhook payloads now include the `language` property * Fixed a bug where `card.3D.transStatusReason` and `card.3D.cardholderInfo` was not returned  ### 2.0.12 - 04.02.2021 * Added support for PayPal transaction context (STC) * Fixed a bug where the transaction status did not switch to `failed` after it timed out * Fixed a bug with `option.rememberMe` not returning the Alias from the Status API  ### 2.0.11 - 01.02.2021 * Returning `card.3D.transStatusReason` (if available) from the Status API  ### 2.0.10 - 18.01.2021 * Returning `card.3D.cardholderInfo` (if available) from the Status API  ### 2.0.9 - 21.12.2020 * Added support for Alipay <code>ALP</code>  ### 2.0.8 - 21.12.2020 * Added full support for Klarna <code>KLN</code> * Added support for swissbilling <code>SWB</code>  </details>
 *
 * The version of the OpenAPI document: 2.0.22
 * Contact: support@datatrans.ch
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 5.0.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace Datatrans\Client\Model;

use \ArrayAccess;
use \Datatrans\Client\ObjectSerializer;

/**
 * StatusResponse Class Doc Comment
 *
 * @category Class
 * @package  Datatrans\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<TKey, TValue>
 * @template TKey int|null
 * @template TValue mixed|null  
 */
class StatusResponse implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'StatusResponse';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'transaction_id' => 'string',
        'type' => 'string',
        'status' => 'string',
        'currency' => 'string',
        'refno' => 'string',
        'refno2' => 'string',
        'payment_method' => 'string',
        'detail' => '\Datatrans\Client\Model\Detail',
        'customer' => '\Datatrans\Client\Model\Customer',
        'cdm' => 'object',
        'language' => 'string',
        'card' => '\Datatrans\Client\Model\CardDetail',
        'twi' => '\Datatrans\Client\Model\TwintDetail',
        'pap' => '\Datatrans\Client\Model\PayPalDetail',
        'rek' => '\Datatrans\Client\Model\RekaDetail',
        'elv' => '\Datatrans\Client\Model\ElvDetail',
        'kln' => '\Datatrans\Client\Model\KlarnaDetail',
        'int' => '\Datatrans\Client\Model\ByjunoDetail',
        'swp' => '\Datatrans\Client\Model\SwissPassDetail',
        'mfx' => '\Datatrans\Client\Model\MFXDetail',
        'mpx' => '\Datatrans\Client\Model\MPXDetail',
        'mdp' => '\Datatrans\Client\Model\MDPDetail',
        'esy' => '\Datatrans\Client\Model\SwisscomPayDetail',
        'pfc' => '\Datatrans\Client\Model\PostfinanceDetail',
        'wec' => '\Datatrans\Client\Model\WeChatDetail',
        'scx' => '\Datatrans\Client\Model\SuperCard',
        'history' => '\Datatrans\Client\Model\Action[]',
        'ep2' => '\Datatrans\Client\Model\Ep2'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'transaction_id' => null,
        'type' => null,
        'status' => null,
        'currency' => null,
        'refno' => null,
        'refno2' => null,
        'payment_method' => null,
        'detail' => null,
        'customer' => null,
        'cdm' => null,
        'language' => null,
        'card' => null,
        'twi' => null,
        'pap' => null,
        'rek' => null,
        'elv' => null,
        'kln' => null,
        'int' => null,
        'swp' => null,
        'mfx' => null,
        'mpx' => null,
        'mdp' => null,
        'esy' => null,
        'pfc' => null,
        'wec' => null,
        'scx' => null,
        'history' => null,
        'ep2' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'transaction_id' => 'transactionId',
        'type' => 'type',
        'status' => 'status',
        'currency' => 'currency',
        'refno' => 'refno',
        'refno2' => 'refno2',
        'payment_method' => 'paymentMethod',
        'detail' => 'detail',
        'customer' => 'customer',
        'cdm' => 'cdm',
        'language' => 'language',
        'card' => 'card',
        'twi' => 'TWI',
        'pap' => 'PAP',
        'rek' => 'REK',
        'elv' => 'ELV',
        'kln' => 'KLN',
        'int' => 'INT',
        'swp' => 'SWP',
        'mfx' => 'MFX',
        'mpx' => 'MPX',
        'mdp' => 'MDP',
        'esy' => 'ESY',
        'pfc' => 'PFC',
        'wec' => 'WEC',
        'scx' => 'SCX',
        'history' => 'history',
        'ep2' => 'ep2'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'transaction_id' => 'setTransactionId',
        'type' => 'setType',
        'status' => 'setStatus',
        'currency' => 'setCurrency',
        'refno' => 'setRefno',
        'refno2' => 'setRefno2',
        'payment_method' => 'setPaymentMethod',
        'detail' => 'setDetail',
        'customer' => 'setCustomer',
        'cdm' => 'setCdm',
        'language' => 'setLanguage',
        'card' => 'setCard',
        'twi' => 'setTwi',
        'pap' => 'setPap',
        'rek' => 'setRek',
        'elv' => 'setElv',
        'kln' => 'setKln',
        'int' => 'setInt',
        'swp' => 'setSwp',
        'mfx' => 'setMfx',
        'mpx' => 'setMpx',
        'mdp' => 'setMdp',
        'esy' => 'setEsy',
        'pfc' => 'setPfc',
        'wec' => 'setWec',
        'scx' => 'setScx',
        'history' => 'setHistory',
        'ep2' => 'setEp2'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'transaction_id' => 'getTransactionId',
        'type' => 'getType',
        'status' => 'getStatus',
        'currency' => 'getCurrency',
        'refno' => 'getRefno',
        'refno2' => 'getRefno2',
        'payment_method' => 'getPaymentMethod',
        'detail' => 'getDetail',
        'customer' => 'getCustomer',
        'cdm' => 'getCdm',
        'language' => 'getLanguage',
        'card' => 'getCard',
        'twi' => 'getTwi',
        'pap' => 'getPap',
        'rek' => 'getRek',
        'elv' => 'getElv',
        'kln' => 'getKln',
        'int' => 'getInt',
        'swp' => 'getSwp',
        'mfx' => 'getMfx',
        'mpx' => 'getMpx',
        'mdp' => 'getMdp',
        'esy' => 'getEsy',
        'pfc' => 'getPfc',
        'wec' => 'getWec',
        'scx' => 'getScx',
        'history' => 'getHistory',
        'ep2' => 'getEp2'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }

    const TYPE_PAYMENT = 'payment';
    const TYPE_CREDIT = 'credit';
    const TYPE_CARD_CHECK = 'card_check';
    const STATUS_INITIALIZED = 'initialized';
    const STATUS_CHALLENGE_REQUIRED = 'challenge_required';
    const STATUS_CHALLENGE_ONGOING = 'challenge_ongoing';
    const STATUS_AUTHENTICATED = 'authenticated';
    const STATUS_AUTHORIZED = 'authorized';
    const STATUS_SETTLED = 'settled';
    const STATUS_CANCELED = 'canceled';
    const STATUS_TRANSMITTED = 'transmitted';
    const STATUS_FAILED = 'failed';
    const PAYMENT_METHOD_ACC = 'ACC';
    const PAYMENT_METHOD_ALP = 'ALP';
    const PAYMENT_METHOD_APL = 'APL';
    const PAYMENT_METHOD_AMX = 'AMX';
    const PAYMENT_METHOD_AZP = 'AZP';
    const PAYMENT_METHOD_BON = 'BON';
    const PAYMENT_METHOD_CFY = 'CFY';
    const PAYMENT_METHOD_CSY = 'CSY';
    const PAYMENT_METHOD_CUP = 'CUP';
    const PAYMENT_METHOD_DIN = 'DIN';
    const PAYMENT_METHOD_DII = 'DII';
    const PAYMENT_METHOD_DIB = 'DIB';
    const PAYMENT_METHOD_DIS = 'DIS';
    const PAYMENT_METHOD_DNK = 'DNK';
    const PAYMENT_METHOD_ECA = 'ECA';
    const PAYMENT_METHOD_ELV = 'ELV';
    const PAYMENT_METHOD_EPS = 'EPS';
    const PAYMENT_METHOD_ESY = 'ESY';
    const PAYMENT_METHOD_INT = 'INT';
    const PAYMENT_METHOD_JCB = 'JCB';
    const PAYMENT_METHOD_JEL = 'JEL';
    const PAYMENT_METHOD_KLN = 'KLN';
    const PAYMENT_METHOD_MAU = 'MAU';
    const PAYMENT_METHOD_MDP = 'MDP';
    const PAYMENT_METHOD_MFX = 'MFX';
    const PAYMENT_METHOD_MPX = 'MPX';
    const PAYMENT_METHOD_MYO = 'MYO';
    const PAYMENT_METHOD_PAP = 'PAP';
    const PAYMENT_METHOD_PAY = 'PAY';
    const PAYMENT_METHOD_PEF = 'PEF';
    const PAYMENT_METHOD_PFC = 'PFC';
    const PAYMENT_METHOD_PSC = 'PSC';
    const PAYMENT_METHOD_REK = 'REK';
    const PAYMENT_METHOD_SAM = 'SAM';
    const PAYMENT_METHOD_SWB = 'SWB';
    const PAYMENT_METHOD_SCX = 'SCX';
    const PAYMENT_METHOD_SWP = 'SWP';
    const PAYMENT_METHOD_TWI = 'TWI';
    const PAYMENT_METHOD_UAP = 'UAP';
    const PAYMENT_METHOD_VIS = 'VIS';
    const PAYMENT_METHOD_WEC = 'WEC';
    const LANGUAGE_EN = 'en';
    const LANGUAGE_DE = 'de';
    const LANGUAGE_FR = 'fr';
    const LANGUAGE_IT = 'it';
    const LANGUAGE_ES = 'es';
    const LANGUAGE_EL = 'el';
    const LANGUAGE_NO = 'no';
    const LANGUAGE_DA = 'da';
    const LANGUAGE_PL = 'pl';
    const LANGUAGE_PT = 'pt';
    const LANGUAGE_RU = 'ru';
    const LANGUAGE_JA = 'ja';
    

    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getTypeAllowableValues()
    {
        return [
            self::TYPE_PAYMENT,
            self::TYPE_CREDIT,
            self::TYPE_CARD_CHECK,
        ];
    }
    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getStatusAllowableValues()
    {
        return [
            self::STATUS_INITIALIZED,
            self::STATUS_CHALLENGE_REQUIRED,
            self::STATUS_CHALLENGE_ONGOING,
            self::STATUS_AUTHENTICATED,
            self::STATUS_AUTHORIZED,
            self::STATUS_SETTLED,
            self::STATUS_CANCELED,
            self::STATUS_TRANSMITTED,
            self::STATUS_FAILED,
        ];
    }
    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getPaymentMethodAllowableValues()
    {
        return [
            self::PAYMENT_METHOD_ACC,
            self::PAYMENT_METHOD_ALP,
            self::PAYMENT_METHOD_APL,
            self::PAYMENT_METHOD_AMX,
            self::PAYMENT_METHOD_AZP,
            self::PAYMENT_METHOD_BON,
            self::PAYMENT_METHOD_CFY,
            self::PAYMENT_METHOD_CSY,
            self::PAYMENT_METHOD_CUP,
            self::PAYMENT_METHOD_DIN,
            self::PAYMENT_METHOD_DII,
            self::PAYMENT_METHOD_DIB,
            self::PAYMENT_METHOD_DIS,
            self::PAYMENT_METHOD_DNK,
            self::PAYMENT_METHOD_ECA,
            self::PAYMENT_METHOD_ELV,
            self::PAYMENT_METHOD_EPS,
            self::PAYMENT_METHOD_ESY,
            self::PAYMENT_METHOD_INT,
            self::PAYMENT_METHOD_JCB,
            self::PAYMENT_METHOD_JEL,
            self::PAYMENT_METHOD_KLN,
            self::PAYMENT_METHOD_MAU,
            self::PAYMENT_METHOD_MDP,
            self::PAYMENT_METHOD_MFX,
            self::PAYMENT_METHOD_MPX,
            self::PAYMENT_METHOD_MYO,
            self::PAYMENT_METHOD_PAP,
            self::PAYMENT_METHOD_PAY,
            self::PAYMENT_METHOD_PEF,
            self::PAYMENT_METHOD_PFC,
            self::PAYMENT_METHOD_PSC,
            self::PAYMENT_METHOD_REK,
            self::PAYMENT_METHOD_SAM,
            self::PAYMENT_METHOD_SWB,
            self::PAYMENT_METHOD_SCX,
            self::PAYMENT_METHOD_SWP,
            self::PAYMENT_METHOD_TWI,
            self::PAYMENT_METHOD_UAP,
            self::PAYMENT_METHOD_VIS,
            self::PAYMENT_METHOD_WEC,
        ];
    }
    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getLanguageAllowableValues()
    {
        return [
            self::LANGUAGE_EN,
            self::LANGUAGE_DE,
            self::LANGUAGE_FR,
            self::LANGUAGE_IT,
            self::LANGUAGE_ES,
            self::LANGUAGE_EL,
            self::LANGUAGE_NO,
            self::LANGUAGE_DA,
            self::LANGUAGE_PL,
            self::LANGUAGE_PT,
            self::LANGUAGE_RU,
            self::LANGUAGE_JA,
        ];
    }
    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['transaction_id'] = $data['transaction_id'] ?? null;
        $this->container['type'] = $data['type'] ?? null;
        $this->container['status'] = $data['status'] ?? null;
        $this->container['currency'] = $data['currency'] ?? null;
        $this->container['refno'] = $data['refno'] ?? null;
        $this->container['refno2'] = $data['refno2'] ?? null;
        $this->container['payment_method'] = $data['payment_method'] ?? null;
        $this->container['detail'] = $data['detail'] ?? null;
        $this->container['customer'] = $data['customer'] ?? null;
        $this->container['cdm'] = $data['cdm'] ?? null;
        $this->container['language'] = $data['language'] ?? null;
        $this->container['card'] = $data['card'] ?? null;
        $this->container['twi'] = $data['twi'] ?? null;
        $this->container['pap'] = $data['pap'] ?? null;
        $this->container['rek'] = $data['rek'] ?? null;
        $this->container['elv'] = $data['elv'] ?? null;
        $this->container['kln'] = $data['kln'] ?? null;
        $this->container['int'] = $data['int'] ?? null;
        $this->container['swp'] = $data['swp'] ?? null;
        $this->container['mfx'] = $data['mfx'] ?? null;
        $this->container['mpx'] = $data['mpx'] ?? null;
        $this->container['mdp'] = $data['mdp'] ?? null;
        $this->container['esy'] = $data['esy'] ?? null;
        $this->container['pfc'] = $data['pfc'] ?? null;
        $this->container['wec'] = $data['wec'] ?? null;
        $this->container['scx'] = $data['scx'] ?? null;
        $this->container['history'] = $data['history'] ?? null;
        $this->container['ep2'] = $data['ep2'] ?? null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        $allowedValues = $this->getTypeAllowableValues();
        if (!is_null($this->container['type']) && !in_array($this->container['type'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'type', must be one of '%s'",
                $this->container['type'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getStatusAllowableValues();
        if (!is_null($this->container['status']) && !in_array($this->container['status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'status', must be one of '%s'",
                $this->container['status'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getPaymentMethodAllowableValues();
        if (!is_null($this->container['payment_method']) && !in_array($this->container['payment_method'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'payment_method', must be one of '%s'",
                $this->container['payment_method'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getLanguageAllowableValues();
        if (!is_null($this->container['language']) && !in_array($this->container['language'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'language', must be one of '%s'",
                $this->container['language'],
                implode("', '", $allowedValues)
            );
        }

        if (!is_null($this->container['language']) && (mb_strlen($this->container['language']) > 2)) {
            $invalidProperties[] = "invalid value for 'language', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['language']) && (mb_strlen($this->container['language']) < 2)) {
            $invalidProperties[] = "invalid value for 'language', the character length must be bigger than or equal to 2.";
        }

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets transaction_id
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->container['transaction_id'];
    }

    /**
     * Sets transaction_id
     *
     * @param string|null $transaction_id The transactionId received after an authorization.
     *
     * @return self
     */
    public function setTransactionId($transaction_id)
    {
        $this->container['transaction_id'] = $transaction_id;

        return $this;
    }

    /**
     * Gets type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->container['type'];
    }

    /**
     * Sets type
     *
     * @param string|null $type type
     *
     * @return self
     */
    public function setType($type)
    {
        $allowedValues = $this->getTypeAllowableValues();
        if (!is_null($type) && !in_array($type, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'type', must be one of '%s'",
                    $type,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['type'] = $type;

        return $this;
    }

    /**
     * Gets status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param string|null $status status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $allowedValues = $this->getStatusAllowableValues();
        if (!is_null($status) && !in_array($status, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'status', must be one of '%s'",
                    $status,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets currency
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->container['currency'];
    }

    /**
     * Sets currency
     *
     * @param string|null $currency 3 letter <a href='https://en.wikipedia.org/wiki/ISO_4217' target='_blank'>ISO-4217</a> character code. For example `CHF` or `USD`
     *
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->container['currency'] = $currency;

        return $this;
    }

    /**
     * Gets refno
     *
     * @return string|null
     */
    public function getRefno()
    {
        return $this->container['refno'];
    }

    /**
     * Sets refno
     *
     * @param string|null $refno The merchant's reference number. It should be unique for each transaction.
     *
     * @return self
     */
    public function setRefno($refno)
    {
        $this->container['refno'] = $refno;

        return $this;
    }

    /**
     * Gets refno2
     *
     * @return string|null
     */
    public function getRefno2()
    {
        return $this->container['refno2'];
    }

    /**
     * Sets refno2
     *
     * @param string|null $refno2 Optional customer's reference number. Supported by some payment methods or acquirers.
     *
     * @return self
     */
    public function setRefno2($refno2)
    {
        $this->container['refno2'] = $refno2;

        return $this;
    }

    /**
     * Gets payment_method
     *
     * @return string|null
     */
    public function getPaymentMethod()
    {
        return $this->container['payment_method'];
    }

    /**
     * Sets payment_method
     *
     * @param string|null $payment_method payment_method
     *
     * @return self
     */
    public function setPaymentMethod($payment_method)
    {
        $allowedValues = $this->getPaymentMethodAllowableValues();
        if (!is_null($payment_method) && !in_array($payment_method, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'payment_method', must be one of '%s'",
                    $payment_method,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['payment_method'] = $payment_method;

        return $this;
    }

    /**
     * Gets detail
     *
     * @return \Datatrans\Client\Model\Detail|null
     */
    public function getDetail()
    {
        return $this->container['detail'];
    }

    /**
     * Sets detail
     *
     * @param \Datatrans\Client\Model\Detail|null $detail detail
     *
     * @return self
     */
    public function setDetail($detail)
    {
        $this->container['detail'] = $detail;

        return $this;
    }

    /**
     * Gets customer
     *
     * @return \Datatrans\Client\Model\Customer|null
     */
    public function getCustomer()
    {
        return $this->container['customer'];
    }

    /**
     * Sets customer
     *
     * @param \Datatrans\Client\Model\Customer|null $customer customer
     *
     * @return self
     */
    public function setCustomer($customer)
    {
        $this->container['customer'] = $customer;

        return $this;
    }

    /**
     * Gets cdm
     *
     * @return object|null
     */
    public function getCdm()
    {
        return $this->container['cdm'];
    }

    /**
     * Sets cdm
     *
     * @param object|null $cdm The response of the cybersource decision manager.
     *
     * @return self
     */
    public function setCdm($cdm)
    {
        $this->container['cdm'] = $cdm;

        return $this;
    }

    /**
     * Gets language
     *
     * @return string|null
     */
    public function getLanguage()
    {
        return $this->container['language'];
    }

    /**
     * Sets language
     *
     * @param string|null $language The language (language code) in which the payment was presented to the cardholder. The <a href='https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes' target='_blank'>ISO-639-1</a> two letter language codes listed above are supported
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $allowedValues = $this->getLanguageAllowableValues();
        if (!is_null($language) && !in_array($language, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'language', must be one of '%s'",
                    $language,
                    implode("', '", $allowedValues)
                )
            );
        }
        if (!is_null($language) && (mb_strlen($language) > 2)) {
            throw new \InvalidArgumentException('invalid length for $language when calling StatusResponse., must be smaller than or equal to 2.');
        }
        if (!is_null($language) && (mb_strlen($language) < 2)) {
            throw new \InvalidArgumentException('invalid length for $language when calling StatusResponse., must be bigger than or equal to 2.');
        }

        $this->container['language'] = $language;

        return $this;
    }

    /**
     * Gets card
     *
     * @return \Datatrans\Client\Model\CardDetail|null
     */
    public function getCard()
    {
        return $this->container['card'];
    }

    /**
     * Sets card
     *
     * @param \Datatrans\Client\Model\CardDetail|null $card card
     *
     * @return self
     */
    public function setCard($card)
    {
        $this->container['card'] = $card;

        return $this;
    }

    /**
     * Gets twi
     *
     * @return \Datatrans\Client\Model\TwintDetail|null
     */
    public function getTwi()
    {
        return $this->container['twi'];
    }

    /**
     * Sets twi
     *
     * @param \Datatrans\Client\Model\TwintDetail|null $twi twi
     *
     * @return self
     */
    public function setTwi($twi)
    {
        $this->container['twi'] = $twi;

        return $this;
    }

    /**
     * Gets pap
     *
     * @return \Datatrans\Client\Model\PayPalDetail|null
     */
    public function getPap()
    {
        return $this->container['pap'];
    }

    /**
     * Sets pap
     *
     * @param \Datatrans\Client\Model\PayPalDetail|null $pap pap
     *
     * @return self
     */
    public function setPap($pap)
    {
        $this->container['pap'] = $pap;

        return $this;
    }

    /**
     * Gets rek
     *
     * @return \Datatrans\Client\Model\RekaDetail|null
     */
    public function getRek()
    {
        return $this->container['rek'];
    }

    /**
     * Sets rek
     *
     * @param \Datatrans\Client\Model\RekaDetail|null $rek rek
     *
     * @return self
     */
    public function setRek($rek)
    {
        $this->container['rek'] = $rek;

        return $this;
    }

    /**
     * Gets elv
     *
     * @return \Datatrans\Client\Model\ElvDetail|null
     */
    public function getElv()
    {
        return $this->container['elv'];
    }

    /**
     * Sets elv
     *
     * @param \Datatrans\Client\Model\ElvDetail|null $elv elv
     *
     * @return self
     */
    public function setElv($elv)
    {
        $this->container['elv'] = $elv;

        return $this;
    }

    /**
     * Gets kln
     *
     * @return \Datatrans\Client\Model\KlarnaDetail|null
     */
    public function getKln()
    {
        return $this->container['kln'];
    }

    /**
     * Sets kln
     *
     * @param \Datatrans\Client\Model\KlarnaDetail|null $kln kln
     *
     * @return self
     */
    public function setKln($kln)
    {
        $this->container['kln'] = $kln;

        return $this;
    }

    /**
     * Gets int
     *
     * @return \Datatrans\Client\Model\ByjunoDetail|null
     */
    public function getInt()
    {
        return $this->container['int'];
    }

    /**
     * Sets int
     *
     * @param \Datatrans\Client\Model\ByjunoDetail|null $int int
     *
     * @return self
     */
    public function setInt($int)
    {
        $this->container['int'] = $int;

        return $this;
    }

    /**
     * Gets swp
     *
     * @return \Datatrans\Client\Model\SwissPassDetail|null
     */
    public function getSwp()
    {
        return $this->container['swp'];
    }

    /**
     * Sets swp
     *
     * @param \Datatrans\Client\Model\SwissPassDetail|null $swp swp
     *
     * @return self
     */
    public function setSwp($swp)
    {
        $this->container['swp'] = $swp;

        return $this;
    }

    /**
     * Gets mfx
     *
     * @return \Datatrans\Client\Model\MFXDetail|null
     */
    public function getMfx()
    {
        return $this->container['mfx'];
    }

    /**
     * Sets mfx
     *
     * @param \Datatrans\Client\Model\MFXDetail|null $mfx mfx
     *
     * @return self
     */
    public function setMfx($mfx)
    {
        $this->container['mfx'] = $mfx;

        return $this;
    }

    /**
     * Gets mpx
     *
     * @return \Datatrans\Client\Model\MPXDetail|null
     */
    public function getMpx()
    {
        return $this->container['mpx'];
    }

    /**
     * Sets mpx
     *
     * @param \Datatrans\Client\Model\MPXDetail|null $mpx mpx
     *
     * @return self
     */
    public function setMpx($mpx)
    {
        $this->container['mpx'] = $mpx;

        return $this;
    }

    /**
     * Gets mdp
     *
     * @return \Datatrans\Client\Model\MDPDetail|null
     */
    public function getMdp()
    {
        return $this->container['mdp'];
    }

    /**
     * Sets mdp
     *
     * @param \Datatrans\Client\Model\MDPDetail|null $mdp mdp
     *
     * @return self
     */
    public function setMdp($mdp)
    {
        $this->container['mdp'] = $mdp;

        return $this;
    }

    /**
     * Gets esy
     *
     * @return \Datatrans\Client\Model\SwisscomPayDetail|null
     */
    public function getEsy()
    {
        return $this->container['esy'];
    }

    /**
     * Sets esy
     *
     * @param \Datatrans\Client\Model\SwisscomPayDetail|null $esy esy
     *
     * @return self
     */
    public function setEsy($esy)
    {
        $this->container['esy'] = $esy;

        return $this;
    }

    /**
     * Gets pfc
     *
     * @return \Datatrans\Client\Model\PostfinanceDetail|null
     */
    public function getPfc()
    {
        return $this->container['pfc'];
    }

    /**
     * Sets pfc
     *
     * @param \Datatrans\Client\Model\PostfinanceDetail|null $pfc pfc
     *
     * @return self
     */
    public function setPfc($pfc)
    {
        $this->container['pfc'] = $pfc;

        return $this;
    }

    /**
     * Gets wec
     *
     * @return \Datatrans\Client\Model\WeChatDetail|null
     */
    public function getWec()
    {
        return $this->container['wec'];
    }

    /**
     * Sets wec
     *
     * @param \Datatrans\Client\Model\WeChatDetail|null $wec wec
     *
     * @return self
     */
    public function setWec($wec)
    {
        $this->container['wec'] = $wec;

        return $this;
    }

    /**
     * Gets scx
     *
     * @return \Datatrans\Client\Model\SuperCard|null
     */
    public function getScx()
    {
        return $this->container['scx'];
    }

    /**
     * Sets scx
     *
     * @param \Datatrans\Client\Model\SuperCard|null $scx scx
     *
     * @return self
     */
    public function setScx($scx)
    {
        $this->container['scx'] = $scx;

        return $this;
    }

    /**
     * Gets history
     *
     * @return \Datatrans\Client\Model\Action[]|null
     */
    public function getHistory()
    {
        return $this->container['history'];
    }

    /**
     * Sets history
     *
     * @param \Datatrans\Client\Model\Action[]|null $history history
     *
     * @return self
     */
    public function setHistory($history)
    {
        $this->container['history'] = $history;

        return $this;
    }

    /**
     * Gets ep2
     *
     * @return \Datatrans\Client\Model\Ep2|null
     */
    public function getEp2()
    {
        return $this->container['ep2'];
    }

    /**
     * Sets ep2
     *
     * @param \Datatrans\Client\Model\Ep2|null $ep2 ep2
     *
     * @return self
     */
    public function setEp2($ep2)
    {
        $this->container['ep2'] = $ep2;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    public function jsonSerialize()
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


