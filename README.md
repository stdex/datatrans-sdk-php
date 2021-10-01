# OpenAPIClient-php

Welcome to the Datatrans API reference.
This document is meant to be used in combination with https://docs.datatrans.ch.
All the parameters used in the curl and web samples are described here.
Reach out to support@datatrans.ch if something is missing or unclear.

Last updated: 09.09.21 - 07:44 UTC

# Payment Process
The following steps describe how transactions are processed with Datatrans.
We separate payments in three categories: Customer-initiated payments, merchant-initiated payments and after the payment.

## Customer Initiated Payments
We have three integrations available: [Redirect](https://docs.datatrans.ch/docs/redirect-lightbox),
[Lightbox](https://docs.datatrans.ch/docs/redirect-lightbox) and [Secure Fields](https://docs.datatrans.ch/docs/secure-fields).

### Redirect & Lightbox
- Send the required parameters to initialize a `transactionId` to the [init](#operation/init) endpoint.
- Let the customer proceed with the payment by redirecting them to the correct link - or showing them your payment form.
  - Redirect: Redirect the browser to the following URL structure
    ```
    https://pay.sandbox.datatrans.com/v1/start/transactionId
    ```
  - Lightbox: Load the JavaScript library and initialize the payment form:
    ```js
    <script src=\"https://pay.sandbox.datatrans.com/upp/payment/js/datatrans-2.0.0.js\">
    ```
    ```js
    payButton.onclick = function() {
      Datatrans.startPayment({
        transactionId:  \"transactionId\"
      });
    };
    ```
- Your customer proceeds with entering their payment information and finally hits the pay or continue button.
- For card payments, we check the payment information with your acquirers. The acquirers check the payment information with the issuing parties.
The customer proceeds with 3D Secure whenever required.
- Once the transaction is completed, we return all relevant information
to you (check our [Webhook section](#section/Webhook) for more details), and the status of the transaction.
The browser will be redirected to the success, cancel or error page with our `transactionId` in the response.

### Secure Fields
- Send the required parameters to initialize a transactionId to our [secureFieldsInit](#operation/secureFieldsInit) endpoint.
- Load the Secure Fields JavaScript libarary and initialize Secure Fields:
  ```js
  <script src=\"https://pay.sandbox.datatrans.com/upp/payment/js/secure-fields-2.0.0.js\">
  ```
  ```js
  var secureFields = new SecureFields();
  secureFields.init(
    {{transactionId}}, {
        cardNumber: \"cardNumberPlaceholder\",
        cvv: \"cvvPlaceholder\",
    });
  ```
- Handle the success event of the secureFields.submit() call.
- If 3D authentication is required for a specific transaction, the `redirect` property inside the `data`
object will indicate the URL that the customer needs to be redirected to.
- Use the [Authorize an authenticated transaction](#operation/authorize-split)endpoint to fully authorize
the Secure Fields transaction. This is required to finalize the authorization process with Secure Fields.

## Merchant Initiated Payments
Once you have processed a customer-initiated payment or registration you can call our API to process
recurring payments. Check our [authorize](#operation/authorize) endpoint to see how to create a recurring
payment or our [validate](#operation/validate) endpoint to validate your customers’ saved payment details.

## After the payment
Use the `transactionId` to check the [status](#operation/status) and to [settle](#operation/settle),
[cancel](#operation/cancel) or [refund](#operation/credit) a transaction.

# Idempotency

To retry identical requests with the same effect without accidentally performing the same operation more than needed,
you can add the header `Idempotency-Key` to your requests. This is useful when API calls are disrupted or you did not
receive a response. In other words, retrying identical requests with our idempotency key will not have any side effects.
We will return the same response for any identical request that includes the same idempotency key.

If your request failed to reach our servers, no idempotent result is saved because no API endpoint processed your request.
In such cases, you can simply retry your operation safely. Idempotency keys remain stored for 60 minutes. After 60 minutes
have passed, sending the same request together with the previous idempotency key will create a new operation.

Please note that the idempotency key has to be unique for each request and has to be defined by yourself. We recommend
assigning a random value as your idempotency key and using UUID v4. Idempotency is only available for `POST` requests.

Idempotency was implemented according to the [\"The Idempotency HTTP Header Field\" Internet-Draft](https://tools.ietf.org/id/draft-idempotency-header-01.html)

|Scenario|Condition|Expectation|
|:---|:---|:---|
|First time request|Idempotency key has not been seen during the past 60 minutes.|The request is processed normally.|
|Repeated request|The request was retried after the first time request completed.| The response from the first time request will be returned.|
|Repeated request|The request was retried before the first time request completed.| 409 Conflict. It is recommended that clients time their retries using an exponential backoff algorithm.|
|Repeated request|The request body is different than the one from the first time request.| 422 Unprocessable Entity.|

Example:
```sh
curl -i 'https://api.sandbox.datatrans.com/v1/transactions' \\
    -H 'Authorization: Basic MTEwMDAwNzI4MzpobDJST1NScUN2am5EVlJL' \\
    -H 'Content-Type: application/json; charset=UTF-8' \\
    -H 'Idempotency-Key: e75d621b-0e56-4b71-b889-1acec3e9d870' \\
    -d '{
    \"refno\" : \"58b389331dad\",
    \"amount\" : 1000,
    \"currency\" : \"CHF\",
    \"paymentMethods\" : [ \"VIS\", \"ECA\", \"PAP\" ],
    \"option\" : {
       \"createAlias\" : true
    }
}'
```

# Authentication
Authentication to the APIs is performed with HTTP basic authentication. Your
`merchantId` acts as the username. To get the password, login
to the <a href='https://admin.sandbox.datatrans.com/' target='_blank'>dashboard</a>
and navigate to the security settings under `UPP Administration > Security`.

Create a base64 encoded value consisting of merchantId and password (most HTTP clients
are able to handle the base64 encoding automatically) and submit the Authorization header with your requests. Here’s an example:

```
base64(merchantId:password) = MTAwMDAxMTAxMTpYMWVXNmkjJA==
```

```
Authorization: Basic MTAwMDAxMTAxMTpYMWVXNmkjJA==
````

All API requests must be done over HTTPS with TLS >= 1.2.


<!-- ReDoc-Inject: <security-definitions> -->

# Errors
Datatrans uses HTTP response codes to indicate if an API call was successful or resulted in a failure.
HTTP `2xx` status codes indicate a successful API call whereas HTTP `4xx` status codes
indicate client errors or if something with the transaction went wrong - for example a decline.
In rare cases HTTP `5xx` status codes are returned. Those indicate errors on Datatrans side.

Here’s the payload of a sample HTTP `400` error, showing that your request has wrong values in it
```
{
  \"error\" : {
    \"code\" : \"INVALID_PROPERTY\",
    \"message\" : \"init.initRequest.currency The given currency does not have the right format\"
  }
}
```

# Webhook
After each authorization Datatrans tries to call the configured Webhook (POST) URL. The Webhook URL
can be configured within the <a href='https://admin.sandbox.datatrans.com/' target='_blank'>dashboard</a>.
The Webhook payload contains the same information as the response of a [Status API](#operation/status) call.

## Webhook signing
If you want your webhook requests to be signed, setup a HMAC key in your merchant configuration.
To get your HMAC key, login to our dashboard and navigate to the Security settings in your
merchant configuration to view your server to server security settings.
Select the radio button `Important parameters will be digitally signed (HMAC-SHA256) and sent with payment messages`.
Datatrans will use this key to sign the webhook payload and will add a `Datatrans-Signature` HTTP request header:

```sh
Datatrans-Signature: t=1559303131511,s0=33819a1220fd8e38fc5bad3f57ef31095fac0deb38c001ba347e694f48ffe2fc
```

On your server, calculate the signature of the webhook payload and finally compare it to `s0`.
`timestamp` is the `t` value from the Datatrans-Signature header, `payload` represents all UTF-8 bytes
from the body of the payload and finally `key` is the HMAC key you configured within the dashboard.
If the value of `sign` is equal to `s0` from the `Datatrans-Signature` header,
the webhook payload is valid and was not tampered.

**Java**

```java
// hex bytes of the key
byte[] key = Hex.decodeHex(key);

// Create sign with timestamp and payload
String algorithm = \"HmacSha256\";
SecretKeySpec macKey = new SecretKeySpec(key, algorithm);
Mac mac = Mac.getInstance(algorithm);
mac.init(macKey);
mac.update(String.valueOf(timestamp).getBytes());
byte[] result = mac.doFinal(payload.getBytes());
String sign = Hex.encodeHexString(result);
```

**Python**

```python
# hex bytes of the key
key_hex_bytes = bytes.fromhex(key)

# Create sign with timestamp and payload
sign = hmac.new(key_hex_bytes, bytes(str(timestamp) + payload, 'utf-8'), hashlib.sha256)
```

# Release notes
<details>
  <summary>Details</summary>

  ### 2.0.22 - 21.07.2021
* Added full support for Swisscom Pay `ESY`
* The `marketplace` object now accepts an array of splits.

### 2.0.21 - 21.05.2021
* Updated idempotency handling. See the details here https://api-reference.datatrans.ch/#section/Idempotency

### 2.0.20 - 18.05.2021
* In addition to `debit` and `credit` the Status API now also returns `prepaid` in the `card.info.type` property.
* paysafecard - Added support for `merchantClientId` 

### 2.0.19 - 03.05.2021
* Fixed `PAP.orderTransactionId` to be a string
* Added support for `PAP.fraudSessionId` (PayPal FraudNet)

### 2.0.18 - 21.04.2021
* Added new `POST /v1/transactions/screen` API to check a customer's credit score before sending an actual authorization request.
Currently only `INT` (Byjuno) is supported.

### 2.0.17 - 20.04.2021
* Added new `GET /v1/aliases` API to receive more information about a particular alias.

### 2.0.16 - 13.04.2021
* Added support for Migros Bank E-Pay <code>MDP</code>

### 2.0.15 - 24.03.2021
* Byjuno - renamed `subPaymentMethod` to `subtype` (`subPaymentMethod` still works)
* Klarna - Returning the `subtype` (`pay_now`, `pay_later`, `pay_over_time`, `direct_debit`, `direct_bank_transfer`)
from the Status API

### 2.0.14 - 09.03.2021
* Byjuno - Added support for `customData` and `firstRateAmount`
* Returning the `transactionId` (if available) for a failed Refund API call.

### 2.0.13 - 15.02.2021
* The Status and Webhook payloads now include the `language` property
* Fixed a bug where `card.3D.transStatusReason` and `card.3D.cardholderInfo` was not returned

### 2.0.12 - 04.02.2021
* Added support for PayPal transaction context (STC)
* Fixed a bug where the transaction status did not switch to `failed` after it timed out
* Fixed a bug with `option.rememberMe` not returning the Alias from the Status API

### 2.0.11 - 01.02.2021
* Returning `card.3D.transStatusReason` (if available) from the Status API

### 2.0.10 - 18.01.2021
* Returning `card.3D.cardholderInfo` (if available) from the Status API

### 2.0.9 - 21.12.2020
* Added support for Alipay <code>ALP</code>

### 2.0.8 - 21.12.2020
* Added full support for Klarna <code>KLN</code>
* Added support for swissbilling <code>SWB</code>

</details>


For more information, please visit [https://docs.datatrans.ch](https://docs.datatrans.ch).

## Installation & Usage

### Requirements

PHP 7.2 and later.

### Composer

To install the bindings via [Composer](https://getcomposer.org/), add the following to `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/GIT_USER_ID/GIT_REPO_ID.git"
    }
  ],
  "require": {
    "GIT_USER_ID/GIT_REPO_ID": "*@dev"
  }
}
```

Then run `composer install`

### Manual Installation

Download the files and include `autoload.php`:

```php
<?php
require_once('/path/to/OpenAPIClient-php/vendor/autoload.php');
```

## Getting Started

Please follow the [installation procedure](#installation--usage) and then run the following:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1AliasesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$alias_convert_request = {"legacyAlias":"424242SKMPRI4242"}; // \Datatrans\Client\Model\AliasConvertRequest

try {
    $result = $apiInstance->aliasesConvert($alias_convert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1AliasesApi->aliasesConvert: ', $e->getMessage(), PHP_EOL;
}

```

## API Endpoints

All URIs are relative to *https://api.sandbox.datatrans.com*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*V1AliasesApi* | [**aliasesConvert**](docs/Api/V1AliasesApi.md#aliasesconvert) | **POST** /v1/aliases | Convert alias
*V1AliasesApi* | [**aliasesDelete**](docs/Api/V1AliasesApi.md#aliasesdelete) | **DELETE** /v1/aliases/{alias} | Delete alias
*V1AliasesApi* | [**aliasesInfo**](docs/Api/V1AliasesApi.md#aliasesinfo) | **GET** /v1/aliases/{alias} | Get alias info
*V1OpenapiApi* | [**get**](docs/Api/V1OpenapiApi.md#get) | **GET** /v1/openapi | 
*V1ReconciliationsApi* | [**bulkSaleReport**](docs/Api/V1ReconciliationsApi.md#bulksalereport) | **POST** /v1/reconciliations/sales/bulk | Bulk reporting of sales
*V1ReconciliationsApi* | [**saleReport**](docs/Api/V1ReconciliationsApi.md#salereport) | **POST** /v1/reconciliations/sales | Report a sale
*V1TransactionsApi* | [**authorize**](docs/Api/V1TransactionsApi.md#authorize) | **POST** /v1/transactions/authorize | Authorize a transaction
*V1TransactionsApi* | [**authorizeSplit**](docs/Api/V1TransactionsApi.md#authorizesplit) | **POST** /v1/transactions/{transactionId}/authorize | Authorize an authenticated transaction
*V1TransactionsApi* | [**cancel**](docs/Api/V1TransactionsApi.md#cancel) | **POST** /v1/transactions/{transactionId}/cancel | Cancel a transaction
*V1TransactionsApi* | [**credit**](docs/Api/V1TransactionsApi.md#credit) | **POST** /v1/transactions/{transactionId}/credit | Refund a transaction
*V1TransactionsApi* | [**init**](docs/Api/V1TransactionsApi.md#init) | **POST** /v1/transactions | Initialize a transaction
*V1TransactionsApi* | [**screen**](docs/Api/V1TransactionsApi.md#screen) | **POST** /v1/transactions/screen | Screen the customer details
*V1TransactionsApi* | [**secureFieldsInit**](docs/Api/V1TransactionsApi.md#securefieldsinit) | **POST** /v1/transactions/secureFields | Initialize a Secure Fields transaction
*V1TransactionsApi* | [**secureFieldsUpdate**](docs/Api/V1TransactionsApi.md#securefieldsupdate) | **PATCH** /v1/transactions/secureFields/{transactionId} | Update the amount of a Secure Fields transaction
*V1TransactionsApi* | [**settle**](docs/Api/V1TransactionsApi.md#settle) | **POST** /v1/transactions/{transactionId}/settle | Settle a transaction
*V1TransactionsApi* | [**status**](docs/Api/V1TransactionsApi.md#status) | **GET** /v1/transactions/{transactionId} | Checking the status of a transaction
*V1TransactionsApi* | [**validate**](docs/Api/V1TransactionsApi.md#validate) | **POST** /v1/transactions/validate | Validate an existing alias

## Models

- [AccardaAttachment](docs/Model/AccardaAttachment.md)
- [AccardaRequest](docs/Model/AccardaRequest.md)
- [AccommodationMetaData](docs/Model/AccommodationMetaData.md)
- [Action](docs/Model/Action.md)
- [AirlineDataRequest](docs/Model/AirlineDataRequest.md)
- [AirlineMetaData](docs/Model/AirlineMetaData.md)
- [AliasCardInfoDetail](docs/Model/AliasCardInfoDetail.md)
- [AliasConvertRequest](docs/Model/AliasConvertRequest.md)
- [AliasConvertResponse](docs/Model/AliasConvertResponse.md)
- [AliasInfoResponse](docs/Model/AliasInfoResponse.md)
- [AliasesError](docs/Model/AliasesError.md)
- [AliasesErrorCode](docs/Model/AliasesErrorCode.md)
- [AliasesResponseBase](docs/Model/AliasesResponseBase.md)
- [AlipayRequest](docs/Model/AlipayRequest.md)
- [AmazonFraudContext](docs/Model/AmazonFraudContext.md)
- [AmazonPayRequest](docs/Model/AmazonPayRequest.md)
- [ApplePayRequest](docs/Model/ApplePayRequest.md)
- [ApplePayValidateRequest](docs/Model/ApplePayValidateRequest.md)
- [Article](docs/Model/Article.md)
- [AuthorizeDetail](docs/Model/AuthorizeDetail.md)
- [AuthorizeError](docs/Model/AuthorizeError.md)
- [AuthorizeRequest](docs/Model/AuthorizeRequest.md)
- [AuthorizeResponse](docs/Model/AuthorizeResponse.md)
- [AuthorizeSplitError](docs/Model/AuthorizeSplitError.md)
- [AuthorizeSplitRequest](docs/Model/AuthorizeSplitRequest.md)
- [AuthorizeSplitResponse](docs/Model/AuthorizeSplitResponse.md)
- [AuthorizeSplitThreeDSecure](docs/Model/AuthorizeSplitThreeDSecure.md)
- [BillingAddress](docs/Model/BillingAddress.md)
- [BoncardRequest](docs/Model/BoncardRequest.md)
- [Browser](docs/Model/Browser.md)
- [BulkSaleReportRequest](docs/Model/BulkSaleReportRequest.md)
- [BuyerMetaData](docs/Model/BuyerMetaData.md)
- [ByjunoAuthorizeRequest](docs/Model/ByjunoAuthorizeRequest.md)
- [ByjunoDetail](docs/Model/ByjunoDetail.md)
- [ByjunoScreenRequest](docs/Model/ByjunoScreenRequest.md)
- [CancelDetail](docs/Model/CancelDetail.md)
- [CancelRequest](docs/Model/CancelRequest.md)
- [CardAuthorizeRequest](docs/Model/CardAuthorizeRequest.md)
- [CardDetail](docs/Model/CardDetail.md)
- [CardInfo](docs/Model/CardInfo.md)
- [CardInitRequest](docs/Model/CardInitRequest.md)
- [CardInitThreeDSecure](docs/Model/CardInitThreeDSecure.md)
- [CardValidateRequest](docs/Model/CardValidateRequest.md)
- [Cardholder](docs/Model/Cardholder.md)
- [CardholderAccount](docs/Model/CardholderAccount.md)
- [CardholderAccountInformation](docs/Model/CardholderAccountInformation.md)
- [CardholderPhoneNumber](docs/Model/CardholderPhoneNumber.md)
- [CreditDetail](docs/Model/CreditDetail.md)
- [CreditError](docs/Model/CreditError.md)
- [CreditRequest](docs/Model/CreditRequest.md)
- [CreditResponse](docs/Model/CreditResponse.md)
- [Customer](docs/Model/Customer.md)
- [CustomerRequest](docs/Model/CustomerRequest.md)
- [Detail](docs/Model/Detail.md)
- [EMVCo3DAuthenticationDataAuthorizeRequest](docs/Model/EMVCo3DAuthenticationDataAuthorizeRequest.md)
- [EMVCo3DAuthenticationDataStatusResponse](docs/Model/EMVCo3DAuthenticationDataStatusResponse.md)
- [ESY](docs/Model/ESY.md)
- [EasyPayValidateRequest](docs/Model/EasyPayValidateRequest.md)
- [ElvDetail](docs/Model/ElvDetail.md)
- [Ep2](docs/Model/Ep2.md)
- [EpsRequest](docs/Model/EpsRequest.md)
- [FailDetail](docs/Model/FailDetail.md)
- [GooglePayRequest](docs/Model/GooglePayRequest.md)
- [GooglePayValidateRequest](docs/Model/GooglePayValidateRequest.md)
- [Header](docs/Model/Header.md)
- [InitDetail](docs/Model/InitDetail.md)
- [InitRequest](docs/Model/InitRequest.md)
- [InitResponse](docs/Model/InitResponse.md)
- [Installment](docs/Model/Installment.md)
- [KlarnaArena](docs/Model/KlarnaArena.md)
- [KlarnaAuthorizeRequest](docs/Model/KlarnaAuthorizeRequest.md)
- [KlarnaCustomerAccountInfo](docs/Model/KlarnaCustomerAccountInfo.md)
- [KlarnaDetail](docs/Model/KlarnaDetail.md)
- [KlarnaEvent](docs/Model/KlarnaEvent.md)
- [KlarnaInitRequest](docs/Model/KlarnaInitRequest.md)
- [KlarnaPaymentHistoryFull](docs/Model/KlarnaPaymentHistoryFull.md)
- [KlarnaPaymentHistorySimple](docs/Model/KlarnaPaymentHistorySimple.md)
- [KlarnaSubscription](docs/Model/KlarnaSubscription.md)
- [KlarnaValidateRequest](docs/Model/KlarnaValidateRequest.md)
- [Leg](docs/Model/Leg.md)
- [LocalTime](docs/Model/LocalTime.md)
- [MDPDetail](docs/Model/MDPDetail.md)
- [MDPInitRequest](docs/Model/MDPInitRequest.md)
- [MFXDetail](docs/Model/MFXDetail.md)
- [MFXRequest](docs/Model/MFXRequest.md)
- [MPXDetail](docs/Model/MPXDetail.md)
- [MPXRequest](docs/Model/MPXRequest.md)
- [MarketPlace](docs/Model/MarketPlace.md)
- [MarketPlaceAuthorize](docs/Model/MarketPlaceAuthorize.md)
- [MarketPlaceCredit](docs/Model/MarketPlaceCredit.md)
- [MarketPlaceSettle](docs/Model/MarketPlaceSettle.md)
- [MarketPlaceSplit](docs/Model/MarketPlaceSplit.md)
- [MerchantData](docs/Model/MerchantData.md)
- [MerchantRiskIndicator](docs/Model/MerchantRiskIndicator.md)
- [OptionRequest](docs/Model/OptionRequest.md)
- [OrderMetaData](docs/Model/OrderMetaData.md)
- [OrderRequest](docs/Model/OrderRequest.md)
- [Passenger](docs/Model/Passenger.md)
- [PayPalAuthorizeRequest](docs/Model/PayPalAuthorizeRequest.md)
- [PayPalDetail](docs/Model/PayPalDetail.md)
- [PayPalInitRequest](docs/Model/PayPalInitRequest.md)
- [PayPalValidateRequest](docs/Model/PayPalValidateRequest.md)
- [PaysafecardRequest](docs/Model/PaysafecardRequest.md)
- [PfcAuthorizeRequest](docs/Model/PfcAuthorizeRequest.md)
- [PfcInitRequest](docs/Model/PfcInitRequest.md)
- [PfcValidateRequest](docs/Model/PfcValidateRequest.md)
- [PostfinanceDetail](docs/Model/PostfinanceDetail.md)
- [Purchase](docs/Model/Purchase.md)
- [ReconciliationsError](docs/Model/ReconciliationsError.md)
- [ReconciliationsErrorCode](docs/Model/ReconciliationsErrorCode.md)
- [RedirectRequest](docs/Model/RedirectRequest.md)
- [RekaDetail](docs/Model/RekaDetail.md)
- [RekaRequest](docs/Model/RekaRequest.md)
- [SaleReportRequest](docs/Model/SaleReportRequest.md)
- [SaleReportResponse](docs/Model/SaleReportResponse.md)
- [ScreenRequest](docs/Model/ScreenRequest.md)
- [Secure3DResponse](docs/Model/Secure3DResponse.md)
- [SecureFieldsInitRequest](docs/Model/SecureFieldsInitRequest.md)
- [SecureFieldsInitResponse](docs/Model/SecureFieldsInitResponse.md)
- [SecureFieldsThreeDSecure](docs/Model/SecureFieldsThreeDSecure.md)
- [SecureFieldsUpdateRequest](docs/Model/SecureFieldsUpdateRequest.md)
- [SettleDetail](docs/Model/SettleDetail.md)
- [SettleRequest](docs/Model/SettleRequest.md)
- [ShippingAddress](docs/Model/ShippingAddress.md)
- [StatusResponse](docs/Model/StatusResponse.md)
- [SuperCard](docs/Model/SuperCard.md)
- [SwissBillingAuthorizeRequest](docs/Model/SwissBillingAuthorizeRequest.md)
- [SwissBillingRequest](docs/Model/SwissBillingRequest.md)
- [SwissPassDetail](docs/Model/SwissPassDetail.md)
- [SwissPassRequest](docs/Model/SwissPassRequest.md)
- [SwisscomPayDetail](docs/Model/SwisscomPayDetail.md)
- [Theme](docs/Model/Theme.md)
- [ThemeConfiguration](docs/Model/ThemeConfiguration.md)
- [ThreeDSRequestor](docs/Model/ThreeDSRequestor.md)
- [ThreeDSRequestorAuthenticationInformation](docs/Model/ThreeDSRequestorAuthenticationInformation.md)
- [Ticket](docs/Model/Ticket.md)
- [TransactionsError](docs/Model/TransactionsError.md)
- [TransactionsErrorCode](docs/Model/TransactionsErrorCode.md)
- [TransactionsResponseBase](docs/Model/TransactionsResponseBase.md)
- [TwintAuthorizeRequest](docs/Model/TwintAuthorizeRequest.md)
- [TwintDetail](docs/Model/TwintDetail.md)
- [TwintRequest](docs/Model/TwintRequest.md)
- [ValidateRequest](docs/Model/ValidateRequest.md)
- [WeChatDetail](docs/Model/WeChatDetail.md)
- [WeChatRequest](docs/Model/WeChatRequest.md)
- [WeChatResponse](docs/Model/WeChatResponse.md)

## Authorization

### Basic

- **Type**: HTTP basic authentication

## Tests

To run the tests, use:

```bash
composer install
vendor/bin/phpunit
```

## Author

support@datatrans.ch

## About this package

This PHP package is automatically generated by the [OpenAPI Generator](https://openapi-generator.tech) project:

- API version: `2.0.22`
- Build package: `org.openapitools.codegen.languages.PhpClientCodegen`
