# # StatusResponse

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**transaction_id** | **string** | The transactionId received after an authorization. | [optional]
**type** | **string** |  | [optional]
**status** | **string** |  | [optional]
**currency** | **string** | 3 letter &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_4217&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-4217&lt;/a&gt; character code. For example &#x60;CHF&#x60; or &#x60;USD&#x60; | [optional]
**refno** | **string** | The merchant&#39;s reference number. It should be unique for each transaction. | [optional]
**refno2** | **string** | Optional customer&#39;s reference number. Supported by some payment methods or acquirers. | [optional]
**payment_method** | **string** |  | [optional]
**detail** | [**\Datatrans\Client\Model\Detail**](Detail.md) |  | [optional]
**customer** | [**\Datatrans\Client\Model\Customer**](Customer.md) |  | [optional]
**cdm** | **object** | The response of the cybersource decision manager. | [optional]
**language** | **string** | The language (language code) in which the payment was presented to the cardholder. The &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-639-1&lt;/a&gt; two letter language codes listed above are supported | [optional]
**card** | [**\Datatrans\Client\Model\CardDetail**](CardDetail.md) |  | [optional]
**twi** | [**\Datatrans\Client\Model\TwintDetail**](TwintDetail.md) |  | [optional]
**pap** | [**\Datatrans\Client\Model\PayPalDetail**](PayPalDetail.md) |  | [optional]
**rek** | [**\Datatrans\Client\Model\RekaDetail**](RekaDetail.md) |  | [optional]
**elv** | [**\Datatrans\Client\Model\ElvDetail**](ElvDetail.md) |  | [optional]
**kln** | [**\Datatrans\Client\Model\KlarnaDetail**](KlarnaDetail.md) |  | [optional]
**int** | [**\Datatrans\Client\Model\ByjunoDetail**](ByjunoDetail.md) |  | [optional]
**swp** | [**\Datatrans\Client\Model\SwissPassDetail**](SwissPassDetail.md) |  | [optional]
**mfx** | [**\Datatrans\Client\Model\MFXDetail**](MFXDetail.md) |  | [optional]
**mpx** | [**\Datatrans\Client\Model\MPXDetail**](MPXDetail.md) |  | [optional]
**mdp** | [**\Datatrans\Client\Model\MDPDetail**](MDPDetail.md) |  | [optional]
**esy** | [**\Datatrans\Client\Model\SwisscomPayDetail**](SwisscomPayDetail.md) |  | [optional]
**pfc** | [**\Datatrans\Client\Model\PostfinanceDetail**](PostfinanceDetail.md) |  | [optional]
**wec** | [**\Datatrans\Client\Model\WeChatDetail**](WeChatDetail.md) |  | [optional]
**scx** | [**\Datatrans\Client\Model\SuperCard**](SuperCard.md) |  | [optional]
**history** | [**\Datatrans\Client\Model\Action[]**](Action.md) |  | [optional]
**ep2** | [**\Datatrans\Client\Model\Ep2**](Ep2.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
