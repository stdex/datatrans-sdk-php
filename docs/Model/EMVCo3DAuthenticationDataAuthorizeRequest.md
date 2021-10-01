# # EMVCo3DAuthenticationDataAuthorizeRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**eci** | **string** | The Electronic Commerce Indicator | [optional]
**xid** | **string** | The transaction ID returned by the directory server | [optional]
**three_ds_transaction_id** | **string** | The transaction ID returned by the 3D Secure Provider | [optional]
**cavv** | **string** | The Cardholder Authentication Verification Value | [optional]
**three_ds_version** | **string** | The 3D version | [optional]
**cavv_algorithm** | **string** | The 3D algorithm | [optional]
**directory_response** | **string** | Transaction status after &#x60;ARes&#x60;  |Value|3Dv1|3Dv2| |:---|:---|:---| |Y| enrolled| authenticated| |N| not enrolled| authentication failed| |U| not available| not available| |C| |challenge needed| |R| |rejected| |A| |authentication attempt| | [optional]
**authentication_response** | **string** | Transaction status after &#x60;RReq&#x60; (Challenge flow)  |Value|3Dv1|3Dv2| |:---|:---|:---| |Y| authenticated| authenticated| |N| authentication failed| authentication failed| |U| not available| not available| |A| authentication attempt| authentication attempt| |C| process incomplete| process incomplete| |D| not enrolled| | | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
