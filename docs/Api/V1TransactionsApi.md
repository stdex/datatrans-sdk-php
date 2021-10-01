# Datatrans\Client\V1TransactionsApi

All URIs are relative to https://api.sandbox.datatrans.com.

Method | HTTP request | Description
------------- | ------------- | -------------
[**authorize()**](V1TransactionsApi.md#authorize) | **POST** /v1/transactions/authorize | Authorize a transaction
[**authorizeSplit()**](V1TransactionsApi.md#authorizeSplit) | **POST** /v1/transactions/{transactionId}/authorize | Authorize an authenticated transaction
[**cancel()**](V1TransactionsApi.md#cancel) | **POST** /v1/transactions/{transactionId}/cancel | Cancel a transaction
[**credit()**](V1TransactionsApi.md#credit) | **POST** /v1/transactions/{transactionId}/credit | Refund a transaction
[**init()**](V1TransactionsApi.md#init) | **POST** /v1/transactions | Initialize a transaction
[**screen()**](V1TransactionsApi.md#screen) | **POST** /v1/transactions/screen | Screen the customer details
[**secureFieldsInit()**](V1TransactionsApi.md#secureFieldsInit) | **POST** /v1/transactions/secureFields | Initialize a Secure Fields transaction
[**secureFieldsUpdate()**](V1TransactionsApi.md#secureFieldsUpdate) | **PATCH** /v1/transactions/secureFields/{transactionId} | Update the amount of a Secure Fields transaction
[**settle()**](V1TransactionsApi.md#settle) | **POST** /v1/transactions/{transactionId}/settle | Settle a transaction
[**status()**](V1TransactionsApi.md#status) | **GET** /v1/transactions/{transactionId} | Checking the status of a transaction
[**validate()**](V1TransactionsApi.md#validate) | **POST** /v1/transactions/validate | Validate an existing alias


## `authorize()`

```php
authorize($authorize_request): \Datatrans\Client\Model\AuthorizeResponse
```

Authorize a transaction

To create a transaction without user interaction, send all required parameters to our authorize endpoint. This is the API call for merchant-initiated transactions with an existing `alias`. Depending on the payment method, additional parameters will be required. Refer to the payment method specific objects (for example `PAP`) to see which parameters are required additionally send. For credit cards, the `card` object has to be used

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1TransactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorize_request = {"currency":"CHF","refno":"Q7eHBiQ40","card":{"alias":"AAABcH0Bq92s3kgAESIAAbGj5NIsAHWC","expiryMonth":"12","expiryYear":"21"},"amount":1000}; // \Datatrans\Client\Model\AuthorizeRequest

try {
    $result = $apiInstance->authorize($authorize_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1TransactionsApi->authorize: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorize_request** | [**\Datatrans\Client\Model\AuthorizeRequest**](../Model/AuthorizeRequest.md)|  |

### Return type

[**\Datatrans\Client\Model\AuthorizeResponse**](../Model/AuthorizeResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `authorizeSplit()`

```php
authorizeSplit($transaction_id, $authorize_split_request): \Datatrans\Client\Model\AuthorizeSplitResponse
```

Authorize an authenticated transaction

Use this API endpoint to fully authorize an already authenticated transaction. This call is required for any transaction done with our Secure Fields or if during the initialization of a transaction the parameter `option.authenticationOnly` was set to `true`

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1TransactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$transaction_id = 56; // int
$authorize_split_request = {"amount":1000,"refno":"uVEireCD1"}; // \Datatrans\Client\Model\AuthorizeSplitRequest

try {
    $result = $apiInstance->authorizeSplit($transaction_id, $authorize_split_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1TransactionsApi->authorizeSplit: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **transaction_id** | **int**|  |
 **authorize_split_request** | [**\Datatrans\Client\Model\AuthorizeSplitRequest**](../Model/AuthorizeSplitRequest.md)|  |

### Return type

[**\Datatrans\Client\Model\AuthorizeSplitResponse**](../Model/AuthorizeSplitResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `cancel()`

```php
cancel($transaction_id, $cancel_request)
```

Cancel a transaction

Cancel requests can be used to release a blocked amount from an authorization. The transaction must either be in status `authorized` or `settled`. The `transactionId` is needed to cancel an authorization

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1TransactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$transaction_id = 56; // int
$cancel_request = {"refno":"B2qaJjHIm"}; // \Datatrans\Client\Model\CancelRequest | Cancel a transaction

try {
    $apiInstance->cancel($transaction_id, $cancel_request);
} catch (Exception $e) {
    echo 'Exception when calling V1TransactionsApi->cancel: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **transaction_id** | **int**|  |
 **cancel_request** | [**\Datatrans\Client\Model\CancelRequest**](../Model/CancelRequest.md)| Cancel a transaction |

### Return type

void (empty response body)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `credit()`

```php
credit($transaction_id, $credit_request): \Datatrans\Client\Model\CreditResponse
```

Refund a transaction

Refund requests can be used to credit a transaction which is in status `settled`. The previously settled amount must not be exceeded.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1TransactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$transaction_id = 56; // int
$credit_request = {"amount":1000,"currency":"CHF","refno":"1AmHBsXrB"}; // \Datatrans\Client\Model\CreditRequest | Credit a transaction

try {
    $result = $apiInstance->credit($transaction_id, $credit_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1TransactionsApi->credit: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **transaction_id** | **int**|  |
 **credit_request** | [**\Datatrans\Client\Model\CreditRequest**](../Model/CreditRequest.md)| Credit a transaction |

### Return type

[**\Datatrans\Client\Model\CreditResponse**](../Model/CreditResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `init()`

```php
init($init_request): \Datatrans\Client\Model\InitResponse
```

Initialize a transaction

Securely send all the needed parameters to the transaction initialization API. The result of this API call is a `HTTP 201` status code with a `transactionId` in the response body and the `Location` header set. This call is required to proceed with our Redirect and Lightbox integration

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1TransactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$init_request = {"currency":"CHF","refno":"qpacYElhE","amount":1337,"redirect":{"successUrl":"https://pay.sandbox.datatrans.com/upp/merchant/successPage.jsp","cancelUrl":"https://pay.sandbox.datatrans.com/upp/merchant/cancelPage.jsp","errorUrl":"https://pay.sandbox.datatrans.com/upp/merchant/errorPage.jsp"}}; // \Datatrans\Client\Model\InitRequest

try {
    $result = $apiInstance->init($init_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1TransactionsApi->init: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **init_request** | [**\Datatrans\Client\Model\InitRequest**](../Model/InitRequest.md)|  |

### Return type

[**\Datatrans\Client\Model\InitResponse**](../Model/InitResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `screen()`

```php
screen($screen_request): \Datatrans\Client\Model\AuthorizeResponse
```

Screen the customer details

Check the customer's credit score before sending an actual authorization request. No amount will be blocked on the customers account. Currently, only invoicing method `INT` support screening.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1TransactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$screen_request = {"amount":2000,"currency":"CHF","refno":"ys11sPoU6","customer":{"id":"10067822","title":"Herr","firstName":"Markus","lastName":"Uberland","street":"Amstelstrasse","street2":"11","city":"Allschwil","country":"CH","zipCode":"4123","phone":"0448111111","cellPhone":"0448222222","email":"test@gmail.com","gender":"male","birthDate":"1986-05-14","language":"DE","type":"P","ipAddress":"213.55.184.229"},"INT":{"deliveryMethod":"POST","deviceFingerprintId":"635822543440473727","paperInvoice":false,"repaymentType":4,"riskOwner":"IJ","verifiedDocument1Type":"swiss-travel-pass","verifiedDocument1Number":"5000200001","verifiedDocument1Issuer":"SBB"}}; // \Datatrans\Client\Model\ScreenRequest | Screen request

try {
    $result = $apiInstance->screen($screen_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1TransactionsApi->screen: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **screen_request** | [**\Datatrans\Client\Model\ScreenRequest**](../Model/ScreenRequest.md)| Screen request |

### Return type

[**\Datatrans\Client\Model\AuthorizeResponse**](../Model/AuthorizeResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `secureFieldsInit()`

```php
secureFieldsInit($secure_fields_init_request): \Datatrans\Client\Model\SecureFieldsInitResponse
```

Initialize a Secure Fields transaction

Proceed with the steps below to process [Secure Fields payment transactions](https://docs.datatrans.ch/docs/integrations-secure-fields):  - Call the /v1/transactions/secureFields endpoint to retrieve a `transactionId`. The success result of this API call is a `HTTP 201` status code with a `transactionId` in the response body. - Initialize the `SecureFields` JavaScript library with the returned `transactionId`: ```js var secureFields = new SecureFields(); secureFields.init(     transactionId, {         cardNumber: \"cardNumberPlaceholder\",         cvv: \"cvvPlaceholder\",     }); ``` - Handle the `success` event of the `secureFields.submit()` call. Example `success` event data: ```json {     \"event\":\"success\",     \"data\": {         \"transactionId\":\"{transactionId}\",         \"cardInfo\":{\"brand\":\"MASTERCARD\",\"type\":\"credit\",\"usage\":\"consumer\",\"country\":\"CH\",\"issuer\":\"DATATRANS\"},         \"redirect\":\"https://pay.sandbox.datatrans.com/upp/v1/3D2/{transactionId}\"     } } ``` - If 3D authentication is required, the `redirect` property will indicate the URL that the browser needs to be redirected to. - Use the [Authorize an authenticated transaction](#operation/authorize-split) endpoint to authorize the Secure Fields transaction. This is required to finalize the authorization process with Secure Fields. - Use the `transactionId` to check the [status](#operation/status) and to [settle](#operation/settle), [cancel](#operation/cancel) or [credit (refund)](#operation/refund) an transaction.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1TransactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$secure_fields_init_request = {"amount":100,"currency":"CHF","returnUrl":"http://example.org/return"}; // \Datatrans\Client\Model\SecureFieldsInitRequest

try {
    $result = $apiInstance->secureFieldsInit($secure_fields_init_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1TransactionsApi->secureFieldsInit: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **secure_fields_init_request** | [**\Datatrans\Client\Model\SecureFieldsInitRequest**](../Model/SecureFieldsInitRequest.md)|  |

### Return type

[**\Datatrans\Client\Model\SecureFieldsInitResponse**](../Model/SecureFieldsInitResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `secureFieldsUpdate()`

```php
secureFieldsUpdate($transaction_id, $secure_fields_update_request)
```

Update the amount of a Secure Fields transaction

Use this API to update the amount of a Secure Fields transaction. This action is only allowed before the 3D process. At least one property must be updated.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1TransactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$transaction_id = 56; // int
$secure_fields_update_request = {"amount":1338}; // \Datatrans\Client\Model\SecureFieldsUpdateRequest

try {
    $apiInstance->secureFieldsUpdate($transaction_id, $secure_fields_update_request);
} catch (Exception $e) {
    echo 'Exception when calling V1TransactionsApi->secureFieldsUpdate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **transaction_id** | **int**|  |
 **secure_fields_update_request** | [**\Datatrans\Client\Model\SecureFieldsUpdateRequest**](../Model/SecureFieldsUpdateRequest.md)|  |

### Return type

void (empty response body)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `settle()`

```php
settle($transaction_id, $settle_request)
```

Settle a transaction

The Settlement request is often also referred to as “Capture” or “Clearing”. It can be used for the settlement of previously authorized transactions. Only after settling a transaction the funds will be credited to your bank accountThe `transactionId` is needed to settle an authorization. This API call is not needed if `autoSettle` was set to `true` when [initializing a transaction](#operation/init).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1TransactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$transaction_id = 56; // int
$settle_request = {"amount":1000,"currency":"CHF","refno":"UsFPQ5vbi"}; // \Datatrans\Client\Model\SettleRequest

try {
    $apiInstance->settle($transaction_id, $settle_request);
} catch (Exception $e) {
    echo 'Exception when calling V1TransactionsApi->settle: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **transaction_id** | **int**|  |
 **settle_request** | [**\Datatrans\Client\Model\SettleRequest**](../Model/SettleRequest.md)|  |

### Return type

void (empty response body)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `status()`

```php
status($transaction_id): \Datatrans\Client\Model\StatusResponse
```

Checking the status of a transaction

The API endpoint status can be used to check the status of any transaction, see its history, and retrieve the card information.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1TransactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$transaction_id = 56; // int

try {
    $result = $apiInstance->status($transaction_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1TransactionsApi->status: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **transaction_id** | **int**|  |

### Return type

[**\Datatrans\Client\Model\StatusResponse**](../Model/StatusResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `validate()`

```php
validate($validate_request): \Datatrans\Client\Model\AuthorizeResponse
```

Validate an existing alias

An existing alias can be validated at any time with the transaction validate API. No amount will be blocked on the customers account. Only credit cards (including Apple Pay and Google Pay), `PFC`, `KLN` and `PAP` support validation of an existing alias.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1TransactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$validate_request = {"refno":"b9pQDjOgu","currency":"CHF","card":{"alias":"AAABcH0Bq92s3kgAESIAAbGj5NIsAHWC","expiryMonth":"12","expiryYear":"21"}}; // \Datatrans\Client\Model\ValidateRequest | Validate an alias

try {
    $result = $apiInstance->validate($validate_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1TransactionsApi->validate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **validate_request** | [**\Datatrans\Client\Model\ValidateRequest**](../Model/ValidateRequest.md)| Validate an alias |

### Return type

[**\Datatrans\Client\Model\AuthorizeResponse**](../Model/AuthorizeResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
