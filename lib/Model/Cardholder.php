<?php
/**
 * Cardholder
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
 * Cardholder Class Doc Comment
 *
 * @category Class
 * @package  Datatrans\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<TKey, TValue>
 * @template TKey int|null
 * @template TValue mixed|null  
 */
class Cardholder implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Cardholder';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'addr_match' => 'string',
        'bill_addr_city' => 'string',
        'bill_addr_country' => 'string',
        'bill_addr_line1' => 'string',
        'bill_addr_line2' => 'string',
        'bill_addr_line3' => 'string',
        'bill_addr_post_code' => 'string',
        'bill_addr_state' => 'string',
        'email' => 'string',
        'home_phone' => '\Datatrans\Client\Model\CardholderPhoneNumber',
        'mobile_phone' => '\Datatrans\Client\Model\CardholderPhoneNumber',
        'work_phone' => '\Datatrans\Client\Model\CardholderPhoneNumber',
        'cardholder_name' => 'string',
        'ship_addr_city' => 'string',
        'ship_addr_country' => 'string',
        'ship_addr_line1' => 'string',
        'ship_addr_line2' => 'string',
        'ship_addr_line3' => 'string',
        'ship_addr_post_code' => 'string',
        'ship_addr_state' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'addr_match' => null,
        'bill_addr_city' => null,
        'bill_addr_country' => null,
        'bill_addr_line1' => null,
        'bill_addr_line2' => null,
        'bill_addr_line3' => null,
        'bill_addr_post_code' => null,
        'bill_addr_state' => null,
        'email' => null,
        'home_phone' => null,
        'mobile_phone' => null,
        'work_phone' => null,
        'cardholder_name' => null,
        'ship_addr_city' => null,
        'ship_addr_country' => null,
        'ship_addr_line1' => null,
        'ship_addr_line2' => null,
        'ship_addr_line3' => null,
        'ship_addr_post_code' => null,
        'ship_addr_state' => null
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
        'addr_match' => 'addrMatch',
        'bill_addr_city' => 'billAddrCity',
        'bill_addr_country' => 'billAddrCountry',
        'bill_addr_line1' => 'billAddrLine1',
        'bill_addr_line2' => 'billAddrLine2',
        'bill_addr_line3' => 'billAddrLine3',
        'bill_addr_post_code' => 'billAddrPostCode',
        'bill_addr_state' => 'billAddrState',
        'email' => 'email',
        'home_phone' => 'homePhone',
        'mobile_phone' => 'mobilePhone',
        'work_phone' => 'workPhone',
        'cardholder_name' => 'cardholderName',
        'ship_addr_city' => 'shipAddrCity',
        'ship_addr_country' => 'shipAddrCountry',
        'ship_addr_line1' => 'shipAddrLine1',
        'ship_addr_line2' => 'shipAddrLine2',
        'ship_addr_line3' => 'shipAddrLine3',
        'ship_addr_post_code' => 'shipAddrPostCode',
        'ship_addr_state' => 'shipAddrState'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'addr_match' => 'setAddrMatch',
        'bill_addr_city' => 'setBillAddrCity',
        'bill_addr_country' => 'setBillAddrCountry',
        'bill_addr_line1' => 'setBillAddrLine1',
        'bill_addr_line2' => 'setBillAddrLine2',
        'bill_addr_line3' => 'setBillAddrLine3',
        'bill_addr_post_code' => 'setBillAddrPostCode',
        'bill_addr_state' => 'setBillAddrState',
        'email' => 'setEmail',
        'home_phone' => 'setHomePhone',
        'mobile_phone' => 'setMobilePhone',
        'work_phone' => 'setWorkPhone',
        'cardholder_name' => 'setCardholderName',
        'ship_addr_city' => 'setShipAddrCity',
        'ship_addr_country' => 'setShipAddrCountry',
        'ship_addr_line1' => 'setShipAddrLine1',
        'ship_addr_line2' => 'setShipAddrLine2',
        'ship_addr_line3' => 'setShipAddrLine3',
        'ship_addr_post_code' => 'setShipAddrPostCode',
        'ship_addr_state' => 'setShipAddrState'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'addr_match' => 'getAddrMatch',
        'bill_addr_city' => 'getBillAddrCity',
        'bill_addr_country' => 'getBillAddrCountry',
        'bill_addr_line1' => 'getBillAddrLine1',
        'bill_addr_line2' => 'getBillAddrLine2',
        'bill_addr_line3' => 'getBillAddrLine3',
        'bill_addr_post_code' => 'getBillAddrPostCode',
        'bill_addr_state' => 'getBillAddrState',
        'email' => 'getEmail',
        'home_phone' => 'getHomePhone',
        'mobile_phone' => 'getMobilePhone',
        'work_phone' => 'getWorkPhone',
        'cardholder_name' => 'getCardholderName',
        'ship_addr_city' => 'getShipAddrCity',
        'ship_addr_country' => 'getShipAddrCountry',
        'ship_addr_line1' => 'getShipAddrLine1',
        'ship_addr_line2' => 'getShipAddrLine2',
        'ship_addr_line3' => 'getShipAddrLine3',
        'ship_addr_post_code' => 'getShipAddrPostCode',
        'ship_addr_state' => 'getShipAddrState'
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

    const ADDR_MATCH_Y = 'Y';
    const ADDR_MATCH_N = 'N';
    

    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getAddrMatchAllowableValues()
    {
        return [
            self::ADDR_MATCH_Y,
            self::ADDR_MATCH_N,
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
        $this->container['addr_match'] = $data['addr_match'] ?? null;
        $this->container['bill_addr_city'] = $data['bill_addr_city'] ?? null;
        $this->container['bill_addr_country'] = $data['bill_addr_country'] ?? null;
        $this->container['bill_addr_line1'] = $data['bill_addr_line1'] ?? null;
        $this->container['bill_addr_line2'] = $data['bill_addr_line2'] ?? null;
        $this->container['bill_addr_line3'] = $data['bill_addr_line3'] ?? null;
        $this->container['bill_addr_post_code'] = $data['bill_addr_post_code'] ?? null;
        $this->container['bill_addr_state'] = $data['bill_addr_state'] ?? null;
        $this->container['email'] = $data['email'] ?? null;
        $this->container['home_phone'] = $data['home_phone'] ?? null;
        $this->container['mobile_phone'] = $data['mobile_phone'] ?? null;
        $this->container['work_phone'] = $data['work_phone'] ?? null;
        $this->container['cardholder_name'] = $data['cardholder_name'] ?? null;
        $this->container['ship_addr_city'] = $data['ship_addr_city'] ?? null;
        $this->container['ship_addr_country'] = $data['ship_addr_country'] ?? null;
        $this->container['ship_addr_line1'] = $data['ship_addr_line1'] ?? null;
        $this->container['ship_addr_line2'] = $data['ship_addr_line2'] ?? null;
        $this->container['ship_addr_line3'] = $data['ship_addr_line3'] ?? null;
        $this->container['ship_addr_post_code'] = $data['ship_addr_post_code'] ?? null;
        $this->container['ship_addr_state'] = $data['ship_addr_state'] ?? null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        $allowedValues = $this->getAddrMatchAllowableValues();
        if (!is_null($this->container['addr_match']) && !in_array($this->container['addr_match'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'addr_match', must be one of '%s'",
                $this->container['addr_match'],
                implode("', '", $allowedValues)
            );
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
     * Gets addr_match
     *
     * @return string|null
     */
    public function getAddrMatch()
    {
        return $this->container['addr_match'];
    }

    /**
     * Sets addr_match
     *
     * @param string|null $addr_match addr_match
     *
     * @return self
     */
    public function setAddrMatch($addr_match)
    {
        $allowedValues = $this->getAddrMatchAllowableValues();
        if (!is_null($addr_match) && !in_array($addr_match, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'addr_match', must be one of '%s'",
                    $addr_match,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['addr_match'] = $addr_match;

        return $this;
    }

    /**
     * Gets bill_addr_city
     *
     * @return string|null
     */
    public function getBillAddrCity()
    {
        return $this->container['bill_addr_city'];
    }

    /**
     * Sets bill_addr_city
     *
     * @param string|null $bill_addr_city bill_addr_city
     *
     * @return self
     */
    public function setBillAddrCity($bill_addr_city)
    {
        $this->container['bill_addr_city'] = $bill_addr_city;

        return $this;
    }

    /**
     * Gets bill_addr_country
     *
     * @return string|null
     */
    public function getBillAddrCountry()
    {
        return $this->container['bill_addr_country'];
    }

    /**
     * Sets bill_addr_country
     *
     * @param string|null $bill_addr_country bill_addr_country
     *
     * @return self
     */
    public function setBillAddrCountry($bill_addr_country)
    {
        $this->container['bill_addr_country'] = $bill_addr_country;

        return $this;
    }

    /**
     * Gets bill_addr_line1
     *
     * @return string|null
     */
    public function getBillAddrLine1()
    {
        return $this->container['bill_addr_line1'];
    }

    /**
     * Sets bill_addr_line1
     *
     * @param string|null $bill_addr_line1 bill_addr_line1
     *
     * @return self
     */
    public function setBillAddrLine1($bill_addr_line1)
    {
        $this->container['bill_addr_line1'] = $bill_addr_line1;

        return $this;
    }

    /**
     * Gets bill_addr_line2
     *
     * @return string|null
     */
    public function getBillAddrLine2()
    {
        return $this->container['bill_addr_line2'];
    }

    /**
     * Sets bill_addr_line2
     *
     * @param string|null $bill_addr_line2 bill_addr_line2
     *
     * @return self
     */
    public function setBillAddrLine2($bill_addr_line2)
    {
        $this->container['bill_addr_line2'] = $bill_addr_line2;

        return $this;
    }

    /**
     * Gets bill_addr_line3
     *
     * @return string|null
     */
    public function getBillAddrLine3()
    {
        return $this->container['bill_addr_line3'];
    }

    /**
     * Sets bill_addr_line3
     *
     * @param string|null $bill_addr_line3 bill_addr_line3
     *
     * @return self
     */
    public function setBillAddrLine3($bill_addr_line3)
    {
        $this->container['bill_addr_line3'] = $bill_addr_line3;

        return $this;
    }

    /**
     * Gets bill_addr_post_code
     *
     * @return string|null
     */
    public function getBillAddrPostCode()
    {
        return $this->container['bill_addr_post_code'];
    }

    /**
     * Sets bill_addr_post_code
     *
     * @param string|null $bill_addr_post_code bill_addr_post_code
     *
     * @return self
     */
    public function setBillAddrPostCode($bill_addr_post_code)
    {
        $this->container['bill_addr_post_code'] = $bill_addr_post_code;

        return $this;
    }

    /**
     * Gets bill_addr_state
     *
     * @return string|null
     */
    public function getBillAddrState()
    {
        return $this->container['bill_addr_state'];
    }

    /**
     * Sets bill_addr_state
     *
     * @param string|null $bill_addr_state bill_addr_state
     *
     * @return self
     */
    public function setBillAddrState($bill_addr_state)
    {
        $this->container['bill_addr_state'] = $bill_addr_state;

        return $this;
    }

    /**
     * Gets email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->container['email'];
    }

    /**
     * Sets email
     *
     * @param string|null $email email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->container['email'] = $email;

        return $this;
    }

    /**
     * Gets home_phone
     *
     * @return \Datatrans\Client\Model\CardholderPhoneNumber|null
     */
    public function getHomePhone()
    {
        return $this->container['home_phone'];
    }

    /**
     * Sets home_phone
     *
     * @param \Datatrans\Client\Model\CardholderPhoneNumber|null $home_phone home_phone
     *
     * @return self
     */
    public function setHomePhone($home_phone)
    {
        $this->container['home_phone'] = $home_phone;

        return $this;
    }

    /**
     * Gets mobile_phone
     *
     * @return \Datatrans\Client\Model\CardholderPhoneNumber|null
     */
    public function getMobilePhone()
    {
        return $this->container['mobile_phone'];
    }

    /**
     * Sets mobile_phone
     *
     * @param \Datatrans\Client\Model\CardholderPhoneNumber|null $mobile_phone mobile_phone
     *
     * @return self
     */
    public function setMobilePhone($mobile_phone)
    {
        $this->container['mobile_phone'] = $mobile_phone;

        return $this;
    }

    /**
     * Gets work_phone
     *
     * @return \Datatrans\Client\Model\CardholderPhoneNumber|null
     */
    public function getWorkPhone()
    {
        return $this->container['work_phone'];
    }

    /**
     * Sets work_phone
     *
     * @param \Datatrans\Client\Model\CardholderPhoneNumber|null $work_phone work_phone
     *
     * @return self
     */
    public function setWorkPhone($work_phone)
    {
        $this->container['work_phone'] = $work_phone;

        return $this;
    }

    /**
     * Gets cardholder_name
     *
     * @return string|null
     */
    public function getCardholderName()
    {
        return $this->container['cardholder_name'];
    }

    /**
     * Sets cardholder_name
     *
     * @param string|null $cardholder_name cardholder_name
     *
     * @return self
     */
    public function setCardholderName($cardholder_name)
    {
        $this->container['cardholder_name'] = $cardholder_name;

        return $this;
    }

    /**
     * Gets ship_addr_city
     *
     * @return string|null
     */
    public function getShipAddrCity()
    {
        return $this->container['ship_addr_city'];
    }

    /**
     * Sets ship_addr_city
     *
     * @param string|null $ship_addr_city ship_addr_city
     *
     * @return self
     */
    public function setShipAddrCity($ship_addr_city)
    {
        $this->container['ship_addr_city'] = $ship_addr_city;

        return $this;
    }

    /**
     * Gets ship_addr_country
     *
     * @return string|null
     */
    public function getShipAddrCountry()
    {
        return $this->container['ship_addr_country'];
    }

    /**
     * Sets ship_addr_country
     *
     * @param string|null $ship_addr_country ship_addr_country
     *
     * @return self
     */
    public function setShipAddrCountry($ship_addr_country)
    {
        $this->container['ship_addr_country'] = $ship_addr_country;

        return $this;
    }

    /**
     * Gets ship_addr_line1
     *
     * @return string|null
     */
    public function getShipAddrLine1()
    {
        return $this->container['ship_addr_line1'];
    }

    /**
     * Sets ship_addr_line1
     *
     * @param string|null $ship_addr_line1 ship_addr_line1
     *
     * @return self
     */
    public function setShipAddrLine1($ship_addr_line1)
    {
        $this->container['ship_addr_line1'] = $ship_addr_line1;

        return $this;
    }

    /**
     * Gets ship_addr_line2
     *
     * @return string|null
     */
    public function getShipAddrLine2()
    {
        return $this->container['ship_addr_line2'];
    }

    /**
     * Sets ship_addr_line2
     *
     * @param string|null $ship_addr_line2 ship_addr_line2
     *
     * @return self
     */
    public function setShipAddrLine2($ship_addr_line2)
    {
        $this->container['ship_addr_line2'] = $ship_addr_line2;

        return $this;
    }

    /**
     * Gets ship_addr_line3
     *
     * @return string|null
     */
    public function getShipAddrLine3()
    {
        return $this->container['ship_addr_line3'];
    }

    /**
     * Sets ship_addr_line3
     *
     * @param string|null $ship_addr_line3 ship_addr_line3
     *
     * @return self
     */
    public function setShipAddrLine3($ship_addr_line3)
    {
        $this->container['ship_addr_line3'] = $ship_addr_line3;

        return $this;
    }

    /**
     * Gets ship_addr_post_code
     *
     * @return string|null
     */
    public function getShipAddrPostCode()
    {
        return $this->container['ship_addr_post_code'];
    }

    /**
     * Sets ship_addr_post_code
     *
     * @param string|null $ship_addr_post_code ship_addr_post_code
     *
     * @return self
     */
    public function setShipAddrPostCode($ship_addr_post_code)
    {
        $this->container['ship_addr_post_code'] = $ship_addr_post_code;

        return $this;
    }

    /**
     * Gets ship_addr_state
     *
     * @return string|null
     */
    public function getShipAddrState()
    {
        return $this->container['ship_addr_state'];
    }

    /**
     * Sets ship_addr_state
     *
     * @param string|null $ship_addr_state ship_addr_state
     *
     * @return self
     */
    public function setShipAddrState($ship_addr_state)
    {
        $this->container['ship_addr_state'] = $ship_addr_state;

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


