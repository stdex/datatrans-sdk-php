# # KlarnaEvent

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**name** | **string** | The name of the event. | [optional]
**company** | **string** | The name of the company arranging the event. | [optional]
**genre** | **string** | The genre of the event. | [optional]
**arena** | [**\Datatrans\Client\Model\KlarnaArena**](KlarnaArena.md) |  | [optional]
**start** | [**\DateTime**](\DateTime.md) | The start date and time of the event. Must be in &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_8601&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). | [optional]
**end** | [**\DateTime**](\DateTime.md) | The end date and time of the event. Must be in &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_8601&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). | [optional]
**access_controlled_venue** | **bool** | Tickets are digitally checked when entering the venue. | [optional]
**ticket_delivery_method** | **string** | The ticket delivery method. | [optional]
**ticket_delivery_recipient** | **string** | The name of the recipient the ticket is delivered to. If the method isEMAIL or PHONE, use either the email address or the phone number. | [optional]
**affiliate_name** | **string** | The name of the affiliate that originated the purchase. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
