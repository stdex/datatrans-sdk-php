# # AccardaRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**mode** | **string** | Defines the type of the payment | [default to 'invoice']
**installment** | [**\Datatrans\Client\Model\Installment**](Installment.md) |  | [optional]
**channel** | **string** | The invoice channel | [optional]
**street_split** | **bool** | If &#x60;true&#x60; the value of &#x60;customer.street&#x60; will be split into street nameand street number | [optional] [default to false]
**screening_only** | **bool** | If &#x60;true&#x60; only a pre-screening request is done. | [optional] [default to false]
**order_number** | **string** | Accarda reference number, mainly useful for B2B orders where the company doing the order might have their own ID to identify the invoice later on within their own systems. | [optional]
**coupon_amount** | **int** | Amount in the basket payed by coupon or other payment instruments. | [optional]
**attachments** | [**\Datatrans\Client\Model\AccardaAttachment[]**](AccardaAttachment.md) | List of base64 encoded attachments | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
