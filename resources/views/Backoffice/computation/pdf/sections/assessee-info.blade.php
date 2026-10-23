<div class="section-title">ASSESSEE INFORMATION</div>
<table>
    <tr>
        <td width="20%" class="label">Name</td>
        <td width="30%">{{ $computation->client->name ?? 'N/A' }}</td>
        <td width="15%" class="label">PAN</td>
        <td width="35%">{{ $computation->client->pan ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Father's Name</td>
        <td>{{ $computation->client->father_name ?? 'N/A' }}</td>
        <td class="label">Aadhaar</td>
        <td>{{ $computation->client->aadhaar ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Address</td>
        <td colspan="3">{{ $computation->client->address ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">City / State</td>
        <td>{{ $computation->client->city ?? 'N/A' }}, {{ $computation->client->state ?? 'N/A' }}</td>
        <td class="label">Pincode</td>
        <td>{{ $computation->client->pincode ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Residential Status</td>
        <td>{{ $computation->client->residential_status ?? 'N/A' }}</td>
        <td class="label">Constitution</td>
        <td>{{ $computation->constitution ?? 'N/A' }}</td>
    </tr>
</table>