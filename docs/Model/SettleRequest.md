# # SettleRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**amount** | **int** | The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. |
**currency** | **string** | 3 letter &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_4217&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-4217&lt;/a&gt; character code. For example &#x60;CHF&#x60; or &#x60;USD&#x60; |
**refno** | **string** | The merchant&#39;s reference number. Most payment methods require you to have a unique reference for a transaction. In case you must change the reference number in settlement, ensure first it is supported by the dedicated payment method. |
**refno2** | **string** | Optional customer&#39;s reference number. Supported by some payment methods or acquirers. | [optional]
**airline_data** | [**\Datatrans\Client\Model\AirlineDataRequest**](AirlineDataRequest.md) |  | [optional]
**marketplace** | [**\Datatrans\Client\Model\MarketPlaceSettle**](MarketPlaceSettle.md) |  | [optional]
**extensions** | **object** | An object for additional data needed by some merchants for customized processes. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
