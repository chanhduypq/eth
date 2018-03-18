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
    <style>
        .scroll{
            max-height: 300px;
            overflow: scroll;            
        }
        .no_scroll{
            overflow: hidden !important;
            max-height: max-content !important;
        }
    </style>
</head>
<body>

<div class="container">


    <div class="starter-template">
        <h1>Nanopool</h1>
    </div>
    <div>

        <a href="{{ route('multipleWorkerHistory', ['group' => $group, 'time' => 'all']) }}" class="btn btn-success @if($time == 'all') disabled @endif ">All time</a>
        <a href="{{ route('multipleWorkerHistory', ['group' => $group, 'time' => 'month']) }}" class="btn btn-success @if($time == 'month') disabled @endif ">1 Month</a>
        <a href="{{ route('multipleWorkerHistory', ['group' => $group, 'time' => 'week']) }}" class="btn btn-success @if($time == 'week') disabled @endif ">1 Week</a>
        <a href="{{ route('multipleWorkerHistory', ['group' => $group, 'time' => 'day']) }}" class="btn btn-success @if($time == 'day') disabled @endif ">1 Day</a> <br><br>


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
    <div class="loading" style="margin: 0 auto;text-align: center;position: absolute;z-index: 9999;width: 90%;">
        <img src="/images/ui-anim_basic_16x16.gif"/>
        <div>0/<?php echo count(json_decode($machines));?> machines</div>
    </div>
    <br><br>
    <div>
        <table class="table table-bordered" id="nanopool-table">
            <thead>
            <th>worker ID</th>
            <th>date</th>
            <th>shares</th>
            <th>hashrate</th>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <br><br><br><br>
    <div class="loading" style="margin: 0 auto;text-align: center;position: absolute;z-index: 9999;width: 90%;">
        <img src="/images/ui-anim_basic_16x16.gif"/>
        <div>0/<?php echo count(json_decode($machines));?> machines</div>
    </div>
    <br><br>
    <div id="container"></div>
    

    {{--<div>--}}
    {{--<div id="container1" style="height: 400px; min-width: 310px"></div>--}}
    {{--</div>--}}
</div><!-- /.container -->
<script>
    var number_of_load_machine_complete=0;
    var online_time=0,offline_time=0,total_shares=0,average_hashrate=0;
    var machines='<?php echo $machines;?>';
    var series=[];
    var times=[];
    machines=$.parseJSON(machines);
    var time='{{ $time }}';
    
    for(i=0;i<machines.length;i++){
        loadData(machines[i].wallet_address,machines[i].machine_id);
    }
    
    function showCommon(online_time,offline_time,total_shares,average_hashrate){
        $("#online_time").html(online_time.toFixed(2)+' hours');
        $("#offline_time").html(offline_time.toFixed(2)+' hours');
        $("#total_shares").html(total_shares);
        $("#average_hashrate").html(average_hashrate.toFixed(2));
    }
        
    function loadData(wallet,id){
        if(time=='all'){
            from='_';
            to='_';
        }
        else{//tính sau tuetc
            from='03/17/2018';
            to='03/18/2018';
        }
        $.ajax({
            url: "{{ route('getHashratechartForMachine') }}",
            async: true,
            type: 'POST',
            data: {'wallet':wallet,'id':id,'fromDate':from,'toDate':to},
            success: function (data) {                    
                data=$.parseJSON(data);
                online_time+=parseFloat(data['online_time']);
                offline_time+=parseFloat(data['offline_time']);
                total_shares+=parseFloat(data['total_shares']);
                average_hashrate+=parseFloat(data['average_hashrate']);
                if(!($('#nanopool-table').parent().hasClass('scroll'))){
                    $('#nanopool-table').parent().addClass('scroll');
                }
                showTableForOneMachine(data['data'],data['machine_id']);
                
                temp=data['data'];
                hashrates=[];
                for(i=0;i<temp.length;i++){
                    hashrate=parseFloat(temp[i].hashrate);
                    hashrates.push(hashrate);
                    times.push(parseInt(new Date(temp[i].date).getTime()));
                }

                min_of_array = Math.min.apply(Math, times);
                pie.addSeries({'name':id,'data':hashrates,'pointStart':min_of_array,'pointInterval':600000,'tooltip': {'valueDecimals': 0,'valueSuffix': ''}});
                
                number_of_load_machine_complete++;
                $(".loading div").html(number_of_load_machine_complete+" / "+machines.length+" machines");
                if(number_of_load_machine_complete == machines.length){
                    average_hashrate=average_hashrate/number_of_load_machine_complete;
                    showCommon(online_time,offline_time,total_shares,average_hashrate);
                    $(".loading").remove();
                    $('#nanopool-table').DataTable();
                    $('#nanopool-table_wrapper').parent().removeClass('scroll');
                }
                

            },
            error: function (request, status, error) {
                console.log(request.responseText);
            }
        });
    }
    
