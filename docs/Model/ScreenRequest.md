# # ScreenRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**amount** | **int** | The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. |
**currency** | **string** | 3 letter &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_4217&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-4217&lt;/a&gt; character code. For example &#x60;CHF&#x60; or &#x60;USD&#x60; |
**refno** | **string** | The merchant&#39;s reference number. It should be unique for each transaction. |
**customer** | [**\Datatrans\Client\Model\CustomerRequest**](CustomerRequest.md) |  | [optional]
**billing** | [**\Datatrans\Client\Model\BillingAddress**](BillingAddress.md) |  | [optional]
**shipping** | [**\Datatrans\Client\Model\ShippingAddress**](ShippingAddress.md) |  | [optional]
**int** | [**\Datatrans\Client\Model\ByjunoScreenRequest**](ByjunoScreenRequest.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
