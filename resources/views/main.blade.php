<?php 
$online_time = 0;
$offline_time = 0;
$total_shares = 0;
$average_hashrate = 0;
foreach($generalInfo as $worker) {
    $online_time+=$worker['data_all']['online_time'];
    $offline_time+=$worker['data_all']['offline_time'];
    $total_shares+=$worker['data_all']['total_shares'];
    $average_hashrate+=$worker['data_all']['average_hashrate'];
}
$online_time = round($online_time, 2);
$offline_time = round($offline_time, 2);
if(count($generalInfo)>0){
    $average_hashrate= round($average_hashrate/count($generalInfo),2);
}

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
    <!--<script src="https://code.highcharts.com/highcharts.js"></script>-->
    

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
            @foreach($wallets as $key => $wallet)  
                <div class="tab-pane fade @if($key == 0) active in @endif" role="tabpanel" id="{{ $key }}" data="{{ $wallet->address }}"
                     aria-labelledby="{{ $key }}-tab">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td>Account</td>
                            <td>{{ $wallet->general_info['account'] }}</td>
                        </tr>
                        <tr>
                            <td>Balance</td>
                            <td>{{ $wallet->general_info['balance'] }}</td>
                        </tr>
                        <tr>
                            <td>Hashrate</td>
                            <td>{{ $wallet->general_info['hashrate'] }}</td>
                        </tr>
                        <tr>
                            <td>Avg Hashrate (1 Hour)</td>
                            <td>{{ $wallet->general_info['avgHashrate']['h1'] }}</td>
                        </tr>
                        <tr>
                            <td>Avg Hashrate (3 Hour)</td>
                            <td>{{ $wallet->general_info['avgHashrate']['h3'] }}</td>
                        </tr>
                        <tr>
                            <td>Avg Hashrate (6 Hour)</td>
                            <td>{{ $wallet->general_info['avgHashrate']['h6'] }}</td>
                        </tr>
                        <tr>
                            <td>Avg Hashrate (12 Hour)</td>
                            <td>{{ $wallet->general_info['avgHashrate']['h12'] }}</td>
                        </tr>
                        <tr>
                            <td>Avg Hashrate (24 Hour)</td>
                            <td>{{ $wallet->general_info['avgHashrate']['h24'] }}</td>
                        </tr>
                        <tr>
                            <td></td>
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
        
    $(function () {
        trs=$("#nanopool-table tbody tr");
        for(i=0;i<trs.length-1;i++){
            updateData($(trs[i]));
        }
        
        
        divs=$(".tab-pane.fade");
        for(i=0;i<divs.length;i++){
            updateWalletGeneralInfo($(divs[i]));
        }
        

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

