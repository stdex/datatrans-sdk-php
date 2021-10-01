# # AirlineDataRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**country_code** | **string** | Passenger country code in &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-3166-1-alpha2&lt;/a&gt; format. | [optional]
**agent_code** | **string** | IATA agency code | [optional]
**pnr** | **string** | PNR | [optional]
**issue_date** | [**\DateTime**](\DateTime.md) | Ticket issuing date. Must be in &lt;a href&#x3D;&#39;https://en.wikipedia.org/wiki/ISO_8601&#39; target&#x3D;&#39;_blank&#39;&gt;ISO-8601&lt;/a&gt; format (&#x60;YYYY-MM-DD&#x60;). | [optional]
**tickets** | [**\Datatrans\Client\Model\Ticket[]**](Ticket.md) | A list of tickets for this purchase. Note: PAP only supports one ticket. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
