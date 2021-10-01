# # InitRequest

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
**card** | [**\Datatrans\Client\Model\CardInitRequest**](CardInitRequest.md) |  | [optional]
**bon** | [**\Datatrans\Client\Model\BoncardRequest**](BoncardRequest.md) |  | [optional]
**pap** | [**\Datatrans\Client\Model\PayPalInitRequest**](PayPalInitRequest.md) |  | [optional]
**pfc** | [**\Datatrans\Client\Model\PfcInitRequest**](PfcInitRequest.md) |  | [optional]
**rek** | [**\Datatrans\Client\Model\RekaRequest**](RekaRequest.md) |  | [optional]
**kln** | [**\Datatrans\Client\Model\KlarnaInitRequest**](KlarnaInitRequest.md) |  | [optional]
**twi** | [**\Datatrans\Client\Model\TwintRequest**](TwintRequest.md) |  | [optional]
**int** | [**\Datatrans\Client\Model\ByjunoAuthorizeRequest**](ByjunoAuthorizeRequest.md) |  | [optional]
**esy** | [**\Datatrans\Client\Model\ESY**](ESY.md) |  | [optional]
**airline_data** | [**\Datatrans\Client\Model\AirlineDataRequest**](AirlineDataRequest.md) |  | [optional]
**amount** | **int** | The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. Can be omitted for use cases where only a registration should take place (if the payment method supports registrations) | [optional]
**language** | **string** | This parameter specifies the language (language code) in which the payment page should be presented to the cardholder. The &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-639-1&lt;/a&gt; two letter language codes listed above are supported | [optional]
**payment_methods** | **string[]** | An array of payment method shortnames. For example &#x60;[\&quot;VIS\&quot;, \&quot;PFC\&quot;]&#x60;. If omitted, all available payment methods will be displayed on the payment page. If the Mobile SDKs are used (&#x60;returnMobileToken&#x60;), this array is mandatory. | [optional]
**theme** | [**\Datatrans\Client\Model\Theme**](Theme.md) |  | [optional]
**redirect** | [**\Datatrans\Client\Model\RedirectRequest**](RedirectRequest.md) |  | [optional]
**option** | [**\Datatrans\Client\Model\OptionRequest**](OptionRequest.md) |  | [optional]
**swp** | [**\Datatrans\Client\Model\SwissPassRequest**](SwissPassRequest.md) |  | [optional]
**mfx** | [**\Datatrans\Client\Model\MFXRequest**](MFXRequest.md) |  | [optional]
**mpx** | [**\Datatrans\Client\Model\MPXRequest**](MPXRequest.md) |  | [optional]
**azp** | [**\Datatrans\Client\Model\AmazonPayRequest**](AmazonPayRequest.md) |  | [optional]
**eps** | [**\Datatrans\Client\Model\EpsRequest**](EpsRequest.md) |  | [optional]
**alp** | [**\Datatrans\Client\Model\AlipayRequest**](AlipayRequest.md) |  | [optional]
**wec** | [**\Datatrans\Client\Model\WeChatRequest**](WeChatRequest.md) |  | [optional]
**swb** | [**\Datatrans\Client\Model\SwissBillingRequest**](SwissBillingRequest.md) |  | [optional]
**mdp** | [**\Datatrans\Client\Model\MDPInitRequest**](MDPInitRequest.md) |  | [optional]
**psc** | [**\Datatrans\Client\Model\PaysafecardRequest**](PaysafecardRequest.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
