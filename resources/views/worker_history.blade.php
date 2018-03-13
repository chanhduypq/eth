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
        <h1>Nanopool (Worker with ID {{ $workerId }})</h1>
    </div>
    <div>

        <a href="{{ route('workerHistory', ['wallet' => $address, 'id' => $workerId, 'time' => 'all']) }}" class="btn btn-success @if($time == 'all') disabled @endif ">All time</a>
        <a href="{{ route('workerHistory', ['wallet' => $address, 'id' => $workerId, 'time' => 'month']) }}" class="btn btn-success @if($time == 'month') disabled @endif ">1 Month</a>
        <a href="{{ route('workerHistory', ['wallet' => $address, 'id' => $workerId, 'time' => 'week']) }}" class="btn btn-success @if($time == 'week') disabled @endif ">1 Week</a>
        <a href="{{ route('workerHistory', ['wallet' => $address, 'id' => $workerId, 'time' => 'day']) }}" class="btn btn-success @if($time == 'day') disabled @endif ">1 Day</a> <br><br>


        <table class="table table-bordered">
            <tbody>
            <tr>
                <td>Online time (Uptime)</td>
                <td>{{ number_format($anyActivityShares['actvTime'], 2) }} hours</td>
            </tr>
            <tr>
                <td>Offline time (Uptime)</td>
                <td>{{ number_format($zeroActivityShares['actvTime'], 2) }} hours</td>
            </tr>
            <tr>
                <td>Total shares</td>
                <td>{{ $anyActivityShares['shares'] }}</td>
            </tr>
            <tr>
                <td>Average Hashrate</td>
                <td>{{ $averageHashRate }}</td>
            </tr>
            </tbody>
        </table>

        <br>
        <br>
    </div>
    <br>
    <div>
        <table class="table table-bordered" id="nanopool-table">
            <thead>
            <th>date</th>
            <th>shares</th>
            <th>hashrate</th>
            </thead>
            <tbody>
            @foreach($workerData as $worker)
                <tr>
                    <td data-sort="{{ strtotime($worker->date) }}">{{ date('Y-m-d H:i:s', strtotime($worker->date)) }}</td>
                    <td>{{ $worker->shares }}</td>
                    <td>{{ $worker->hashrate }}</td>
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

        var items = [{!! $workerHistory !!}];

        $('#container1').highcharts({
            chart: {
                type: 'line',
                marginTop: 50,
                backgroundColor: null,
                height: 300,
                spacingLeft: 0,
                spacingRight: 5,
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false,
                        formatter: function () {
                            return '<b>' + this.point.name + '</b>: ' + this.y + '';
                        }
                    },
                    showInLegend: true,
                    center: [70, 80],
                    size: '100%',
                },
                series: {
                    animation: {
                        duration: 1200
                    }
                }
            },

            title: {
                text: 'History of reported hashrate (Average hashrate)'
            },
            subtitle: {
                //   text: '<span class="text-centered"><h1>' + 111 +'</h1></span>',
                useHTML: true,
                align: 'right',
                verticalAlign: 'top',
                y: 0,
                x: 5,
                style: {
                    zIndex: 1
                }
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    minute: '%H:%M',
                    hour: '%H:%M',
                    day: '%b %e',
                    week: '%b %e'

                },
                tickWidth: 0,
                gridLineDashStyle: 'Dot',
                gridLineWidth: 1
            },

            yAxis: {
                min: 0,
                title: {
                    text: 'Requests',
                    style: {
                        color: '#000000',
                        fontSize: '10px'
                    }
                },
                gridLineWidth: 1,
                labels: {
                    enabled: true
                }
            },
            tooltip: {
                pointFormat: '{point.y} Mh/s'
            },
            exporting: {
                enabled: false
            },

            series: [{
                //name: 'Requests (Total requests: ' + 11 +')',
                data: items,
                pointStart: 0,
                lineWidth: 2,
                marker: {
                    symbol: "circle",
                    lineWidth: 1,
                    radius: 4
                }
            }],
            threshold: null
        });
    });

</script>
</body>
</html>

