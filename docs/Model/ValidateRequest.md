# # ValidateRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**refno** | **string** | The merchant&#39;s reference number. It should be unique for each transaction. |
**refno2** | **string** | Optional customer&#39;s reference number. Supported by some payment methods or acquirers. | [optional]
**currency** | **string** | 3 letter &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_4217&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-4217&lt;/a&gt; character code. For example &#x60;CHF&#x60; or &#x60;USD&#x60; |
**card** | [**\Datatrans\Client\Model\CardValidateRequest**](CardValidateRequest.md) |  | [optional]
**pfc** | [**\Datatrans\Client\Model\PfcValidateRequest**](PfcValidateRequest.md) |  | [optional]
**kln** | [**\Datatrans\Client\Model\KlarnaValidateRequest**](KlarnaValidateRequest.md) |  | [optional]
**pap** | [**\Datatrans\Client\Model\PayPalValidateRequest**](PayPalValidateRequest.md) |  | [optional]
**pay** | [**\Datatrans\Client\Model\GooglePayValidateRequest**](GooglePayValidateRequest.md) |  | [optional]
**apl** | [**\Datatrans\Client\Model\ApplePayValidateRequest**](ApplePayValidateRequest.md) |  | [optional]
**esy** | [**\Datatrans\Client\Model\EasyPayValidateRequest**](EasyPayValidateRequest.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
