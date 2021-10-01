# # AuthorizeRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**currency** | **string** | 3 letter &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_4217&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-4217&lt;/a&gt; character code. For example &#x60;CHF&#x60; or &#x60;USD&#x60; |
**refno** | **string** | The merchant&#39;s reference number. It should be unique for each transaction. |
**refno2** | **string** | Optional customer&#39;s reference number. Supported by some payment methods or acquirers. | [optional]
**auto_settle** | **bool** | Whether to automatically settle the transaction after an authorization or not. If not present, the settings defined in the dashboard (&#39;Authorisation / Settlement&#39; or &#39;Direct Debit&#39;) will be used. | [optional]
**customer** | [**\Datatrans\Client\Model\CustomerRequest**](CustomerRequest.md) |  | [optional]
**billing** | [**\Datatrans\Client\Model\BillingAddress**](BillingAddress.md) |  | [optional]
**shipping** | [**\Datatrans\Client\Model\ShippingAddress**](ShippingAddress.md) |  | [optional]
**order** | [**\Datatrans\Client\Model\OrderRequest**](OrderRequest.md) |  | [optional]
**card** | [**\Datatrans\Client\Model\CardAuthorizeRequest**](CardAuthorizeRequest.md) |  | [optional]
**bon** | [**\Datatrans\Client\Model\BoncardRequest**](BoncardRequest.md) |  | [optional]
**pap** | [**\Datatrans\Client\Model\PayPalAuthorizeRequest**](PayPalAuthorizeRequest.md) |  | [optional]
**pfc** | [**\Datatrans\Client\Model\PfcAuthorizeRequest**](PfcAuthorizeRequest.md) |  | [optional]
**rek** | [**\Datatrans\Client\Model\RekaRequest**](RekaRequest.md) |  | [optional]
**kln** | [**\Datatrans\Client\Model\KlarnaAuthorizeRequest**](KlarnaAuthorizeRequest.md) |  | [optional]
**twi** | [**\Datatrans\Client\Model\TwintAuthorizeRequest**](TwintAuthorizeRequest.md) |  | [optional]
**int** | [**\Datatrans\Client\Model\ByjunoAuthorizeRequest**](ByjunoAuthorizeRequest.md) |  | [optional]
**esy** | [**\Datatrans\Client\Model\ESY**](ESY.md) |  | [optional]
**airline_data** | [**\Datatrans\Client\Model\AirlineDataRequest**](AirlineDataRequest.md) |  | [optional]
**amount** | **int** | The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. |
**acc** | [**\Datatrans\Client\Model\AccardaRequest**](AccardaRequest.md) |  | [optional]
**pay** | [**\Datatrans\Client\Model\GooglePayRequest**](GooglePayRequest.md) |  | [optional]
**apl** | [**\Datatrans\Client\Model\ApplePayRequest**](ApplePayRequest.md) |  | [optional]
**marketplace** | [**\Datatrans\Client\Model\MarketPlaceAuthorize**](MarketPlaceAuthorize.md) |  | [optional]
**swb** | [**\Datatrans\Client\Model\SwissBillingAuthorizeRequest**](SwissBillingAuthorizeRequest.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
