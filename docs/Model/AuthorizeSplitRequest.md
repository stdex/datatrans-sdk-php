# # AuthorizeSplitRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**amount** | **int** | The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. | [optional]
**refno** | **string** | The merchant&#39;s reference number. It should be unique for each transaction. |
**refno2** | **string** | Optional customer&#39;s reference number. Supported by some payment methods or acquirers. | [optional]
**auto_settle** | **bool** | Whether to automatically settle the transaction after an authorization or not. If not present, the settings defined in the dashboard (&#39;Authorisation / Settlement&#39; or &#39;Direct Debit&#39;) will be used. | [optional]
**cdm** | **object** | CyberSource specific parameters. Use the same properties as you would for direct CyberSource requests. | [optional]
**_3_d** | [**\Datatrans\Client\Model\AuthorizeSplitThreeDSecure**](AuthorizeSplitThreeDSecure.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
