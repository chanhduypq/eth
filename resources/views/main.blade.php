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
            @foreach($wallets as $key => $wallet)  
                <div class="tab-pane fade @if($key == 0) active in @endif" role="tabpanel" id="{{ $key }}" data="{{ $wallet->address }}"
                     aria-labelledby="{{ $key }}-tab">
                    <table class="table table-bordered">
                        <tbody>
                        <tr class="loading">
                            <td colspan="2" style="margin: 0 auto;text-align: center;">
                                <div style="margin: 0 auto;text-align: center;">
                                    <img src="/images/ui-anim_basic_16x16.gif"/>
                                </div>
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
                    <td colspan="<?php echo $colspan-7;?>">
                        <div class="loading">
                            <img src="/images/ui-anim_basic_16x16.gif"/>
                        </div>
                    </td>
                    <td>{{ $worker['wallet_name'] }}</td>
                    <td>
                        <a href="{{ route('workerHistory', ['id' => $worker['id'], 'time' => $selected, 'wallet' => $worker['address']]) }}"
                           target="_blank">View history</a></td>
                </tr>
            @endforeach
            <?php 
            if(count($generalInfo)){ ?>
                <tr>
                    <td colspan="<?php echo $colspan;?>" style="text-align: right;">
                        <a href="{{ route('multipleWorkerHistory', ['group' => $group, 'time' => $selected]) }}"
                           target="_blank">View history of all machine</a></td>
                </tr>
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
        from_date='{{ $fromDate }}';
        to_date='{{ $toDate }}';
        var number_of_load_machine_complete=0;
        var online_time=0,offline_time=0,total_shares=0,average_hashrate=0;

        function showCommon(online_time,offline_time,total_shares,average_hashrate){
            $("#online_time").html(online_time.toFixed(2)+' hours');
            $("#offline_time").html(offline_time.toFixed(2)+' hours');
            $("#total_shares").html(total_shares);
            $("#average_hashrate").html(average_hashrate.toFixed(2));
        }
        function loadData(selector){
            $.ajax({
                url: "{{ route('getHashratechartForMachine') }}",
                async: true,
                type: 'POST',
                data: {'wallet':$(selector).attr('class'),'id':$.trim($(selector).find('td').eq(0).html()),'fromDate':from_date,'toDate':to_date},
                success: function (data) {                    
                    $(selector).find('.loading').hide();
                    data=$.parseJSON(data);
                    for(key in data){
                        td1=$(selector).find('td').eq(0).html();
                        td2=$(selector).find('td').eq(1).html();
                        td3=$(selector).find('td').eq(2).html();
                        td4=$(selector).find('td').eq(3).html();
                        td5=$(selector).find('td').eq(4).html();
                        td7=$(selector).find('td').eq(6).html();
                        td8=$(selector).find('td').eq(7).html();
                        if(key=='h1'){
                            html="<td>"+td1+"</td>"+"<td>"+td2+"</td>"+"<td>"+td3+"</td>"+"<td>"+td4+"</td>"+"<td>"+td5+"</td>";
                            html+="<td>"+data['h1']+"</td>"+"<td>"+data['h3']+"</td>"+"<td>"+data['h6']+"</td>"+"<td>"+data['h12']+"</td>"+"<td>"+data['h24']+"</td>";
                            html+="<td>"+td7+"</td>";
                            html+="<td>"+td8+"</td>";
                            $(selector).html(html);
                            break;
                        }
                        else if(key=='d1'){
                            html="<td>"+td1+"</td>"+"<td>"+td2+"</td>"+"<td>"+td3+"</td>"+"<td>"+td4+"</td>"+"<td>"+td5+"</td>";
                            html+="<td>"+data['d1']+"</td>"+"<td>"+data['d2']+"</td>"+"<td>"+data['d3']+"</td>"+"<td>"+data['d4']+"</td>"+"<td>"+data['d5']+"</td>"+"<td>"+data['d6']+"</td>"+"<td>"+data['d7']+"</td>";
                            html+="<td>"+td7+"</td>";
                            html+="<td>"+td8+"</td>";
                            $(selector).html(html);
                            break;
                        }
                        else if(key=='w1'){
                            html="<td>"+td1+"</td>"+"<td>"+td2+"</td>"+"<td>"+td3+"</td>"+"<td>"+td4+"</td>"+"<td>"+td5+"</td>";
                            html+="<td>"+data['w1']+"</td>"+"<td>"+data['w2']+"</td>"+"<td>"+data['w3']+"</td>"+"<td>"+data['w4']+"</td>";
                            html+="<td>"+td7+"</td>";
                            html+="<td>"+td8+"</td>";
                            $(selector).html(html);
                            break;
                        }
                        else if(key=='all'){
                            html="<td>"+td1+"</td>"+"<td>"+td2+"</td>"+"<td>"+td3+"</td>"+"<td>"+td4+"</td>"+"<td>"+td5+"</td>";
                            html+="<td>"+data['all']+"</td>";
                            html+="<td>"+td7+"</td>";
                            html+="<td>"+td8+"</td>";
                            $(selector).html(html);
                            break;
                        }
                        
                        
                        
                    }
                    online_time+=parseFloat(data['online_time']);
                    offline_time+=parseFloat(data['offline_time']);
                    total_shares+=parseFloat(data['total_shares']);
                    average_hashrate+=parseFloat(data['average_hashrate']);
                    number_of_load_machine_complete++;
                    if(number_of_load_machine_complete == $("#nanopool-table tbody tr").length-1){
                        average_hashrate=average_hashrate/number_of_load_machine_complete;
                        showCommon(online_time,offline_time,total_shares,average_hashrate);
                    }
                    
                },
                error: function (request, status, error) {
                    console.log(request.responseText);
                }
            });
        }
        
        function loadWallet(selector){
            $.ajax({
                url: "{{ route('getGeneralInfoForwallet') }}",
                async: true,
                type: 'POST',
                data: {'wallet':$(selector).attr('data')},
                success: function (data) {
                    $(selector).find('tbody').eq(0).html(data);
                },
                error: function (request, status, error) {
                    console.log(request.responseText);
                }
            });
        }
        
    $(function () {
        trs=$("#nanopool-table tbody tr");
        for(i=0;i<trs.length-1;i++){
            loadData($(trs[i]));
        }
        
        
        divs=$(".tab-pane.fade");
        for(i=0;i<divs.length;i++){
            loadWallet($(divs[i]));
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

