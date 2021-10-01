# # MarketPlaceSplit

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**sub_merchant_id** | **string** | Your sub-merchant&#39;s ID. This value is specified by your collector. | [optional]
**amount** | **int** | The share of the transaction that you want to be transferred to / from a sub-merchant&#39;s account in the currency&#39;s smallest unit. For example use 1000 for CHF 10.00. The amount cannot be 0. The sum of all split amounts have to match the transaction amount. | [optional]
**commission** | **int** | Your marketplace commission in the currency&#39;s smallest unit. For example use 1000 for CHF 10.00. The commission will always be deducted from the split amount and can therefore not be higher than the split amount. For settlements, the commission will be deducted from the amount to be paid out to the sub-merchant and credited to your marketplace account. For refunds, the commission will be deducted from the amount to be debited from the sub-merchant and debited instead from your marketplace account. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
