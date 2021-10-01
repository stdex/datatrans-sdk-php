# # AlipayRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**business_type** | **string** | Business type of the merchant. | [optional]
**hotel_name** | **string** | Name of the hotel. Mandatory when businessType is Hotel. | [optional]
**checkin_time** | [**\DateTime**](\DateTime.md) | Hotel checkin time. Mandatory when business type is Hotel. Must be in &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_8601&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). | [optional]
**checkout_time** | [**\DateTime**](\DateTime.md) | Hotel checkout time. Mandatory when business type is Hotel. Must be in &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_8601&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). | [optional]
**flight_number** | **string** | Flight number, e.g. LX1234. Mandatory when businessType is Aviation. | [optional]
**departure_time** | [**\DateTime**](\DateTime.md) | The flight departure time. Mandatory when businessType is Aviation. Must be in &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_8601&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). | [optional]
**admission_notice_url** | **string** | The picture address of admission notice.Mandatory when business type is [ Overseas | Education | Affairs ]. | [optional]
**goods_info** | **string** | Goods information. Mandatory when business type is Retailing. | [optional]
**total_quantity** | **int** | Quantities of goods. Mandatory when business type is Retailing. | [optional]
**other_business_type** | **string** | Name of business type. Mandatory when business type is Other. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
