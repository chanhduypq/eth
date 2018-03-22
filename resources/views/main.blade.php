<?php 
//$online_time = 0;
//foreach($generalInfo as $worker) {
//    $online_time+=$worker['data_all']['online_time'];
//}
//$online_time = round($online_time, 2);

$online_time = round(count($datas['hashrates_all']) * 10 / 60, 2);
$offline_time = $datas['offline_time'];
$total_shares = $datas['total_shares'];
$average_hashrate = $datas['average_hashrate'];

?>
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
    
    <script src="https://code.highcharts.com/stock/highstock.js"></script>
    <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
    

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.js"></script>
    <style>
        label{
            cursor: pointer;
            margin-right: 20px;
        }
    </style>
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
            <li role="presentation" class="active"><a href="#all" role="tab"
                                                                                id="all-tab" data-toggle="tab"
                                                                                aria-controls="all"
                                                                                aria-expanded="true">All wallets (<?php echo count($wallets);?>)
                        </a></li>
            @foreach($wallets as $key => $wallet) 
                <li role="presentation"><a href="#{{ $key }}" role="tab"
                                                                                id="{{ $key }}-tab" data-toggle="tab"
                                                                                aria-controls="{{ $key }}"
                                                                                aria-expanded="true">{{ $wallet->name }}
                        ({{ $wallet->address }})</a></li>
            @endforeach
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade active in" role="tabpanel" id="all" data="All wallets"
                 aria-labelledby="all-tab" style="max-height: 350px;overflow-y: auto;">
                <table class="table table-bordered">
                    <tbody>
                        @foreach($wallets as $key => $wallet)  
                            <tr>
                                <td style="width: 40%;">{{ $wallet->name }} ({{ $wallet->address }})</td>
                                <td>
                                    <div>
                                        Balance: {{ $wallet->general_info['balance'] }}
                                    </div>
                                    <div>
                                        Hashrate: {{ $wallet->general_info['hashrate'] }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
            </div>
            @foreach($wallets as $key => $wallet)  
                <div class="tab-pane fade" role="tabpanel" id="{{ $key }}" data="{{ $wallet->address }}"
                     aria-labelledby="{{ $key }}-tab">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td style="width: 40%;">Account</td>
                            <td>{{ $wallet->general_info['account'] }}</td>
                        </tr>
                        <tr>
                            <td style="width: 40%;">Balance</td>
                            <td>{{ $wallet->general_info['balance'] }}</td>
                        </tr>
                        <tr>
                            <td style="width: 40%;">Hashrate</td>
                            <td>{{ $wallet->general_info['hashrate'] }}</td>
                        </tr>
                        <tr>
                            <td style="width: 40%;">Avg Hashrate (1 Hour)</td>
                            <td>{{ $wallet->general_info['avgHashrate']['h1'] }}</td>
                        </tr>
                        <tr>
                            <td style="width: 40%;">Avg Hashrate (3 Hour)</td>
                            <td>{{ $wallet->general_info['avgHashrate']['h3'] }}</td>
                        </tr>
                        <tr>
                            <td style="width: 40%;">Avg Hashrate (6 Hour)</td>
                            <td>{{ $wallet->general_info['avgHashrate']['h6'] }}</td>
                        </tr>
                        <tr>
                            <td style="width: 40%;">Avg Hashrate (12 Hour)</td>
                            <td>{{ $wallet->general_info['avgHashrate']['h12'] }}</td>
                        </tr>
                        <tr>
                            <td style="width: 40%;">Avg Hashrate (24 Hour)</td>
                            <td>{{ $wallet->general_info['avgHashrate']['h24'] }}</td>
                        </tr>
                        <tr>
                            <td style="width: 40%;">&nbsp;</td>
                            <td><a class="btn-success btn-lg btn" target="_blank"
                                   href="{{ route('payments', ['wallet' => $wallet->general_info['account']]) }}">Payments</a>
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
        @foreach($groups as $group1) 
            <li role="presentation" @if(isset($groupMain) &&  $groupMain == $group1->id) class="active" @endif><a
                        href="{{ route('group', ['group' => $group1->id]) }}">{{ $group1->name }}</a></li>
        @endforeach
        <li role="presentation" @if(isset($noGroup) && $noGroup) class="active" @endif><a
                    href="{{ route('group', ['group' => 'nogroup']) }}">No group</a></li>
    </ul>

    <br>
    <br>

    <form>
        <a class="btn-success btn-sm btn<?php if($selected=='all') echo ' disabled';?>"
           href="?">All time</a>
        <a class="btn-success btn-sm btn<?php if($selected=='day') echo ' disabled';?>"
           href="?fromDate={{ $day }}&toDate={{ $today }}">1 Day</a>
        <a class="btn-success btn-sm btn<?php if($selected=='week') echo ' disabled';?>"
           href="?fromDate={{ $week }}&toDate={{ $today }}">1 Week</a>
        <a class="btn-success btn-sm btn<?php if($selected=='month') echo ' disabled';?>"
           href="?fromDate={{ $month }}&toDate={{ $today }}">1 Month</a>

        <input class="datepicker" id="fromDate" value="{{ $fromDate }}" name="fromDate" data-date-format="mm/dd/yyyy"> to
        <input class="datepicker" id="toDate" value="{{ $toDate }}" name="toDate" data-date-format="mm/dd/yyyy">

        <input class="btn-success" type="submit" value="Search">
    </form>

    <br>
    <br>
    <table class="table table-bordered">
        <tr>
            <td>Online time (Uptime)</td>
            <td id="online_time">
                <?php echo $online_time;?>  hours
            </td>
        </tr>
        <tr>
            <td>Offline time (Uptime)</td>
            <td id="offline_time">
                <?php echo $offline_time;?>  hours
            </td>
        </tr>
        <tr>
            <td>Total shares</td>
            <td id="total_shares">
                <?php echo $total_shares;?>
            </td>
        </tr>
        <tr>
            <td>Average Hashrate</td>
            <td id="average_hashrate">
                <?php echo $average_hashrate;?>
            </td>
        </tr>
    </table>

    <br>
    <div>
        <table class="table table-bordered" id="nanopool-table">
            <thead>
            <?php showThead($fromDate, $toDate,$colspan);?>
            </thead>
            <tbody>
            @foreach($generalInfo as $worker) 
                <tr class="{{ $worker['address'] }}">
                    <td>{{ $worker['id'] }}</td>
                    <td>{{ $worker['uid'] }}</td>
                    <td>{{ $worker['hashrate'] }}</td>
                    <td data-order="{{ $worker['lastshare'] }}">{{ date('Y-m-d H:i:s', $worker['lastshare']) }}</td>
                    <td>{{ $worker['rating']??'&nbsp;' }}</td>
                    <?php foreach($worker['data_all']['hashrates'] as $hashrate){?>
                         <td>{{ $hashrate }}</td>
                    <?php }?>
                    <td>{{ $worker['wallet_name'] }}</td>
                    <td>
                        <a href="{{ route('workerHistory', ['id' => $worker['id'], 'time' => $selected, 'wallet' => $worker['address']]) }}"
                           target="_blank">View history</a></td>
                </tr>
            @endforeach
            <?php 
            if(count($generalInfo)){ ?>
            <thead>
                <tr>
                    <td colspan="<?php echo $colspan;?>" style="text-align: right;">
                        <a href="{{ route('multipleWorkerHistory', ['group' => $group, 'time' => $selected]) }}"
                           target="_blank">View history of all machine</a></td>
                </tr>
            </thead>
            <?php 
            }
            ?>
            </tbody>
        </table>

    </div>
    
    <div class="col-sm-6">
        <?php if(!isset($groupMain)){?>
        <label><input type="radio" value="all" name="select" checked="checked"/>All</label>
        <?php } ?>
        <?php if(count($groups)>0&&!isset($groupMain)){?>
        <label><input type="radio" value="group" name="select"/>Group</label>
        <?php 
            }
            ?>
        <?php if(!isset($groupMain)){?>
        <label><input type="radio" value="no_group" name="select"/>No group</label>
        <?php } ?>
        <?php if(!isset($groupMain)){?>
        <label><input type="radio" value="machine" name="select"/>Machine</label>
        <?php } ?>
    </div>
    <div class="col-sm-6">
        <?php if(count($groups)>0&&!isset($groupMain)){?>
        <select id='group' style="display: <?php if(is_numeric($group)) echo 'block'; else echo 'none';?>;">
            <?php 
            foreach ($groups as $gr){?>                
            <option<?php if($group==$gr->id) echo ' selected="selected"';?> value="<?php echo $gr->id;?>"><?php echo $gr->name;?></option>
            <?php 
            }
            ?>
        </select>
        <?php 
            }
            ?>
        <select id='machine' style="display: none;">
            <?php 
            foreach ($generalInfo as $machine){?>
                <option selected="selected" value="<?php echo $machine['id'];?>"><?php echo $machine['id'];?></option>
            <?php 
            }
            ?>
        </select>
    </div>
    <div id="container"></div>
    
    {{--<div>--}}
    {{--<div id="container1" style="height: 400px; min-width: 310px"></div>--}}
    {{--</div>--}}
</div><!-- /.container -->
<?php 
function showThead($fromDate, $toDate,&$colspan) {
    
    ?>
    <th>ID</th>
    <th>UID</th>
    <th>Hashrate</th>
    <th>Lastshare</th>
    <th>Rating</th>
    <?php 
    if(trim($fromDate)==''||trim($toDate)==''){?>
        <th>Worker Average Hashrate</th> 
        <th>Wallet name</th>
        <th>Actions</th>
    <?php 
    $colspan=8;
        return;
    }
    list($m, $d, $y) = explode("/", $fromDate);
    $fromDate = "$y-$m-$d " . date('H:i:00');
    list($m, $d, $y) = explode("/", $toDate);
    $toDate = "$y-$m-$d " . date('H:i:00');
    $fromDate = new \DateTime($fromDate);
    $toDate = new \DateTime($toDate);

    $diff = $toDate->diff($fromDate);
    
    if($diff->d==1){?>
        <th>Worker Average Hashrate for 1 hour</th>
        <th>Worker Average Hashrate for 3 hour</th>
        <th>Worker Average Hashrate for 6 hour</th>
        <th>Worker Average Hashrate for 12 hour</th>
        <th>Worker Average Hashrate for 24 hour</th>
    <?php 
    $colspan=12;
    }
    else if($diff->d==7){?>
        <th>Worker Average Hashrate for day 1</th>
        <th>Worker Average Hashrate for day 2</th>
        <th>Worker Average Hashrate for day 3</th>
        <th>Worker Average Hashrate for day 4</th>
        <th>Worker Average Hashrate for day 5</th>
        <th>Worker Average Hashrate for day 6</th>
        <th>Worker Average Hashrate for day 7</th>
    <?php 
    $colspan=14;
    }
    else if($diff->d==0&&$diff->m>0){?>
        <th>Worker Average Hashrate for week 1</th>
        <th>Worker Average Hashrate for week 2</th>
        <th>Worker Average Hashrate for week 3</th>
        <th>Worker Average Hashrate for week 4</th>
    <?php 
    $colspan=11;
    }
    ?>
    <th>Wallet name</th>
    <th>Actions</th>
    <?php 
    
}

?>
    <script>
        
        var series=[];
        var machines='<?php echo json_encode($machines);?>';
        var datas='<?php echo json_encode($datas);?>';
        machines=$.parseJSON(machines);
        datas=$.parseJSON(datas);
        min_time=datas.min_time;
        datas=datas.hashrates_all;
        for(i=0;i<machines.length;i++){
            hashrates=[];
            for(j=0;j<datas.length;j++){
                if(datas[j].machine_id==machines[i].machine_id){
                    hashrate=parseFloat(datas[j].hashrate);
                    hashrates.push(hashrate);
                }

            }
            series.push({'group_id': machines[i].group_id,'name':machines[i].machine_id,'data':hashrates,'pointStart':min_time,'pointInterval':600000,'dataLength':hashrates.length, 'tooltip': {'valueDecimals': 0,'valueSuffix': ''}});
        }
        
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
            series:series

        });

        $(".highcharts-credits").html('');

        function resetSeries(newSeries){
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
                series:newSeries

            });

            $(".highcharts-credits").html('');
        }
       
        function updateData(selector){
            $.ajax({
                url: "{{ route('updateMachineInfo') }}",
                async: true,
                type: 'POST',
                data: {'wallet':$(selector).attr('class'),'id':$.trim($(selector).find('td').eq(0).html())},
                success: function (data) {                    
                    console.log(data);
                },
                error: function (request, status, error) {
                    console.log(request.responseText);
                }
            });
        }
        
        function updateWalletGeneralInfo(selector){
            $.ajax({
                url: "{{ route('updateWalletGeneralInfo') }}",
                async: true,
                type: 'POST',
                data: {'wallet':$(selector).attr('data')},
                success: function (data) {
                    console.log(data);
                },
                error: function (request, status, error) {
                    console.log(request.responseText);
                }
            });
        }
        
        machines_nogroup='<?php echo json_encode($machines_nogroup);?>';
        machines_nogroup=$.parseJSON(machines_nogroup);
        
    $(function () {
//        trs=$("#nanopool-table tbody tr");
//        for(i=0;i<trs.length-1;i++){
//            updateData($(trs[i]));
//        }
//        
//        
//        divs=$(".tab-pane.fade");
//        for(i=0;i<divs.length;i++){
//            updateWalletGeneralInfo($(divs[i]));
//        }
        

//        $("#machine").multiSelect();

            $("#machine").change(function (){
                newSeries=[];
               machine_id=$(this).val();
               for(i=0,n=series.length;i<n;i++){
                   if(machine_id==series[i]['name']){
                       newSeries.push(series[i]);
                       break;
                   }
               }
               resetSeries(newSeries);
            });
            $("#group").change(function (){
                newSeries=[];
               group_id=$("#group").val();
               for(i=0,n=series.length;i<n;i++){
                   if(group_id==series[i]['group_id']){
                       newSeries.push(series[i]);
                   }
               }
               resetSeries(newSeries);
            });
           $("input[type='radio']").change(function (){
               newSeries=[];
               if($(this).val()=='machine'){               
                   $("#machine").show();
                   $("#group").hide();
                    machine_id=$("#machine").val();
                    for(i=0,n=series.length;i<n;i++){
                       if(machine_id==series[i]['name']){
                           newSeries.push(series[i]);
                           break;
                       }
                   }
                   resetSeries(newSeries);
               }
               else if($(this).val()=='group'){
                   $("#group").show();
                   $("#machine").hide();
                   group_id=$("#group").val();
                   for(i=0,n=series.length;i<n;i++){
                       if(group_id==series[i]['group_id']){
                           newSeries.push(series[i]);
                       }
                   }
                   resetSeries(newSeries);
               }
               else if($(this).val()=='all'){
                   $("#group").hide();
                   $("#machine").hide();
                   resetSeries(series);
               }
               else{
                   $("#group").hide();
                   $("#machine").hide();
                   for(i=0,n=series.length;i<n;i++){
                       if(machines_nogroup.indexOf(series[i]['name'])!=-1){
                           newSeries.push(series[i]);
                       }
                   }
                   resetSeries(newSeries);
               }
           });
        if ($('#nanopool-table').length > 0) {
            $('#nanopool-table').DataTable();
        }

        $('#sandbox-container .input-daterange').datepicker();

        $('#fromDate').datepicker();
        $('#toDate').datepicker();

        
    });

</script>
</body>
</html>

