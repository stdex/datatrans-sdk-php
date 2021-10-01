# # CardDetail

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**alias** | **string** | The resulting alias, if requested or available. | [optional]
**fingerprint** | **string** | An unique identifier of the card number. Useful to identify multiple customers&#39; or the same customer&#39;s transactions where the same card was used. | [optional]
**masked** | **string** | Masked credit card number. Can be used to display on a users profile page. For example: &#x60;424242xxxxxx4242&#x60; | [optional]
**alias_cvv** | **string** | Alias of the CVV. Will be deleted immediately after authorization. | [optional]
**expiry_month** | **string** | The expiry month of the credit card alias. | [optional]
**expiry_year** | **string** | The expiry year of the credit card alias | [optional]
**info** | [**\Datatrans\Client\Model\CardInfo**](CardInfo.md) |  | [optional]
**wallet_indicator** | **string** |  | [optional]
**_3_d** | [**\Datatrans\Client\Model\EMVCo3DAuthenticationDataStatusResponse**](EMVCo3DAuthenticationDataStatusResponse.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
