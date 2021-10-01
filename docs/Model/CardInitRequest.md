# # CardInitRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**alias** | **string** | Alias received for example from a previous transaction if &#x60;option.createAlias: true&#x60; was used. In order to retrieve the alias from a previous transaction, use the [Status API](#operation/status). | [optional]
**expiry_month** | **string** | The expiry month of the credit card alias. | [optional]
**expiry_year** | **string** | The expiry year of the credit card alias | [optional]
**create_alias_cvv** | **bool** | Specifies whether a CVV alias should be created | [optional]
**_3_d** | [**\Datatrans\Client\Model\CardInitThreeDSecure**](CardInitThreeDSecure.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
