# # GooglePayValidateRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**signature** | **string** | Verifies that the message came from Google. It&#39;s Base64-encoded, and created with ECDSA by the intermediate signing key. | [optional]
**protocol_version** | **string** | Identifies the encryption or signing scheme under which the message was created. It allows the protocol to evolve over time, if needed. | [optional]
**signed_message** | **string** | A JSON object serialized as a string that contains the encryptedMessage, ephemeralPublicKey, and tag. It&#39;s serialized to simplify the signature verification process. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
