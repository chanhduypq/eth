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

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.js"></script>
</head>
<body>

<div class="container">

    <div class="starter-template">
        <h1>Nanopool monitor</h1> (<a href="{{ route('admin.index') }}">admin
            panel</a>)
    </div>
    <br>
    <div>
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            @foreach($wallets as $key => $wallet)
                <li role="presentation" @if($key == 0) class="active" @endif><a href="#{{ $key }}" role="tab"
                                                                                id="{{ $key }}-tab" data-toggle="tab"
                                                                                aria-controls="{{ $key }}"
                                                                                aria-expanded="true">{{ $wallet->name }}
                        ({{ $wallet->address }})</a></li>
            @endforeach
        </ul>
        <div class="tab-content" id="myTabContent">
            @foreach($walletsInformation as $key => $wallet)
                <div class="tab-pane fade @if($key == 0) active in @endif" role="tabpanel" id="{{ $key }}"
                     aria-labelledby="{{ $key }}-tab">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td>Account</td>
                            <td>{{ $wallet['data']['account'] }}</td>
                        </tr>
                        <tr>
                            <td>Balance</td>
                            <td>{{ $wallet['data']['balance'] }}</td>
                        </tr>
                        <tr>
                            <td>Hashrate</td>
                            <td>{{ $wallet['data']['hashrate'] }}</td>
                        </tr>
                        <tr>
                            <td>Avg Hashrate (1 Hour)</td>
                            <td>{{ $wallet['data']['avgHashrate']['h1'] }}</td>
                        </tr>
                        <tr>
                            <td>Avg Hashrate (3 Hour)</td>
                            <td>{{ $wallet['data']['avgHashrate']['h3'] }}</td>
                        </tr>
                        <tr>
                            <td>Avg Hashrate (6 Hour)</td>
                            <td>{{ $wallet['data']['avgHashrate']['h6'] }}</td>
                        </tr>
                        <tr>
                            <td>Avg Hashrate (12 Hour)</td>
                            <td>{{ $wallet['data']['avgHashrate']['h12'] }}</td>
                        </tr>
                        <tr>
                            <td>Avg Hashrate (24 Hour)</td>
                            <td>{{ $wallet['data']['avgHashrate']['h24'] }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><a class="btn-success btn-lg btn" target="_blank"
                                   href="{{ route('payments', ['wallet' => $wallet['data']['account']]) }}">Payments</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
    <br><br><br>

    Select group please:<br><br>
    <ul class="nav nav-tabs">
        <li role="presentation" @if(!isset($groupMain)) class="active" @endif><a href="{{ route('boot') }}">All</a></li>
        @foreach($groups as $group)
            <li role="presentation" @if(isset($groupMain) &&  $groupMain == $group->id) class="active" @endif><a
                        href="{{ route('group', ['group' => $group->id]) }}">{{ $group->name }}</a></li>
        @endforeach
        <li role="presentation" @if(isset($noGroup) && $noGroup) class="active" @endif><a
                    href="{{ route('group', ['group' => 'nogroup']) }}">No group</a></li>
    </ul>

    <br>
    <br>

    <form>
        <a class="btn-success btn-sm btn"
           href="?">All time</a>
        <a class="btn-success btn-sm btn"
           href="?fromDate={{ $day }}&toDate={{ $today }}">1 Day</a>
        <a class="btn-success btn-sm btn"
           href="?fromDate={{ $week }}&toDate={{ $today }}">1 Week</a>
        <a class="btn-success btn-sm btn"
           href="?fromDate={{ $month }}&toDate={{ $today }}">1 Month</a>

        <input class="datepicker" id="fromDate" value="{{ $fromDate }}" name="fromDate" data-date-format="mm/dd/yyyy"> to
        <input class="datepicker" id="toDate" value="{{ $toDate }}" name="toDate" data-date-format="mm/dd/yyyy">

        <input class="btn-success" type="submit" value="Search">
    </form>

    <br>
    <br>
    <table class="table table-bordered">
        <tr>
            <td>Total shares</td>
            <td>{{ $statisticData['totalShares'] }}</td>
        </tr>
        <tr>
            <td>Avarage Hashrate</td>
            <td>{{ $statisticData['avgHashrate'] }}</td>
        </tr>
        <tr>
            <td>Online Time</td>
            <td>{{ $statisticData['onlineTime'] }}</td>
        </tr>
        <tr>
            <td>Offline Time</td>
            <td>{{ $statisticData['offlineTime'] }}</td>
        </tr>
    </table>

    <br>
    <div>
        <table class="table table-bordered" id="nanopool-table">
            <thead>
            <th>ID</th>
            <th>UID</th>
            <th>Hashrate</th>
            <th>Lastshare</th>
            <th>Rating</th>
            <th>Worker Average Hashrate for 1 hour</th>
            <th>Worker Average Hashrate for 3 hour</th>
            <th>Worker Average Hashrate for 6 hour</th>
            <th>Worker Average Hashrate for 12 hour</th>
            <th>Worker Average Hashrate for 24 hour</th>
            <th>Wallet name</th>
            <th>Actions</th>
            </thead>
            <tbody>
            @foreach($generalInfo as $worker)
                <tr>
                    <td>{{ $worker['id'] }}</td>
                    <td>{{ $worker['uid'] }}</td>
                    <td>{{ $worker['hashrate'] }}</td>
                    <td data-order="{{ $worker['lastshare'] }}">{{ date('Y-m-d H:i:s', $worker['lastshare']) }}</td>
                    <td>{{ $worker['rating'] }}</td>
                    <td>{{ $worker['h1'] }}</td>
                    <td>{{ $worker['h3'] }}</td>
                    <td>{{ $worker['h6'] }}</td>
                    <td>{{ $worker['h12'] }}</td>
                    <td>{{ $worker['h24'] }}</td>
                    <td>{{ $worker['wallet_name'] }}</td>
                    <td>
                        <a href="{{ route('workerHistory', ['id' => $worker['id'], 'time' => 'all', 'wallet' => $worker['address']]) }}"
                           target="_blank">View history</a></td>
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
    $(function () {

        if ($('#nanopool-table').length > 0) {
            $('#nanopool-table').DataTable();
        }

        $('#sandbox-container .input-daterange').datepicker();
        {{--var items = [{!! $historyHashRate !!}];--}}

        $('#fromDate').datepicker();
        $('#toDate').datepicker();

        //   console.log(items);
//        $('#container1').highcharts({
//            chart: {
//                type: 'line',
//                marginTop: 50,
//                backgroundColor: null,
//                height: 300,
//                spacingLeft: 0,
//                spacingRight: 5,
//            },
//            plotOptions: {
//                pie: {
//                    allowPointSelect: true,
//                    cursor: 'pointer',
//                    dataLabels: {
//                        enabled: false,
//                        formatter: function () {
//                            return '<b>' + this.point.name + '</b>: ' + this.y + '';
//                        }
//                    },
//                    showInLegend: true,
//                    center: [70, 80],
//                    size: '100%',
//                },
//                series: {
//                    animation: {
//                        duration: 1200
//                    }
//                }
//            },
//
//            title: {
//                text: 'History of reported hashrate (Average hashrate)'
//            },
//            subtitle: {
//             //   text: '<span class="text-centered"><h1>' + 111 +'</h1></span>',
//                useHTML: true,
//                align: 'right',
//                verticalAlign: 'top',
//                y: 0,
//                x: 5,
//                style: {
//                    zIndex: 1
//                }
//            },
//            xAxis: {
//                type: 'datetime',
//                dateTimeLabelFormats: {
//                    day: '%b %e',
//                    week: '%b %e',
//                },
//                tickWidth: 0,
//                gridLineDashStyle: 'Dot',
//                gridLineWidth: 1
//            },
//
//            yAxis: {
//                min: 0,
//                title: {
//                    text: 'Requests',
//                    style: {
//                        color: '#000000',
//                        fontSize: '10px'
//                    }
//                },
//                gridLineWidth: 1,
//                labels: {
//                    enabled: true
//                }
//            },
//            tooltip: {
//                pointFormat: '{point.y} Mh/s'
//            },
//            exporting: {
//                enabled: false
//            },
//
//            series: [{
//                //name: 'Requests (Total requests: ' + 11 +')',
//                data: items,
//                pointStart: 0,
//                lineWidth: 2,
//                marker: {
//                    symbol: "circle",
//                    lineWidth: 1,
//                    radius: 4
//                }
//            }],
//            threshold: null
//        });
    });

</script>
</body>
</html>

