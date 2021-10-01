<?php
/**
 * ByjunoAuthorizeRequest
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
 * ByjunoAuthorizeRequest Class Doc Comment
 *
 * @category Class
 * @package  Datatrans\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<TKey, TValue>
 * @template TKey int|null
 * @template TValue mixed|null  
 */
class ByjunoAuthorizeRequest implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'ByjunoAuthorizeRequest';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'subtype' => 'string',
        'alias' => 'string',
        'customer_email_confirmed' => 'bool',
        'customer_info1' => 'string',
        'customer_info2' => 'string',
        'delivery_method' => 'string',
        'device_fingerprint_id' => 'string',
        'paper_invoice' => 'bool',
        'repayment_type' => 'int',
        'risk_owner' => 'string',
        'site_id' => 'string',
        'verified_document1_type' => 'string',
        'verified_document1_number' => 'string',
        'verified_document1_issuer' => 'string',
        'custom_data' => 'string[]',
        'first_rate_amount' => 'int'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'subtype' => null,
        'alias' => null,
        'customer_email_confirmed' => null,
        'customer_info1' => null,
        'customer_info2' => null,
        'delivery_method' => null,
        'device_fingerprint_id' => null,
        'paper_invoice' => null,
        'repayment_type' => 'int32',
        'risk_owner' => null,
        'site_id' => null,
        'verified_document1_type' => null,
        'verified_document1_number' => null,
        'verified_document1_issuer' => null,
        'custom_data' => null,
        'first_rate_amount' => 'int32'
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
        'subtype' => 'subtype',
        'alias' => 'alias',
        'customer_email_confirmed' => 'customerEmailConfirmed',
        'customer_info1' => 'customerInfo1',
        'customer_info2' => 'customerInfo2',
        'delivery_method' => 'deliveryMethod',
        'device_fingerprint_id' => 'deviceFingerprintId',
        'paper_invoice' => 'paperInvoice',
        'repayment_type' => 'repaymentType',
        'risk_owner' => 'riskOwner',
        'site_id' => 'siteId',
        'verified_document1_type' => 'verifiedDocument1Type',
        'verified_document1_number' => 'verifiedDocument1Number',
        'verified_document1_issuer' => 'verifiedDocument1Issuer',
        'custom_data' => 'customData',
        'first_rate_amount' => 'firstRateAmount'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'subtype' => 'setSubtype',
        'alias' => 'setAlias',
        'customer_email_confirmed' => 'setCustomerEmailConfirmed',
        'customer_info1' => 'setCustomerInfo1',
        'customer_info2' => 'setCustomerInfo2',
        'delivery_method' => 'setDeliveryMethod',
        'device_fingerprint_id' => 'setDeviceFingerprintId',
        'paper_invoice' => 'setPaperInvoice',
        'repayment_type' => 'setRepaymentType',
        'risk_owner' => 'setRiskOwner',
        'site_id' => 'setSiteId',
        'verified_document1_type' => 'setVerifiedDocument1Type',
        'verified_document1_number' => 'setVerifiedDocument1Number',
        'verified_document1_issuer' => 'setVerifiedDocument1Issuer',
        'custom_data' => 'setCustomData',
        'first_rate_amount' => 'setFirstRateAmount'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'subtype' => 'getSubtype',
        'alias' => 'getAlias',
        'customer_email_confirmed' => 'getCustomerEmailConfirmed',
        'customer_info1' => 'getCustomerInfo1',
        'customer_info2' => 'getCustomerInfo2',
        'delivery_method' => 'getDeliveryMethod',
        'device_fingerprint_id' => 'getDeviceFingerprintId',
        'paper_invoice' => 'getPaperInvoice',
        'repayment_type' => 'getRepaymentType',
        'risk_owner' => 'getRiskOwner',
        'site_id' => 'getSiteId',
        'verified_document1_type' => 'getVerifiedDocument1Type',
        'verified_document1_number' => 'getVerifiedDocument1Number',
        'verified_document1_issuer' => 'getVerifiedDocument1Issuer',
        'custom_data' => 'getCustomData',
        'first_rate_amount' => 'getFirstRateAmount'
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

    const SUBTYPE_INVOICE_INSTALLMENT_BYJUNO_INVOICE_MONTHLYINVOICE = 'INVOICE, INSTALLMENT, BYJUNO-INVOICE, MONTHLYINVOICE';
    const RISK_OWNER_IJ = 'IJ';
    const RISK_OWNER_CLIENT = 'CLIENT';
    const VERIFIED_DOCUMENT1_TYPE_SWISS_TRAVEL_PASS = 'swiss-travel-pass';
    const VERIFIED_DOCUMENT1_TYPE_OTHER = 'other';
    

    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getSubtypeAllowableValues()
    {
        return [
            self::SUBTYPE_INVOICE_INSTALLMENT_BYJUNO_INVOICE_MONTHLYINVOICE,
        ];
    }
    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getRiskOwnerAllowableValues()
    {
        return [
            self::RISK_OWNER_IJ,
            self::RISK_OWNER_CLIENT,
        ];
    }
    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getVerifiedDocument1TypeAllowableValues()
    {
        return [
            self::VERIFIED_DOCUMENT1_TYPE_SWISS_TRAVEL_PASS,
            self::VERIFIED_DOCUMENT1_TYPE_OTHER,
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
        $this->container['subtype'] = $data['subtype'] ?? null;
        $this->container['alias'] = $data['alias'] ?? null;
        $this->container['customer_email_confirmed'] = $data['customer_email_confirmed'] ?? false;
        $this->container['customer_info1'] = $data['customer_info1'] ?? null;
        $this->container['customer_info2'] = $data['customer_info2'] ?? null;
        $this->container['delivery_method'] = $data['delivery_method'] ?? null;
        $this->container['device_fingerprint_id'] = $data['device_fingerprint_id'] ?? null;
        $this->container['paper_invoice'] = $data['paper_invoice'] ?? null;
        $this->container['repayment_type'] = $data['repayment_type'] ?? null;
        $this->container['risk_owner'] = $data['risk_owner'] ?? null;
        $this->container['site_id'] = $data['site_id'] ?? null;
        $this->container['verified_document1_type'] = $data['verified_document1_type'] ?? null;
        $this->container['verified_document1_number'] = $data['verified_document1_number'] ?? null;
        $this->container['verified_document1_issuer'] = $data['verified_document1_issuer'] ?? null;
        $this->container['custom_data'] = $data['custom_data'] ?? null;
        $this->container['first_rate_amount'] = $data['first_rate_amount'] ?? null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['subtype'] === null) {
            $invalidProperties[] = "'subtype' can't be null";
        }
        $allowedValues = $this->getSubtypeAllowableValues();
        if (!is_null($this->container['subtype']) && !in_array($this->container['subtype'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'subtype', must be one of '%s'",
                $this->container['subtype'],
                implode("', '", $allowedValues)
            );
        }

        if (!is_null($this->container['repayment_type']) && ($this->container['repayment_type'] > 20)) {
            $invalidProperties[] = "invalid value for 'repayment_type', must be smaller than or equal to 20.";
        }

        if (!is_null($this->container['repayment_type']) && ($this->container['repayment_type'] < 0)) {
            $invalidProperties[] = "invalid value for 'repayment_type', must be bigger than or equal to 0.";
        }

        $allowedValues = $this->getRiskOwnerAllowableValues();
        if (!is_null($this->container['risk_owner']) && !in_array($this->container['risk_owner'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'risk_owner', must be one of '%s'",
                $this->container['risk_owner'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getVerifiedDocument1TypeAllowableValues();
        if (!is_null($this->container['verified_document1_type']) && !in_array($this->container['verified_document1_type'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'verified_document1_type', must be one of '%s'",
                $this->container['verified_document1_type'],
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
     * Gets subtype
     *
     * @return string
     */
    public function getSubtype()
    {
        return $this->container['subtype'];
    }

    /**
     * Sets subtype
     *
     * @param string $subtype The Byjuno specific payment method used for the transaction.
     *
     * @return self
     */
    public function setSubtype($subtype)
    {
        $allowedValues = $this->getSubtypeAllowableValues();
        if (!in_array($subtype, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'subtype', must be one of '%s'",
                    $subtype,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['subtype'] = $subtype;

        return $this;
    }

    /**
     * Gets alias
     *
     * @return string|null
     */
    public function getAlias()
    {
        return $this->container['alias'];
    }

    /**
     * Sets alias
     *
     * @param string|null $alias Alias received for example from a previous transaction if `option.createAlias: true` was used. In order to retrieve the alias from a previous transaction, use the [Status API](#operation/status).
     *
     * @return self
     */
    public function setAlias($alias)
    {
        $this->container['alias'] = $alias;

        return $this;
    }

    /**
     * Gets customer_email_confirmed
     *
     * @return bool|null
     */
    public function getCustomerEmailConfirmed()
    {
        return $this->container['customer_email_confirmed'];
    }

    /**
     * Sets customer_email_confirmed
     *
     * @param bool|null $customer_email_confirmed Indicates that the customer has confirmed the email address to the merchant
     *
     * @return self
     */
    public function setCustomerEmailConfirmed($customer_email_confirmed)
    {
        $this->container['customer_email_confirmed'] = $customer_email_confirmed;

        return $this;
    }

    /**
     * Gets customer_info1
     *
     * @return string|null
     */
    public function getCustomerInfo1()
    {
        return $this->container['customer_info1'];
    }

    /**
     * Sets customer_info1
     *
     * @param string|null $customer_info1 Customer information for credit check.
     *
     * @return self
     */
    public function setCustomerInfo1($customer_info1)
    {
        $this->container['customer_info1'] = $customer_info1;

        return $this;
    }

    /**
     * Gets customer_info2
     *
     * @return string|null
     */
    public function getCustomerInfo2()
    {
        return $this->container['customer_info2'];
    }

    /**
     * Sets customer_info2
     *
     * @param string|null $customer_info2 Customer information for credit check.
     *
     * @return self
     */
    public function setCustomerInfo2($customer_info2)
    {
        $this->container['customer_info2'] = $customer_info2;

        return $this;
    }

    /**
     * Gets delivery_method
     *
     * @return string|null
     */
    public function getDeliveryMethod()
    {
        return $this->container['delivery_method'];
    }

    /**
     * Sets delivery_method
     *
     * @param string|null $delivery_method Can be one of POST (Delivery by Swiss Post), SHOP (Point of Sale) or HLD (Home Delivery Service)
     *
     * @return self
     */
    public function setDeliveryMethod($delivery_method)
    {
        $this->container['delivery_method'] = $delivery_method;

        return $this;
    }

    /**
     * Gets device_fingerprint_id
     *
     * @return string|null
     */
    public function getDeviceFingerprintId()
    {
        return $this->container['device_fingerprint_id'];
    }

    /**
     * Sets device_fingerprint_id
     *
     * @param string|null $device_fingerprint_id Identification of the customer in the shop
     *
     * @return self
     */
    public function setDeviceFingerprintId($device_fingerprint_id)
    {
        $this->container['device_fingerprint_id'] = $device_fingerprint_id;

        return $this;
    }

    /**
     * Gets paper_invoice
     *
     * @return bool|null
     */
    public function getPaperInvoice()
    {
        return $this->container['paper_invoice'];
    }

    /**
     * Sets paper_invoice
     *
     * @param bool|null $paper_invoice Whether or not to send a paper invoice.
     *
     * @return self
     */
    public function setPaperInvoice($paper_invoice)
    {
        $this->container['paper_invoice'] = $paper_invoice;

        return $this;
    }

    /**
     * Gets repayment_type
     *
     * @return int|null
     */
    public function getRepaymentType()
    {
        return $this->container['repayment_type'];
    }

    /**
     * Sets repayment_type
     *
     * @param int|null $repayment_type Number from 1 to 20 to indicate the repayment schedule. This is used in combination with payment methods and defined per client configuration.
     *
     * @return self
     */
    public function setRepaymentType($repayment_type)
    {

        if (!is_null($repayment_type) && ($repayment_type > 20)) {
            throw new \InvalidArgumentException('invalid value for $repayment_type when calling ByjunoAuthorizeRequest., must be smaller than or equal to 20.');
        }
        if (!is_null($repayment_type) && ($repayment_type < 0)) {
            throw new \InvalidArgumentException('invalid value for $repayment_type when calling ByjunoAuthorizeRequest., must be bigger than or equal to 0.');
        }

        $this->container['repayment_type'] = $repayment_type;

        return $this;
    }

    /**
     * Gets risk_owner
     *
     * @return string|null
     */
    public function getRiskOwner()
    {
        return $this->container['risk_owner'];
    }

    /**
     * Sets risk_owner
     *
     * @param string|null $risk_owner Defines which party should take the risk.
     *
     * @return self
     */
    public function setRiskOwner($risk_owner)
    {
        $allowedValues = $this->getRiskOwnerAllowableValues();
        if (!is_null($risk_owner) && !in_array($risk_owner, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'risk_owner', must be one of '%s'",
                    $risk_owner,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['risk_owner'] = $risk_owner;

        return $this;
    }

    /**
     * Gets site_id
     *
     * @return string|null
     */
    public function getSiteId()
    {
        return $this->container['site_id'];
    }

    /**
     * Sets site_id
     *
     * @param string|null $site_id Can be used in case when client operates different legally separated stores / points of sale.
     *
     * @return self
     */
    public function setSiteId($site_id)
    {
        $this->container['site_id'] = $site_id;

        return $this;
    }

    /**
     * Gets verified_document1_type
     *
     * @return string|null
     */
    public function getVerifiedDocument1Type()
    {
        return $this->container['verified_document1_type'];
    }

    /**
     * Sets verified_document1_type
     *
     * @param string|null $verified_document1_type Indication if merchant is having verified documents from client request.
     *
     * @return self
     */
    public function setVerifiedDocument1Type($verified_document1_type)
    {
        $allowedValues = $this->getVerifiedDocument1TypeAllowableValues();
        if (!is_null($verified_document1_type) && !in_array($verified_document1_type, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'verified_document1_type', must be one of '%s'",
                    $verified_document1_type,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['verified_document1_type'] = $verified_document1_type;

        return $this;
    }

    /**
     * Gets verified_document1_number
     *
     * @return string|null
     */
    public function getVerifiedDocument1Number()
    {
        return $this->container['verified_document1_number'];
    }

    /**
     * Sets verified_document1_number
     *
     * @param string|null $verified_document1_number Verified document number.
     *
     * @return self
     */
    public function setVerifiedDocument1Number($verified_document1_number)
    {
        $this->container['verified_document1_number'] = $verified_document1_number;

        return $this;
    }

    /**
     * Gets verified_document1_issuer
     *
     * @return string|null
     */
    public function getVerifiedDocument1Issuer()
    {
        return $this->container['verified_document1_issuer'];
    }

    /**
     * Sets verified_document1_issuer
     *
     * @param string|null $verified_document1_issuer Verified document issuer.
     *
     * @return self
     */
    public function setVerifiedDocument1Issuer($verified_document1_issuer)
    {
        $this->container['verified_document1_issuer'] = $verified_document1_issuer;

        return $this;
    }

    /**
     * Gets custom_data
     *
     * @return string[]|null
     */
    public function getCustomData()
    {
        return $this->container['custom_data'];
    }

    /**
     * Sets custom_data
     *
     * @param string[]|null $custom_data A list of custom data fields. It can accept up to 10 entries.
     *
     * @return self
     */
    public function setCustomData($custom_data)
    {
        $this->container['custom_data'] = $custom_data;

        return $this;
    }

    /**
     * Gets first_rate_amount
     *
     * @return int|null
     */
    public function getFirstRateAmount()
    {
        return $this->container['first_rate_amount'];
    }

    /**
     * Sets first_rate_amount
     *
     * @param int|null $first_rate_amount Amount of the first rate paid by the customer.
     *
     * @return self
     */
    public function setFirstRateAmount($first_rate_amount)
    {
        $this->container['first_rate_amount'] = $first_rate_amount;

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


