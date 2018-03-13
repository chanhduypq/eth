<html>
<head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.20.1/vis-timeline-graph2d.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
</head>
<body>

<div class="container">


    <div class="starter-template">
        <h1>Payments for {{ $address }}</h1>
    </div>
    <div>

        <a href="{{ route('payments', ['wallet' => $address]) }}" class="btn btn-success @if($time == 'all') disabled @endif ">All time</a>
        <a href="{{ route('paymentHistory', ['wallet' => $address, 'time' => 'month']) }}" class="btn btn-success @if($time == 'month') disabled @endif ">1 Month</a>
        <a href="{{ route('paymentHistory', ['wallet' => $address, 'time' => 'week']) }}" class="btn btn-success @if($time == 'week') disabled @endif ">1 Week</a>
        <a href="{{ route('paymentHistory', ['wallet' => $address, 'time' => 'day']) }}" class="btn btn-success @if($time == 'day') disabled @endif ">1 Day</a> <br><br>

        <br>

        <b>Total amount:</b> {{ $totalSumPayment }}
    </div>
    <br>
    <div>
        <table class="table table-bordered" id="nanopool-table">
            <thead>
            <th>date</th>
            <th>amount</th>
            <th>confirmed</th>
            </thead>
            <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td data-sort="{{ $payment['date'] }}">{{ date('Y-m-d H:i:s', $payment['date']) }}</td>
                    <td>{{ $payment['amount'] }}</td>
                    <td>{{ $payment['confirmed'] ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>


    {{--<div>--}}
        {{--<div id="container1" style="height: 400px; min-width: 310px"></div>--}}
    {{--</div>--}}
</div><!-- /.container -->
<script>
    $(function(){

        if($('#nanopool-table').length > 0) {
            $('#nanopool-table').DataTable();
        }

    });

</script>
</body>
</html>