//    $.ajax({
//        url: "{{ route('getHashrateHistoryForMultiMachine') }}",
//        async: true,
//        type: 'POST',
//        data: {'group':"{{ $group }}",'time':"{{ $time }}"},
//        success: function (data) {
//            $(".loading").remove();
//            showGraph(data);
//            showTable(data);
//            showCommon(data);
//        },
//        error: function (request, status, error) {
//            console.log(request.responseText);
//        }
//    });
    
    
    function showTableForOneMachine(data_json_array,machine_id){
        for(i=0;i<data_json_array.length;i++){
            tr='<tr>'+
                    '<td>'+machine_id+'</td>'+
                    '<td data-sort="'+data_json_array[i].date+'">'+data_json_array[i].date+'</td>'+
                    '<td>'+data_json_array[i].shares+'</td>'+
                    '<td>'+data_json_array[i].hashrate+'</td>'+
                        +'</tr>';
            $('#nanopool-table tbody').append(tr);

        }
        
//        $('#nanopool-table').DataTable();
    }
    
    showGraph('');
    
    function showGraph(data_json_string){
        if(data_json_string!=''){
            series=[];
            data_json_array=$.parseJSON(data_json_string);
            data_json_array=data_json_array.data;
            for(key in data_json_array){

                temp=data_json_array[key];
                times=[];
                hashrates=[];
                for(i=0;i<temp.length;i++){
                    hashrate=parseFloat(temp[i].hashrate);
                    hashrates.push(hashrate);
                    times.push(parseInt(new Date(temp[i].date).getTime()));
                }

                min_of_array = Math.min.apply(Math, times);
                series.push({'name':key,'data':hashrates,'pointStart':min_of_array,'pointInterval':600000,'tooltip': {'valueDecimals': 0,'valueSuffix': ''}});
            }
        }
        else{
            series=[];
            data={'pointStart':1230764400000,'pointInterval':600000,'dataLength':0,'data':[]};//600000: 10 phút
        }
        


        //hide text Zoom
        Highcharts.setOptions({
                lang:{
                    rangeSelectorZoom: ''
                }
        });

        // Create the chart
        pie = new Highcharts.stockChart('container', {
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
                name: '',
                data: [],
                pointStart: 1230764400000,
                pointInterval: 600000,
                tooltip: {
                    valueDecimals: 0,
                    valueSuffix: ''
                }
            }]

        });
        
        $(".highcharts-credits").html('');
        
    }
    
    
        
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

            series: series,

            exporting: {
                enabled: false
            },
            
            xAxis: {
                labels: {enabled:false}

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
    
    for(i=0;i<datas.length;i++){
        pie.series[i].setData( datas[i].slice(from, to) );
    }

//     pie.series.setData( datas.slice(from, to) );
    
    // Prevent click eventpropagation
    return false;
}

//pageselectCallback(0);
//

//    $(function(){
//
//        if($('#nanopool-table').length > 0) {
//            $('#nanopool-table').DataTable({"pageLength": parseInt(10*(series.length))});
//        }
//        
//    });

</script>
</body>
</html>

