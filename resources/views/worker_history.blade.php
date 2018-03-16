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
    <script src="/js/jquery.dataTables.min.js?<?php echo substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);?>"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.20.1/vis-timeline-graph2d.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
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
    
    <div id="container"></div>
    
    <div id="Pagination" class="pagination"></div> 


    {{--<div>--}}
    {{--<div id="container1" style="height: 400px; min-width: 310px"></div>--}}
    {{--</div>--}}
</div><!-- /.container -->
<script>
    data_json_string='<?php echo json_encode($workerData);?>';
    data_json_array=$.parseJSON(data_json_string);
    var datas=[];
    for(i=0;i<data_json_array.length;i++){
        hashrate=parseFloat(data_json_array[i].hashrate);
        datas.push([data_json_array[i].date,hashrate]);
        
    }
//    Highcharts.chart('container', {
//
//        title: {
//            text: 'History of reported hashrate (Average hashrate)'
//        },
//
//        subtitle: {
//            text: ''
//        },
//
//    //    xAxis: {
//    //        type: 'datetime',
//    //        dateTimeLabelFormats: {
//    //            minute: '%H:%M',
//    //            hour: '%H:%M',
//    //            day: '%b %e',
//    //            week: '%b %e'
//    //
//    //        },
//    //        tickWidth: 0,
//    //        gridLineDashStyle: 'Dot',
//    //        gridLineWidth: 1
//    //    },
//
//        yAxis: {
//            title: {
//                text: 'Requests'
//            }
//        },
//        legend: {
//            layout: 'vertical',
//            align: 'right',
//            verticalAlign: 'middle'
//        },
//
//        plotOptions: {
//            series: {
//                label: {
//                    connectorAllowed: false
//                },
//                pointStart: 2010
//            }
//        },
//
//    //    tooltip: {
//    //        pointFormat: '{point.y} Mh/s'
//    //    },
//
//        series: [{
//            name: '{{ $workerId }}',
//            data: datas
//        }
//    //    , {
//    //        name: 'Manufacturing',
//    //        data: [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
//    //    }, {
//    //        name: 'Sales & Distribution',
//    //        data: [11744, 17722, 16005, 19771, 20185, 24377, 32147, 39387]
//    //    }, {
//    //        name: 'Project Development',
//    //        data: [null, null, 7988, 12169, 15112, 22452, 34400, 34227]
//    //    }, {
//    //        name: 'Other',
//    //        data: [12908, 5948, 8105, 11248, 8989, 11816, 18274, 18111]
//    //    }
//        ],
//
//        exporting: {
//            enabled: false
//        },
//
//        responsive: {
//            rules: [{
//                condition: {
//                    maxWidth: 500
//                },
//                chartOptions: {
//                    legend: {
//                        layout: 'horizontal',
//                        align: 'center',
//                        verticalAlign: 'bottom'
//                    }
//                }
//            }]
//        }
//
//    });


    
function pageselectCallback(page_index){

    var items_per_page = 10,
        max_elem = 10,
        from = page_index * items_per_page,
        to = from + max_elem,
        newcontent = '',
        chartData = [];

    //pie.series[0].setData( data.slice(from,to) );
    if(typeof pie !== 'object') {
        pie = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'line'
            },
            title: {
                text: 'History of reported hashrate (Average hashrate)'
            },

            subtitle: {
                text: ''
            },

        //    xAxis: {
        //        type: 'datetime',
        //        dateTimeLabelFormats: {
        //            minute: '%H:%M',
        //            hour: '%H:%M',
        //            day: '%b %e',
        //            week: '%b %e'
        //
        //        },
        //        tickWidth: 0,
        //        gridLineDashStyle: 'Dot',
        //        gridLineWidth: 1
        //    },

            yAxis: {
                title: {
                    text: 'Hashrate'
                }
            },
            xAxis: {
                labels: {enabled:false}

            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                    pointStart: 2010
                }
            },

        //    tooltip: {
        //        pointFormat: '{point.y} Mh/s'
        //    },

            series: [{
                name: '{{ $workerId }}',
                data: []
            }
        //    , {
        //        name: 'Manufacturing',
        //        data: [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
        //    }, {
        //        name: 'Sales & Distribution',
        //        data: [11744, 17722, 16005, 19771, 20185, 24377, 32147, 39387]
        //    }, {
        //        name: 'Project Development',
        //        data: [null, null, 7988, 12169, 15112, 22452, 34400, 34227]
        //    }, {
        //        name: 'Other',
        //        data: [12908, 5948, 8105, 11248, 8989, 11816, 18274, 18111]
        //    }
            ],

            exporting: {
                enabled: false
            },

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        });        
    }
    
    pie.series[0].setData( datas.slice(from, to) );
    
    // Prevent click eventpropagation
    return false;
}

pageselectCallback(0);

$(".highcharts-credits").html('');
    $(function(){

        if($('#nanopool-table').length > 0) {
            $('#nanopool-table').DataTable();
        }
        

//        var items = [{!! $workerHistory !!}];
//
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
//                    minute: '%H:%M',
//                    hour: '%H:%M',
//                    day: '%b %e',
//                    week: '%b %e'
//
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

