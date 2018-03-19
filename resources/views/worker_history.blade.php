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
<!--    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>-->
    
    <script src="https://code.highcharts.com/stock/highstock.js"></script>
    <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
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
                <td id="online_time">
                    <div class="loading" style="margin: 0 auto;text-align: center;">
                        <img src="/images/ui-anim_basic_16x16.gif"/>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Offline time (Uptime)</td>
                <td id="offline_time">
                    <div class="loading" style="margin: 0 auto;text-align: center;">
                        <img src="/images/ui-anim_basic_16x16.gif"/>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Total shares</td>
                <td id="total_shares">
                    <div class="loading" style="margin: 0 auto;text-align: center;">
                        <img src="/images/ui-anim_basic_16x16.gif"/>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Average Hashrate</td>
                <td id="average_hashrate">
                    <div class="loading" style="margin: 0 auto;text-align: center;">
                        <img src="/images/ui-anim_basic_16x16.gif"/>
                    </div>
                </td>
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
                <tr class="loading">
                    <td colspan="3" style="margin: 0 auto;text-align: center;">
                        <div style="margin: 0 auto;text-align: center;">
                            <img src="/images/ui-anim_basic_16x16.gif"/>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="loading" style="margin: 0 auto;text-align: center;position: absolute;z-index: 9999;padding-top: 50px;width: 90%;">
        <img src="/images/ui-anim_basic_16x16.gif"/>
    </div>
    <div id="container"></div>
    

    {{--<div>--}}
    {{--<div id="container1" style="height: 400px; min-width: 310px"></div>--}}
    {{--</div>--}}
</div><!-- /.container -->

<script>

        var time='{{ $time }}';
        from_date='{{ $from_date }}';
        to_date='{{ $to_date }}';

    $.ajax({
        url: "{{ route('getHashratechartForMachine') }}",
        async: true,
        type: 'POST',
        data: {'wallet':"{{ $address }}",'id':"{{ $workerId }}",'fromDate':from_date,'toDate':to_date},
        success: function (data) {  
            $(".loading").remove();
            showGraph(data);
            data=$.parseJSON(data);
            
            online_time=parseFloat(data['online_time']);
            offline_time=parseFloat(data['offline_time']);
            total_shares=parseFloat(data['total_shares']);
            average_hashrate=parseFloat(data['average_hashrate']);
            showCommon(online_time,offline_time,total_shares,average_hashrate);
            
            showTable(data['data']);


        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
    
    function showCommon(online_time,offline_time,total_shares,average_hashrate){
        $("#online_time").html(online_time.toFixed(2)+' hours');
        $("#offline_time").html(offline_time.toFixed(2)+' hours');
        $("#total_shares").html(total_shares);
        $("#average_hashrate").html(average_hashrate.toFixed(2));
    }
    
    function showTable(data_json_array){
        for(i=0;i<data_json_array.length;i++){
            tr='<tr>'+
                    '<td data-sort="'+data_json_array[i].date_string+'">'+data_json_array[i].date_string+'</td>'+
                    '<td>'+data_json_array[i].shares+'</td>'+
                    '<td>'+data_json_array[i].hashrate+'</td>'+
                        +'</tr>';
            $('#nanopool-table tbody').append(tr);

        }
        $('#nanopool-table').DataTable();
    }
    showGraph('');
    
    function showGraph(data_json_string){
        if(data_json_string!=''){
            data_json_array=$.parseJSON(data_json_string);
            data_json_array=data_json_array.data;
            var datas=[];
            var times=[];
            var hashrates=[];
            for(i=0;i<data_json_array.length;i++){
                hashrate=parseFloat(data_json_array[i].hashrate);
                datas.push([data_json_array[i].date,hashrate]);
                times.push(parseInt(new Date(data_json_array[i].date).getTime()));
                hashrates.push(hashrate);

            }
            var max_of_array = Math.max.apply(Math, times);
            var min_of_array = Math.min.apply(Math, times);

            data={'pointStart':min_of_array,'pointInterval':600000,'dataLength':times.length,'data':hashrates};//600000: 10 phút
        }
        else{
            data={'pointStart':1230764400000,'pointInterval':600000,'dataLength':0,'data':[]};//600000: 10 phút
        }
        


        //hide text Zoom
        Highcharts.setOptions({
                lang:{
                    rangeSelectorZoom: ''
                }
        });

        // Create the chart
        Highcharts.stockChart('container', {
            chart: {
                events: {
                    load: function () {
                        this.setTitle(null, {
                            text: ''
                        });
                    }
                },
                zoomType: 'x'
            },

            rangeSelector: {

                buttons: [{
                    type: 'day',
                    count: 1,
                    text: '1d'
                }, {
                    type: 'week',
                    count: 1,
                    text: '1w'
                }, {
                    type: 'month',
                    count: 1,
                    text: '1m'
                }, {
                    type: 'month',
                    count: 6,
                    text: '6m'
                }, {
                    type: 'year',
                    count: 1,
                    text: '1y'
                }, {
                    type: 'all',
                    text: 'All'
                }],
                selected: 3
            },

            yAxis: {
                title: {
                    text: 'Hashrates'
                }
            },

            title: {
                text: 'History of reported hashrate (Average hashrate)'
            },

            subtitle: {
                text: ''
            },

            series: [{
                name: '{{ $workerId }}',
                data: data.data,
                pointStart: data.pointStart,
                pointInterval: data.pointInterval,
                tooltip: {
                    valueDecimals: 0,
                    valueSuffix: ''
                }
            }]

        });
        
        $(".highcharts-credits").html('');
        
    }

</script>
</body>
</html>

