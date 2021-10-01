# # KlarnaInitRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**sub_payment_method** | **string** | The Klarna specific payment method used for the transaction. | [optional]
**events** | [**\Datatrans\Client\Model\KlarnaEvent[]**](KlarnaEvent.md) | A list of Klarna events. | [optional]
**subscriptions** | [**\Datatrans\Client\Model\KlarnaSubscription[]**](KlarnaSubscription.md) | A list of Klarna subscriptions. | [optional]
**account_infos** | [**\Datatrans\Client\Model\KlarnaCustomerAccountInfo[]**](KlarnaCustomerAccountInfo.md) | A list of Klarna customer account infos. | [optional]
**history_simple** | [**\Datatrans\Client\Model\KlarnaPaymentHistorySimple[]**](KlarnaPaymentHistorySimple.md) | A list of simple history entries | [optional]
**history_full** | [**\Datatrans\Client\Model\KlarnaPaymentHistoryFull[]**](KlarnaPaymentHistoryFull.md) | A list of full history entries | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
