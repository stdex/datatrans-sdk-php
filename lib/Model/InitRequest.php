<?php
/**
 * InitRequest
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
 * InitRequest Class Doc Comment
 *
 * @category Class
 * @package  Datatrans\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<TKey, TValue>
 * @template TKey int|null
 * @template TValue mixed|null  
 */
class InitRequest implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'InitRequest';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'currency' => 'string',
        'refno' => 'string',
        'refno2' => 'string',
        'auto_settle' => 'bool',
        'customer' => '\Datatrans\Client\Model\CustomerRequest',
        'billing' => '\Datatrans\Client\Model\BillingAddress',
        'shipping' => '\Datatrans\Client\Model\ShippingAddress',
        'order' => '\Datatrans\Client\Model\OrderRequest',
        'card' => '\Datatrans\Client\Model\CardInitRequest',
        'bon' => '\Datatrans\Client\Model\BoncardRequest',
        'pap' => '\Datatrans\Client\Model\PayPalInitRequest',
        'pfc' => '\Datatrans\Client\Model\PfcInitRequest',
        'rek' => '\Datatrans\Client\Model\RekaRequest',
        'kln' => '\Datatrans\Client\Model\KlarnaInitRequest',
        'twi' => '\Datatrans\Client\Model\TwintRequest',
        'int' => '\Datatrans\Client\Model\ByjunoAuthorizeRequest',
        'esy' => '\Datatrans\Client\Model\ESY',
        'airline_data' => '\Datatrans\Client\Model\AirlineDataRequest',
        'amount' => 'int',
        'language' => 'string',
        'payment_methods' => 'string[]',
        'theme' => '\Datatrans\Client\Model\Theme',
        'redirect' => '\Datatrans\Client\Model\RedirectRequest',
        'option' => '\Datatrans\Client\Model\OptionRequest',
        'swp' => '\Datatrans\Client\Model\SwissPassRequest',
        'mfx' => '\Datatrans\Client\Model\MFXRequest',
        'mpx' => '\Datatrans\Client\Model\MPXRequest',
        'azp' => '\Datatrans\Client\Model\AmazonPayRequest',
        'eps' => '\Datatrans\Client\Model\EpsRequest',
        'alp' => '\Datatrans\Client\Model\AlipayRequest',
        'wec' => '\Datatrans\Client\Model\WeChatRequest',
        'swb' => '\Datatrans\Client\Model\SwissBillingRequest',
        'mdp' => '\Datatrans\Client\Model\MDPInitRequest',
        'psc' => '\Datatrans\Client\Model\PaysafecardRequest'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'currency' => null,
        'refno' => null,
        'refno2' => null,
        'auto_settle' => null,
        'customer' => null,
        'billing' => null,
        'shipping' => null,
        'order' => null,
        'card' => null,
        'bon' => null,
        'pap' => null,
        'pfc' => null,
        'rek' => null,
        'kln' => null,
        'twi' => null,
        'int' => null,
        'esy' => null,
        'airline_data' => null,
        'amount' => 'int64',
        'language' => null,
        'payment_methods' => null,
        'theme' => null,
        'redirect' => null,
        'option' => null,
        'swp' => null,
        'mfx' => null,
        'mpx' => null,
        'azp' => null,
        'eps' => null,
        'alp' => null,
        'wec' => null,
        'swb' => null,
        'mdp' => null,
        'psc' => null
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
        'currency' => 'currency',
        'refno' => 'refno',
        'refno2' => 'refno2',
        'auto_settle' => 'autoSettle',
        'customer' => 'customer',
        'billing' => 'billing',
        'shipping' => 'shipping',
        'order' => 'order',
        'card' => 'card',
        'bon' => 'BON',
        'pap' => 'PAP',
        'pfc' => 'PFC',
        'rek' => 'REK',
        'kln' => 'KLN',
        'twi' => 'TWI',
        'int' => 'INT',
        'esy' => 'ESY',
        'airline_data' => 'airlineData',
        'amount' => 'amount',
        'language' => 'language',
        'payment_methods' => 'paymentMethods',
        'theme' => 'theme',
        'redirect' => 'redirect',
        'option' => 'option',
        'swp' => 'SWP',
        'mfx' => 'MFX',
        'mpx' => 'MPX',
        'azp' => 'AZP',
        'eps' => 'EPS',
        'alp' => 'ALP',
        'wec' => 'WEC',
        'swb' => 'SWB',
        'mdp' => 'MDP',
        'psc' => 'PSC'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'currency' => 'setCurrency',
        'refno' => 'setRefno',
        'refno2' => 'setRefno2',
        'auto_settle' => 'setAutoSettle',
        'customer' => 'setCustomer',
        'billing' => 'setBilling',
        'shipping' => 'setShipping',
        'order' => 'setOrder',
        'card' => 'setCard',
        'bon' => 'setBon',
        'pap' => 'setPap',
        'pfc' => 'setPfc',
        'rek' => 'setRek',
        'kln' => 'setKln',
        'twi' => 'setTwi',
        'int' => 'setInt',
        'esy' => 'setEsy',
        'airline_data' => 'setAirlineData',
        'amount' => 'setAmount',
        'language' => 'setLanguage',
        'payment_methods' => 'setPaymentMethods',
        'theme' => 'setTheme',
        'redirect' => 'setRedirect',
        'option' => 'setOption',
        'swp' => 'setSwp',
        'mfx' => 'setMfx',
        'mpx' => 'setMpx',
        'azp' => 'setAzp',
        'eps' => 'setEps',
        'alp' => 'setAlp',
        'wec' => 'setWec',
        'swb' => 'setSwb',
        'mdp' => 'setMdp',
        'psc' => 'setPsc'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'currency' => 'getCurrency',
        'refno' => 'getRefno',
        'refno2' => 'getRefno2',
        'auto_settle' => 'getAutoSettle',
        'customer' => 'getCustomer',
        'billing' => 'getBilling',
        'shipping' => 'getShipping',
        'order' => 'getOrder',
        'card' => 'getCard',
        'bon' => 'getBon',
        'pap' => 'getPap',
        'pfc' => 'getPfc',
        'rek' => 'getRek',
        'kln' => 'getKln',
        'twi' => 'getTwi',
        'int' => 'getInt',
        'esy' => 'getEsy',
        'airline_data' => 'getAirlineData',
        'amount' => 'getAmount',
        'language' => 'getLanguage',
        'payment_methods' => 'getPaymentMethods',
        'theme' => 'getTheme',
        'redirect' => 'getRedirect',
        'option' => 'getOption',
        'swp' => 'getSwp',
        'mfx' => 'getMfx',
        'mpx' => 'getMpx',
        'azp' => 'getAzp',
        'eps' => 'getEps',
        'alp' => 'getAlp',
        'wec' => 'getWec',
        'swb' => 'getSwb',
        'mdp' => 'getMdp',
        'psc' => 'getPsc'
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
    const PAYMENT_METHODS_ACC = 'ACC';
    const PAYMENT_METHODS_ALP = 'ALP';
    const PAYMENT_METHODS_APL = 'APL';
    const PAYMENT_METHODS_AMX = 'AMX';
    const PAYMENT_METHODS_AZP = 'AZP';
    const PAYMENT_METHODS_BON = 'BON';
    const PAYMENT_METHODS_CFY = 'CFY';
    const PAYMENT_METHODS_CSY = 'CSY';
    const PAYMENT_METHODS_CUP = 'CUP';
    const PAYMENT_METHODS_DIN = 'DIN';
    const PAYMENT_METHODS_DII = 'DII';
    const PAYMENT_METHODS_DIB = 'DIB';
    const PAYMENT_METHODS_DIS = 'DIS';
    const PAYMENT_METHODS_DNK = 'DNK';
    const PAYMENT_METHODS_ECA = 'ECA';
    const PAYMENT_METHODS_ELV = 'ELV';
    const PAYMENT_METHODS_EPS = 'EPS';
    const PAYMENT_METHODS_ESY = 'ESY';
    const PAYMENT_METHODS_INT = 'INT';
    const PAYMENT_METHODS_JCB = 'JCB';
    const PAYMENT_METHODS_JEL = 'JEL';
    const PAYMENT_METHODS_KLN = 'KLN';
    const PAYMENT_METHODS_MAU = 'MAU';
    const PAYMENT_METHODS_MDP = 'MDP';
    const PAYMENT_METHODS_MFX = 'MFX';
    const PAYMENT_METHODS_MPX = 'MPX';
    const PAYMENT_METHODS_MYO = 'MYO';
    const PAYMENT_METHODS_PAP = 'PAP';
    const PAYMENT_METHODS_PAY = 'PAY';
    const PAYMENT_METHODS_PEF = 'PEF';
    const PAYMENT_METHODS_PFC = 'PFC';
    const PAYMENT_METHODS_PSC = 'PSC';
    const PAYMENT_METHODS_REK = 'REK';
    const PAYMENT_METHODS_SAM = 'SAM';
    const PAYMENT_METHODS_SWB = 'SWB';
    const PAYMENT_METHODS_SCX = 'SCX';
    const PAYMENT_METHODS_SWP = 'SWP';
    const PAYMENT_METHODS_TWI = 'TWI';
    const PAYMENT_METHODS_UAP = 'UAP';
    const PAYMENT_METHODS_VIS = 'VIS';
    const PAYMENT_METHODS_WEC = 'WEC';
    

    
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
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getPaymentMethodsAllowableValues()
    {
        return [
            self::PAYMENT_METHODS_ACC,
            self::PAYMENT_METHODS_ALP,
            self::PAYMENT_METHODS_APL,
            self::PAYMENT_METHODS_AMX,
            self::PAYMENT_METHODS_AZP,
            self::PAYMENT_METHODS_BON,
            self::PAYMENT_METHODS_CFY,
            self::PAYMENT_METHODS_CSY,
            self::PAYMENT_METHODS_CUP,
            self::PAYMENT_METHODS_DIN,
            self::PAYMENT_METHODS_DII,
            self::PAYMENT_METHODS_DIB,
            self::PAYMENT_METHODS_DIS,
            self::PAYMENT_METHODS_DNK,
            self::PAYMENT_METHODS_ECA,
            self::PAYMENT_METHODS_ELV,
            self::PAYMENT_METHODS_EPS,
            self::PAYMENT_METHODS_ESY,
            self::PAYMENT_METHODS_INT,
            self::PAYMENT_METHODS_JCB,
            self::PAYMENT_METHODS_JEL,
            self::PAYMENT_METHODS_KLN,
            self::PAYMENT_METHODS_MAU,
            self::PAYMENT_METHODS_MDP,
            self::PAYMENT_METHODS_MFX,
            self::PAYMENT_METHODS_MPX,
            self::PAYMENT_METHODS_MYO,
            self::PAYMENT_METHODS_PAP,
            self::PAYMENT_METHODS_PAY,
            self::PAYMENT_METHODS_PEF,
            self::PAYMENT_METHODS_PFC,
            self::PAYMENT_METHODS_PSC,
            self::PAYMENT_METHODS_REK,
            self::PAYMENT_METHODS_SAM,
            self::PAYMENT_METHODS_SWB,
            self::PAYMENT_METHODS_SCX,
            self::PAYMENT_METHODS_SWP,
            self::PAYMENT_METHODS_TWI,
            self::PAYMENT_METHODS_UAP,
            self::PAYMENT_METHODS_VIS,
            self::PAYMENT_METHODS_WEC,
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
        $this->container['currency'] = $data['currency'] ?? null;
        $this->container['refno'] = $data['refno'] ?? null;
        $this->container['refno2'] = $data['refno2'] ?? null;
        $this->container['auto_settle'] = $data['auto_settle'] ?? null;
        $this->container['customer'] = $data['customer'] ?? null;
        $this->container['billing'] = $data['billing'] ?? null;
        $this->container['shipping'] = $data['shipping'] ?? null;
        $this->container['order'] = $data['order'] ?? null;
        $this->container['card'] = $data['card'] ?? null;
        $this->container['bon'] = $data['bon'] ?? null;
        $this->container['pap'] = $data['pap'] ?? null;
        $this->container['pfc'] = $data['pfc'] ?? null;
        $this->container['rek'] = $data['rek'] ?? null;
        $this->container['kln'] = $data['kln'] ?? null;
        $this->container['twi'] = $data['twi'] ?? null;
        $this->container['int'] = $data['int'] ?? null;
        $this->container['esy'] = $data['esy'] ?? null;
        $this->container['airline_data'] = $data['airline_data'] ?? null;
        $this->container['amount'] = $data['amount'] ?? null;
        $this->container['language'] = $data['language'] ?? null;
        $this->container['payment_methods'] = $data['payment_methods'] ?? null;
        $this->container['theme'] = $data['theme'] ?? null;
        $this->container['redirect'] = $data['redirect'] ?? null;
        $this->container['option'] = $data['option'] ?? null;
        $this->container['swp'] = $data['swp'] ?? null;
        $this->container['mfx'] = $data['mfx'] ?? null;
        $this->container['mpx'] = $data['mpx'] ?? null;
        $this->container['azp'] = $data['azp'] ?? null;
        $this->container['eps'] = $data['eps'] ?? null;
        $this->container['alp'] = $data['alp'] ?? null;
        $this->container['wec'] = $data['wec'] ?? null;
        $this->container['swb'] = $data['swb'] ?? null;
        $this->container['mdp'] = $data['mdp'] ?? null;
        $this->container['psc'] = $data['psc'] ?? null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['currency'] === null) {
            $invalidProperties[] = "'currency' can't be null";
        }
        if ((mb_strlen($this->container['currency']) > 3)) {
            $invalidProperties[] = "invalid value for 'currency', the character length must be smaller than or equal to 3.";
        }

        if ((mb_strlen($this->container['currency']) < 3)) {
            $invalidProperties[] = "invalid value for 'currency', the character length must be bigger than or equal to 3.";
        }

        if ($this->container['refno'] === null) {
            $invalidProperties[] = "'refno' can't be null";
        }
        if ((mb_strlen($this->container['refno']) > 20)) {
            $invalidProperties[] = "invalid value for 'refno', the character length must be smaller than or equal to 20.";
        }

        if ((mb_strlen($this->container['refno']) < 1)) {
            $invalidProperties[] = "invalid value for 'refno', the character length must be bigger than or equal to 1.";
        }

        if (!is_null($this->container['refno2']) && (mb_strlen($this->container['refno2']) > 17)) {
            $invalidProperties[] = "invalid value for 'refno2', the character length must be smaller than or equal to 17.";
        }

        if (!is_null($this->container['refno2']) && (mb_strlen($this->container['refno2']) < 0)) {
            $invalidProperties[] = "invalid value for 'refno2', the character length must be bigger than or equal to 0.";
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
     * Gets currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->container['currency'];
    }

    /**
     * Sets currency
     *
     * @param string $currency 3 letter <a href='https://en.wikipedia.org/wiki/ISO_4217' target='_blank'>ISO-4217</a> character code. For example `CHF` or `USD`
     *
     * @return self
     */
    public function setCurrency($currency)
    {
        if ((mb_strlen($currency) > 3)) {
            throw new \InvalidArgumentException('invalid length for $currency when calling InitRequest., must be smaller than or equal to 3.');
        }
        if ((mb_strlen($currency) < 3)) {
            throw new \InvalidArgumentException('invalid length for $currency when calling InitRequest., must be bigger than or equal to 3.');
        }

        $this->container['currency'] = $currency;

        return $this;
    }

    /**
     * Gets refno
     *
     * @return string
     */
    public function getRefno()
    {
        return $this->container['refno'];
    }

    /**
     * Sets refno
     *
     * @param string $refno The merchant's reference number. It should be unique for each transaction.
     *
     * @return self
     */
    public function setRefno($refno)
    {
        if ((mb_strlen($refno) > 20)) {
            throw new \InvalidArgumentException('invalid length for $refno when calling InitRequest., must be smaller than or equal to 20.');
        }
        if ((mb_strlen($refno) < 1)) {
            throw new \InvalidArgumentException('invalid length for $refno when calling InitRequest., must be bigger than or equal to 1.');
        }

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
        if (!is_null($refno2) && (mb_strlen($refno2) > 17)) {
            throw new \InvalidArgumentException('invalid length for $refno2 when calling InitRequest., must be smaller than or equal to 17.');
        }
        if (!is_null($refno2) && (mb_strlen($refno2) < 0)) {
            throw new \InvalidArgumentException('invalid length for $refno2 when calling InitRequest., must be bigger than or equal to 0.');
        }

        $this->container['refno2'] = $refno2;

        return $this;
    }

    /**
     * Gets auto_settle
     *
     * @return bool|null
     */
    public function getAutoSettle()
    {
        return $this->container['auto_settle'];
    }

    /**
     * Sets auto_settle
     *
     * @param bool|null $auto_settle Whether to automatically settle the transaction after an authorization or not. If not present, the settings defined in the dashboard ('Authorisation / Settlement' or 'Direct Debit') will be used.
     *
     * @return self
     */
    public function setAutoSettle($auto_settle)
    {
        $this->container['auto_settle'] = $auto_settle;

        return $this;
    }

    /**
     * Gets customer
     *
     * @return \Datatrans\Client\Model\CustomerRequest|null
     */
    public function getCustomer()
    {
        return $this->container['customer'];
    }

    /**
     * Sets customer
     *
     * @param \Datatrans\Client\Model\CustomerRequest|null $customer customer
     *
     * @return self
     */
    public function setCustomer($customer)
    {
        $this->container['customer'] = $customer;

        return $this;
    }

    /**
     * Gets billing
     *
     * @return \Datatrans\Client\Model\BillingAddress|null
     */
    public function getBilling()
    {
        return $this->container['billing'];
    }

    /**
     * Sets billing
     *
     * @param \Datatrans\Client\Model\BillingAddress|null $billing billing
     *
     * @return self
     */
    public function setBilling($billing)
    {
        $this->container['billing'] = $billing;

        return $this;
    }

    /**
     * Gets shipping
     *
     * @return \Datatrans\Client\Model\ShippingAddress|null
     */
    public function getShipping()
    {
        return $this->container['shipping'];
    }

    /**
     * Sets shipping
     *
     * @param \Datatrans\Client\Model\ShippingAddress|null $shipping shipping
     *
     * @return self
     */
    public function setShipping($shipping)
    {
        $this->container['shipping'] = $shipping;

        return $this;
    }

    /**
     * Gets order
     *
     * @return \Datatrans\Client\Model\OrderRequest|null
     */
    public function getOrder()
    {
        return $this->container['order'];
    }

    /**
     * Sets order
     *
     * @param \Datatrans\Client\Model\OrderRequest|null $order order
     *
     * @return self
     */
    public function setOrder($order)
    {
        $this->container['order'] = $order;

        return $this;
    }

    /**
     * Gets card
     *
     * @return \Datatrans\Client\Model\CardInitRequest|null
     */
    public function getCard()
    {
        return $this->container['card'];
    }

    /**
     * Sets card
     *
     * @param \Datatrans\Client\Model\CardInitRequest|null $card card
     *
     * @return self
     */
    public function setCard($card)
    {
        $this->container['card'] = $card;

        return $this;
    }

    /**
     * Gets bon
     *
     * @return \Datatrans\Client\Model\BoncardRequest|null
     */
    public function getBon()
    {
        return $this->container['bon'];
    }

    /**
     * Sets bon
     *
     * @param \Datatrans\Client\Model\BoncardRequest|null $bon bon
     *
     * @return self
     */
    public function setBon($bon)
    {
        $this->container['bon'] = $bon;

        return $this;
    }

    /**
     * Gets pap
     *
     * @return \Datatrans\Client\Model\PayPalInitRequest|null
     */
    public function getPap()
    {
        return $this->container['pap'];
    }

    /**
     * Sets pap
     *
     * @param \Datatrans\Client\Model\PayPalInitRequest|null $pap pap
     *
     * @return self
     */
    public function setPap($pap)
    {
        $this->container['pap'] = $pap;

        return $this;
    }

    /**
     * Gets pfc
     *
     * @return \Datatrans\Client\Model\PfcInitRequest|null
     */
    public function getPfc()
    {
        return $this->container['pfc'];
    }

    /**
     * Sets pfc
     *
     * @param \Datatrans\Client\Model\PfcInitRequest|null $pfc pfc
     *
     * @return self
     */
    public function setPfc($pfc)
    {
        $this->container['pfc'] = $pfc;

        return $this;
    }

    /**
     * Gets rek
     *
     * @return \Datatrans\Client\Model\RekaRequest|null
     */
    public function getRek()
    {
        return $this->container['rek'];
    }

    /**
     * Sets rek
     *
     * @param \Datatrans\Client\Model\RekaRequest|null $rek rek
     *
     * @return self
     */
    public function setRek($rek)
    {
        $this->container['rek'] = $rek;

        return $this;
    }

    /**
     * Gets kln
     *
     * @return \Datatrans\Client\Model\KlarnaInitRequest|null
     */
    public function getKln()
    {
        return $this->container['kln'];
    }

    /**
     * Sets kln
     *
     * @param \Datatrans\Client\Model\KlarnaInitRequest|null $kln kln
     *
     * @return self
     */
    public function setKln($kln)
    {
        $this->container['kln'] = $kln;

        return $this;
    }

    /**
     * Gets twi
     *
     * @return \Datatrans\Client\Model\TwintRequest|null
     */
    public function getTwi()
    {
        return $this->container['twi'];
    }

    /**
     * Sets twi
     *
     * @param \Datatrans\Client\Model\TwintRequest|null $twi twi
     *
     * @return self
     */
    public function setTwi($twi)
    {
        $this->container['twi'] = $twi;

        return $this;
    }

    /**
     * Gets int
     *
     * @return \Datatrans\Client\Model\ByjunoAuthorizeRequest|null
     */
    public function getInt()
    {
        return $this->container['int'];
    }

    /**
     * Sets int
     *
     * @param \Datatrans\Client\Model\ByjunoAuthorizeRequest|null $int int
     *
     * @return self
     */
    public function setInt($int)
    {
        $this->container['int'] = $int;

        return $this;
    }

    /**
     * Gets esy
     *
     * @return \Datatrans\Client\Model\ESY|null
     */
    public function getEsy()
    {
        return $this->container['esy'];
    }

    /**
     * Sets esy
     *
     * @param \Datatrans\Client\Model\ESY|null $esy esy
     *
     * @return self
     */
    public function setEsy($esy)
    {
        $this->container['esy'] = $esy;

        return $this;
    }

    /**
     * Gets airline_data
     *
     * @return \Datatrans\Client\Model\AirlineDataRequest|null
     */
    public function getAirlineData()
    {
        return $this->container['airline_data'];
    }

    /**
     * Sets airline_data
     *
     * @param \Datatrans\Client\Model\AirlineDataRequest|null $airline_data airline_data
     *
     * @return self
     */
    public function setAirlineData($airline_data)
    {
        $this->container['airline_data'] = $airline_data;

        return $this;
    }

    /**
     * Gets amount
     *
     * @return int|null
     */
    public function getAmount()
    {
        return $this->container['amount'];
    }

    /**
     * Sets amount
     *
     * @param int|null $amount The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. Can be omitted for use cases where only a registration should take place (if the payment method supports registrations)
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->container['amount'] = $amount;

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
     * @param string|null $language This parameter specifies the language (language code) in which the payment page should be presented to the cardholder. The <a href='https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes' target='_blank'>ISO-639-1</a> two letter language codes listed above are supported
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
            throw new \InvalidArgumentException('invalid length for $language when calling InitRequest., must be smaller than or equal to 2.');
        }
        if (!is_null($language) && (mb_strlen($language) < 2)) {
            throw new \InvalidArgumentException('invalid length for $language when calling InitRequest., must be bigger than or equal to 2.');
        }

        $this->container['language'] = $language;

        return $this;
    }

    /**
     * Gets payment_methods
     *
     * @return string[]|null
     */
    public function getPaymentMethods()
    {
        return $this->container['payment_methods'];
    }

    /**
     * Sets payment_methods
     *
     * @param string[]|null $payment_methods An array of payment method shortnames. For example `[\"VIS\", \"PFC\"]`. If omitted, all available payment methods will be displayed on the payment page. If the Mobile SDKs are used (`returnMobileToken`), this array is mandatory.
     *
     * @return self
     */
    public function setPaymentMethods($payment_methods)
    {
        $allowedValues = $this->getPaymentMethodsAllowableValues();
        if (!is_null($payment_methods) && array_diff($payment_methods, $allowedValues)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'payment_methods', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['payment_methods'] = $payment_methods;

        return $this;
    }

    /**
     * Gets theme
     *
     * @return \Datatrans\Client\Model\Theme|null
     */
    public function getTheme()
    {
        return $this->container['theme'];
    }

    /**
     * Sets theme
     *
     * @param \Datatrans\Client\Model\Theme|null $theme theme
     *
     * @return self
     */
    public function setTheme($theme)
    {
        $this->container['theme'] = $theme;

        return $this;
    }

    /**
     * Gets redirect
     *
     * @return \Datatrans\Client\Model\RedirectRequest|null
     */
    public function getRedirect()
    {
        return $this->container['redirect'];
    }

    /**
     * Sets redirect
     *
     * @param \Datatrans\Client\Model\RedirectRequest|null $redirect redirect
     *
     * @return self
     */
    public function setRedirect($redirect)
    {
        $this->container['redirect'] = $redirect;

        return $this;
    }

    /**
     * Gets option
     *
     * @return \Datatrans\Client\Model\OptionRequest|null
     */
    public function getOption()
    {
        return $this->container['option'];
    }

    /**
     * Sets option
     *
     * @param \Datatrans\Client\Model\OptionRequest|null $option option
     *
     * @return self
     */
    public function setOption($option)
    {
        $this->container['option'] = $option;

        return $this;
    }

    /**
     * Gets swp
     *
     * @return \Datatrans\Client\Model\SwissPassRequest|null
     */
    public function getSwp()
    {
        return $this->container['swp'];
    }

    /**
     * Sets swp
     *
     * @param \Datatrans\Client\Model\SwissPassRequest|null $swp swp
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
     * @return \Datatrans\Client\Model\MFXRequest|null
     */
    public function getMfx()
    {
        return $this->container['mfx'];
    }

    /**
     * Sets mfx
     *
     * @param \Datatrans\Client\Model\MFXRequest|null $mfx mfx
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
     * @return \Datatrans\Client\Model\MPXRequest|null
     */
    public function getMpx()
    {
        return $this->container['mpx'];
    }

    /**
     * Sets mpx
     *
     * @param \Datatrans\Client\Model\MPXRequest|null $mpx mpx
     *
     * @return self
     */
    public function setMpx($mpx)
    {
        $this->container['mpx'] = $mpx;

        return $this;
    }

    /**
     * Gets azp
     *
     * @return \Datatrans\Client\Model\AmazonPayRequest|null
     */
    public function getAzp()
    {
        return $this->container['azp'];
    }

    /**
     * Sets azp
     *
     * @param \Datatrans\Client\Model\AmazonPayRequest|null $azp azp
     *
     * @return self
     */
    public function setAzp($azp)
    {
        $this->container['azp'] = $azp;

        return $this;
    }

    /**
     * Gets eps
     *
     * @return \Datatrans\Client\Model\EpsRequest|null
     */
    public function getEps()
    {
        return $this->container['eps'];
    }

    /**
     * Sets eps
     *
     * @param \Datatrans\Client\Model\EpsRequest|null $eps eps
     *
     * @return self
     */
    public function setEps($eps)
    {
        $this->container['eps'] = $eps;

        return $this;
    }

    /**
     * Gets alp
     *
     * @return \Datatrans\Client\Model\AlipayRequest|null
     */
    public function getAlp()
    {
        return $this->container['alp'];
    }

    /**
     * Sets alp
     *
     * @param \Datatrans\Client\Model\AlipayRequest|null $alp alp
     *
     * @return self
     */
    public function setAlp($alp)
    {
        $this->container['alp'] = $alp;

        return $this;
    }

    /**
     * Gets wec
     *
     * @return \Datatrans\Client\Model\WeChatRequest|null
     */
    public function getWec()
    {
        return $this->container['wec'];
    }

    /**
     * Sets wec
     *
     * @param \Datatrans\Client\Model\WeChatRequest|null $wec wec
     *
     * @return self
     */
    public function setWec($wec)
    {
        $this->container['wec'] = $wec;

        return $this;
    }

    /**
     * Gets swb
     *
     * @return \Datatrans\Client\Model\SwissBillingRequest|null
     */
    public function getSwb()
    {
        return $this->container['swb'];
    }

    /**
     * Sets swb
     *
     * @param \Datatrans\Client\Model\SwissBillingRequest|null $swb swb
     *
     * @return self
     */
    public function setSwb($swb)
    {
        $this->container['swb'] = $swb;

        return $this;
    }

    /**
     * Gets mdp
     *
     * @return \Datatrans\Client\Model\MDPInitRequest|null
     */
    public function getMdp()
    {
        return $this->container['mdp'];
    }

    /**
     * Sets mdp
     *
     * @param \Datatrans\Client\Model\MDPInitRequest|null $mdp mdp
     *
     * @return self
     */
    public function setMdp($mdp)
    {
        $this->container['mdp'] = $mdp;

        return $this;
    }

    /**
     * Gets psc
     *
     * @return \Datatrans\Client\Model\PaysafecardRequest|null
     */
    public function getPsc()
    {
        return $this->container['psc'];
    }

    /**
     * Sets psc
     *
     * @param \Datatrans\Client\Model\PaysafecardRequest|null $psc psc
     *
     * @return self
     */
    public function setPsc($psc)
    {
        $this->container['psc'] = $psc;

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


