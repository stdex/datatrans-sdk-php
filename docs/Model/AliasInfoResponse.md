# # AliasInfoResponse

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**alias** | **string** | The requested alias. | [optional]
**fingerprint** | **string** | An unique identifier of the card number. Useful to identify multiple customers&#39; or the same customer&#39;s transactions where the same card was used. | [optional]
**type** | **string** |  | [optional]
**masked** | **string** | The nonsensitive masked representation of the value behind the alias (e.g. &#x60;490000xxxxxx0003&#x60; for aliases of type &#x60;CARD&#x60;) | [optional]
**date_created** | [**\DateTime**](\DateTime.md) | Creation date | [optional]
**card** | [**\Datatrans\Client\Model\AliasCardInfoDetail**](AliasCardInfoDetail.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
