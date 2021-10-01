# Datatrans\Client\V1ReconciliationsApi

All URIs are relative to https://api.sandbox.datatrans.com.

Method | HTTP request | Description
------------- | ------------- | -------------
[**bulkSaleReport()**](V1ReconciliationsApi.md#bulkSaleReport) | **POST** /v1/reconciliations/sales/bulk | Bulk reporting of sales
[**saleReport()**](V1ReconciliationsApi.md#saleReport) | **POST** /v1/reconciliations/sales | Report a sale


## `bulkSaleReport()`

```php
bulkSaleReport($bulk_sale_report_request): \Datatrans\Client\Model\SaleReportResponse
```

Bulk reporting of sales

If you are a merchant using our reconciliation services, you can use this API to confirm multiple sales with a single API call. The matching is based on the `transactionId`. The status of the transaction will change to `compensated`

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1ReconciliationsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$bulk_sale_report_request = {"sales":[{"date":"2021-09-09T07:45:27.543+00:00","transactionId":"210909094527075633","currency":"CHF","amount":1000,"type":"payment","refno":"taPWf9RpR"},{"date":"2021-09-09T07:45:28.159+00:00","transactionId":"210909094527695647","currency":"CHF","amount":1000,"type":"payment","refno":"6aa1RLA8u"}]}; // \Datatrans\Client\Model\BulkSaleReportRequest

try {
    $result = $apiInstance->bulkSaleReport($bulk_sale_report_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1ReconciliationsApi->bulkSaleReport: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **bulk_sale_report_request** | [**\Datatrans\Client\Model\BulkSaleReportRequest**](../Model/BulkSaleReportRequest.md)|  |

### Return type

[**\Datatrans\Client\Model\SaleReportResponse**](../Model/SaleReportResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `saleReport()`

```php
saleReport($sale_report_request): \Datatrans\Client\Model\SaleReportResponse
```

Report a sale

If you are a merchant using our reconciliation services, you can use this API to confirm a sale. The matching is based on the `transactionId`. The status of the transaction will change to `compensated`

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure HTTP basic authorization: Basic
$config = Datatrans\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Datatrans\Client\Api\V1ReconciliationsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$sale_report_request = {"date":"2021-09-09T07:45:26.605+00:00","transactionId":"210909094524805603","currency":"CHF","amount":1000,"type":"payment","refno":"uR8SgtvAx"}; // \Datatrans\Client\Model\SaleReportRequest

try {
    $result = $apiInstance->saleReport($sale_report_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1ReconciliationsApi->saleReport: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **sale_report_request** | [**\Datatrans\Client\Model\SaleReportRequest**](../Model/SaleReportRequest.md)|  |

### Return type

[**\Datatrans\Client\Model\SaleReportResponse**](../Model/SaleReportResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
