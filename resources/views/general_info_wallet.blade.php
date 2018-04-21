<tr>
    <td>Account</td>
    <td>{{ $generalInfo['data']['account'] }}</td>
</tr>
<tr>
    <td>Balance</td>
    <td>{{ $generalInfo['data']['balance'] }}</td>
</tr>
<tr>
    <td>Hashrate</td>
    <td>{{ $generalInfo['data']['hashrate'] }}</td>
</tr>
<tr>
    <td>Avg Hashrate (1 Hour)</td>
    <td>{{ $generalInfo['data']['avgHashrate']['h1'] }}</td>
</tr>
<tr>
    <td>Avg Hashrate (3 Hour)</td>
    <td>{{ $generalInfo['data']['avgHashrate']['h3'] }}</td>
</tr>
<tr>
    <td>Avg Hashrate (6 Hour)</td>
    <td>{{ $generalInfo['data']['avgHashrate']['h6'] }}</td>
</tr>
<tr>
    <td>Avg Hashrate (12 Hour)</td>
    <td>{{ $generalInfo['data']['avgHashrate']['h12'] }}</td>
</tr>
<tr>
    <td>Avg Hashrate (24 Hour)</td>
    <td>{{ $generalInfo['data']['avgHashrate']['h24'] }}</td>
</tr>
<tr>
    <td></td>
    <td><a class="btn-success btn-lg btn" target="_blank"
           href="{{ route('payments', ['wallet' => $generalInfo['data']['account']]) }}">Payments</a>
    </td>
</tr>
