<?php
/**
 * ThemeConfiguration
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
 * ThemeConfiguration Class Doc Comment
 *
 * @category Class
 * @description Theme configuration options when using the default &#x60;DT2015&#x60; theme
 * @package  Datatrans\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<TKey, TValue>
 * @template TKey int|null
 * @template TValue mixed|null  
 */
class ThemeConfiguration implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'themeConfiguration';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'brand_color' => 'string',
        'text_color' => 'string',
        'logo_type' => 'string',
        'logo_border_color' => 'string',
        'brand_button' => 'string',
        'pay_button_text_color' => 'string',
        'logo_src' => 'string',
        'initial_view' => 'string',
        'brand_title' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'brand_color' => null,
        'text_color' => null,
        'logo_type' => null,
        'logo_border_color' => null,
        'brand_button' => null,
        'pay_button_text_color' => null,
        'logo_src' => null,
        'initial_view' => null,
        'brand_title' => null
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
        'brand_color' => 'brandColor',
        'text_color' => 'textColor',
        'logo_type' => 'logoType',
        'logo_border_color' => 'logoBorderColor',
        'brand_button' => 'brandButton',
        'pay_button_text_color' => 'payButtonTextColor',
        'logo_src' => 'logoSrc',
        'initial_view' => 'initialView',
        'brand_title' => 'brandTitle'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'brand_color' => 'setBrandColor',
        'text_color' => 'setTextColor',
        'logo_type' => 'setLogoType',
        'logo_border_color' => 'setLogoBorderColor',
        'brand_button' => 'setBrandButton',
        'pay_button_text_color' => 'setPayButtonTextColor',
        'logo_src' => 'setLogoSrc',
        'initial_view' => 'setInitialView',
        'brand_title' => 'setBrandTitle'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'brand_color' => 'getBrandColor',
        'text_color' => 'getTextColor',
        'logo_type' => 'getLogoType',
        'logo_border_color' => 'getLogoBorderColor',
        'brand_button' => 'getBrandButton',
        'pay_button_text_color' => 'getPayButtonTextColor',
        'logo_src' => 'getLogoSrc',
        'initial_view' => 'getInitialView',
        'brand_title' => 'getBrandTitle'
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

    const TEXT_COLOR_WHITE = 'white';
    const TEXT_COLOR_BLACK = 'black';
    const LOGO_TYPE_CIRCLE = 'circle';
    const LOGO_TYPE_RECTANGLE = 'rectangle';
    const LOGO_TYPE_NONE = 'none';
    const INITIAL_VIEW__LIST = 'list';
    const INITIAL_VIEW_GRID = 'grid';
    

    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getTextColorAllowableValues()
    {
        return [
            self::TEXT_COLOR_WHITE,
            self::TEXT_COLOR_BLACK,
        ];
    }
    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getLogoTypeAllowableValues()
    {
        return [
            self::LOGO_TYPE_CIRCLE,
            self::LOGO_TYPE_RECTANGLE,
            self::LOGO_TYPE_NONE,
        ];
    }
    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getInitialViewAllowableValues()
    {
        return [
            self::INITIAL_VIEW__LIST,
            self::INITIAL_VIEW_GRID,
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
        $this->container['brand_color'] = $data['brand_color'] ?? null;
        $this->container['text_color'] = $data['text_color'] ?? null;
        $this->container['logo_type'] = $data['logo_type'] ?? null;
        $this->container['logo_border_color'] = $data['logo_border_color'] ?? null;
        $this->container['brand_button'] = $data['brand_button'] ?? null;
        $this->container['pay_button_text_color'] = $data['pay_button_text_color'] ?? null;
        $this->container['logo_src'] = $data['logo_src'] ?? null;
        $this->container['initial_view'] = $data['initial_view'] ?? null;
        $this->container['brand_title'] = $data['brand_title'] ?? null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        $allowedValues = $this->getTextColorAllowableValues();
        if (!is_null($this->container['text_color']) && !in_array($this->container['text_color'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'text_color', must be one of '%s'",
                $this->container['text_color'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getLogoTypeAllowableValues();
        if (!is_null($this->container['logo_type']) && !in_array($this->container['logo_type'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'logo_type', must be one of '%s'",
                $this->container['logo_type'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getInitialViewAllowableValues();
        if (!is_null($this->container['initial_view']) && !in_array($this->container['initial_view'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'initial_view', must be one of '%s'",
                $this->container['initial_view'],
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
     * Gets brand_color
     *
     * @return string|null
     */
    public function getBrandColor()
    {
        return $this->container['brand_color'];
    }

    /**
     * Sets brand_color
     *
     * @param string|null $brand_color Hex notation of a color
     *
     * @return self
     */
    public function setBrandColor($brand_color)
    {
        $this->container['brand_color'] = $brand_color;

        return $this;
    }

    /**
     * Gets text_color
     *
     * @return string|null
     */
    public function getTextColor()
    {
        return $this->container['text_color'];
    }

    /**
     * Sets text_color
     *
     * @param string|null $text_color The color of the text in the header bar if no logo is given
     *
     * @return self
     */
    public function setTextColor($text_color)
    {
        $allowedValues = $this->getTextColorAllowableValues();
        if (!is_null($text_color) && !in_array($text_color, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'text_color', must be one of '%s'",
                    $text_color,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['text_color'] = $text_color;

        return $this;
    }

    /**
     * Gets logo_type
     *
     * @return string|null
     */
    public function getLogoType()
    {
        return $this->container['logo_type'];
    }

    /**
     * Sets logo_type
     *
     * @param string|null $logo_type The header logo's display style
     *
     * @return self
     */
    public function setLogoType($logo_type)
    {
        $allowedValues = $this->getLogoTypeAllowableValues();
        if (!is_null($logo_type) && !in_array($logo_type, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'logo_type', must be one of '%s'",
                    $logo_type,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['logo_type'] = $logo_type;

        return $this;
    }

    /**
     * Gets logo_border_color
     *
     * @return string|null
     */
    public function getLogoBorderColor()
    {
        return $this->container['logo_border_color'];
    }

    /**
     * Sets logo_border_color
     *
     * @param string|null $logo_border_color Decides whether the logo shall be styled with a border around it, if the value is true the default background color is chosen, else the provided string is used as color value
     *
     * @return self
     */
    public function setLogoBorderColor($logo_border_color)
    {
        $this->container['logo_border_color'] = $logo_border_color;

        return $this;
    }

    /**
     * Gets brand_button
     *
     * @return string|null
     */
    public function getBrandButton()
    {
        return $this->container['brand_button'];
    }

    /**
     * Sets brand_button
     *
     * @param string|null $brand_button Decides if the pay button should have the same color as the brandColor. If set to false the hex color #01669F will be used as a default
     *
     * @return self
     */
    public function setBrandButton($brand_button)
    {
        $this->container['brand_button'] = $brand_button;

        return $this;
    }

    /**
     * Gets pay_button_text_color
     *
     * @return string|null
     */
    public function getPayButtonTextColor()
    {
        return $this->container['pay_button_text_color'];
    }

    /**
     * Sets pay_button_text_color
     *
     * @param string|null $pay_button_text_color The color (hex) of the pay button
     *
     * @return self
     */
    public function setPayButtonTextColor($pay_button_text_color)
    {
        $this->container['pay_button_text_color'] = $pay_button_text_color;

        return $this;
    }

    /**
     * Gets logo_src
     *
     * @return string|null
     */
    public function getLogoSrc()
    {
        return $this->container['logo_src'];
    }

    /**
     * Sets logo_src
     *
     * @param string|null $logo_src An SVG image provided by the merchant. The image needs to be uploaded by using the Datatrans Web Administration Tool
     *
     * @return self
     */
    public function setLogoSrc($logo_src)
    {
        $this->container['logo_src'] = $logo_src;

        return $this;
    }

    /**
     * Gets initial_view
     *
     * @return string|null
     */
    public function getInitialView()
    {
        return $this->container['initial_view'];
    }

    /**
     * Sets initial_view
     *
     * @param string|null $initial_view Wheter the payment page shows the payment method selection as list (default) or as a grid
     *
     * @return self
     */
    public function setInitialView($initial_view)
    {
        $allowedValues = $this->getInitialViewAllowableValues();
        if (!is_null($initial_view) && !in_array($initial_view, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'initial_view', must be one of '%s'",
                    $initial_view,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['initial_view'] = $initial_view;

        return $this;
    }

    /**
     * Gets brand_title
     *
     * @return bool|null
     */
    public function getBrandTitle()
    {
        return $this->container['brand_title'];
    }

    /**
     * Sets brand_title
     *
     * @param bool|null $brand_title If set to `false` and no logo is used (see `logoSrc`), the payment page header will be empty
     *
     * @return self
     */
    public function setBrandTitle($brand_title)
    {
        $this->container['brand_title'] = $brand_title;

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


