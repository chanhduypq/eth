<?php 
/* $online_time = $datas['online_time']; */
$online_time = round(count($datas['hashrates_all']) * 10 / 60, 2);
$offline_time = $datas['offline_time'];
$total_shares = $datas['total_shares'];
$average_hashrate = $datas['average_hashrate'];

$online_time = round($online_time, 2);
$offline_time = round($offline_time, 2);
$average_hashrate = round($average_hashrate, 2);
$showGroup=false;

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
    <script src="/js/jquery.dataTables.min.js?<?php echo substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);?>"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.20.1/vis-timeline-graph2d.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <script src="https://code.highcharts.com/stock/highstock.js"></script>
    <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
    

    <script type="text/javascript" src="/js/jquery-migrate-3.0.0.js"></script>
    <link rel="stylesheet" type="text/css" href="/js/jquery.multiselect.css" />
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
    <script type="text/javascript" src="/js/jquery-ui-1.10.3/ui/jquery-ui.js"></script>
    <script type="text/javascript" src="/js/jquery.multiselect.js"></script>
    <script type="text/javascript" src="/js/prettify.js"></script>
    
    <style>
        .scroll{
            max-height: 300px;
            overflow: scroll;            
        }
        .no_scroll{
            overflow: hidden !important;
            max-height: max-content !important;
        }
        
        label{
            cursor: pointer;
            margin-right: 30px;
        }
        label input{
            width: 30px;
        }
        label:hover{
            background-color: #cc66ff;
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
            </tbody>
        </table>

        <br>
        <br>
    </div>
    <br><br>
    <div>
        <table class="table table-bordered" id="nanopool-table" style="display: none;">
            <thead>
            <th>worker ID</th>
            <th>date</th>
            <th>shares</th>
            <th>hashrate</th>
            </thead>
            <tbody>
                <?php 
                    foreach ($datas['hashrates_all'] as $temp){?>
                    <tr>
                        <td>
                            <?php echo $temp['machine_id'];?>
                        </td>
                        <td>
                            <?php echo $temp['date_d_m_y'];?>
                        </td>
                        <td>
                            <?php echo $temp['shares'];?>
                        </td>
                        <td>
                            <?php echo $temp['hashrate'];?>
                        </td>
                    </tr>
                    <?php 
                    }
                ?>
            </tbody>
        </table>
    </div>
    <br><br>
    <div class="col-sm-12">
        <?php if($group=='all'){?>
            <?php if($group=='all'){?>
            <div class="col-sm-4">
                <label><input type="radio" value="all" name="select" checked="checked"/>All</label>
                <select id='machine' multiple="multiple" style="display: none;">
                    <?php 
                    foreach ($machines as $machine){?>
                        <option selected="selected" value="<?php echo $machine['machine_id'];?>"><?php echo $machine['machine_id'];?></option>
                    <?php 
                    }
                    ?>
                </select>
            </div>
            <?php } ?>
            <?php if(count($groups)>0&&$group=='all'){?>
            <div class="col-sm-4">
                <label><input type="radio" value="group" name="select"/>Group</label>
                <?php if(count($groups)>0&&!isset($groupMain)){?>
                <select id='group' multiple="multiple" style="display: <?php if(is_numeric($group)) echo 'block'; else echo 'none';?>;">
                    <?php 
                    foreach ($groups as $gr){
                        $has=false;
                        foreach ($machines as $machine){
                            if($machine['group_id']==$gr->id){
                                $has=true;
                                break;;
                            }
                        }
                        if($has){
                        ?>    
                            <optgroup label="<?php echo $gr->name;?>">
                                <?php foreach ($machines as $machine){
                                    if($machine['group_id']==$gr->id){?>
                                        <option selected="selected" value="<?php echo $machine['machine_id'];?>"><?php echo $machine['machine_id'];?></option>
                                <?php 
                                    $showGroup=true;
                                    }
                                } ?>
                            </optgroup>
                            <!--<option<?php if($group==$gr->id) echo ' selected="selected"';?> value="<?php echo $gr->id;?>"><?php echo $gr->name;?></option>-->
                    <?php 
                        }
                    }
                    ?>
                </select>
            <?php 
                }
                ?>
            </div>
            <?php 
                }
                ?>
            <?php if($group=='all'){?>
            <div class="col-sm-4">
                <label><input type="radio" value="no_group" name="select"/>No group</label>
            </div>
            <?php } ?>
        <?php } 
        else{?>
            <select id='machine' multiple="multiple" style="display: none;">
                <?php 
                foreach ($machines as $machine){?>
                    <option selected="selected" value="<?php echo $machine['machine_id'];?>"><?php echo $machine['machine_id'];?></option>
                <?php 
                }
                ?>
            </select>
        <?php 
        }
        ?>
        
    </div>
    
    <div id="container"></div>
    

    {{--<div>--}}
    {{--<div id="container1" style="height: 400px; min-width: 310px"></div>--}}
    {{--</div>--}}
</div><!-- /.container -->
<script>
    $(window).load(function() {
          $('#nanopool-table').show();
          $('#nanopool-table').DataTable();
    });
    
    var series=[];
    var machines='<?php echo json_encode($machines);?>';
    var datas='<?php echo json_encode($datas);?>';
    machines=$.parseJSON(machines);
    datas=$.parseJSON(datas);
    min_time=datas.min_time*1000;
    datas=datas.hashrates_all;
    var el1=null;
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
    
    for(i=0;i<machines.length;i++){
//        updateData(machines[i].wallet_address,machines[i].machine_id);
    }
        
    function updateData(wallet,id){
        
        $.ajax({
            url: "{{ route('updateMachineInfo') }}",
            async: true,
            type: 'POST',
            data: {'wallet':wallet,'id':id},
            success: function (data) {     
                console.log(data);

            },
            error: function (request, status, error) {
                console.log(request.responseText);
            }
        });
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
            }, 
//            {
//                type: 'month',
//                count: 6,
//                text: '6m'
//            }, {
//                type: 'year',
//                count: 1,
//                text: '1y'
//            }, 
            {
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
                }, 
//                {
//                    type: 'month',
//                    count: 6,
//                    text: '6m'
//                }, {
//                    type: 'year',
//                    count: 1,
//                    text: '1y'
//                }, 
                {
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
    
    machines_nogroup='<?php echo json_encode($machines_nogroup);?>';
    machines_nogroup=$.parseJSON(machines_nogroup);
    jQuery(function ($){
        <?php if($showGroup==FALSE){?> 
                $("#group").parent().remove();
        <?php }?>
            <?php if($showNoGroup==FALSE){?> 
                $("input[value='no_group']").parent().parent().remove();
        <?php }?>
        
       $("input[type='radio']").change(function (){
           newSeries=[];
           if($(this).val()=='group'){
               $("#group").hide();
               
               $("#machine").hide();
               $("#machine_ms").hide();
               if(el1==null){
                   showGroup();
                   $("#group_ms").show();
               }
               else{
                   $("#group_ms").show();
                   el1.multiselect('refresh');
               }

               if($("#group_ms").find('span').eq(1).html()=='Select options'){
                   $("#group_ms").find('span').eq(1).html('Select machines');
                    resetSeries([]);
                }
           }
           else if($(this).val()=='all'){
               $("#group").hide();
               $("#machine").hide();
               $("#machine_ms").show();
//               resetSeries(series);
                el.multiselect('refresh');
                if($("#machine_ms").find('span').eq(1).html()=='Select options'){
                   $("#machine_ms").find('span').eq(1).html('Select machines');
                    resetSeries([]);
                }
           }
           else{
               $("#group").hide();
               $("#machine").hide();
               $("#machine_ms").hide();
               for(i=0,n=series.length;i<n;i++){
                   if(machines_nogroup.indexOf(series[i]['name'])!=-1){
                       newSeries.push(series[i]);
                   }
               }
               resetSeries(newSeries);
           }
       });
       
       el = $("#machine").multiselect({
                selectedText: function(numChecked, numTotal, checkedItems){
                    newSeries=[];
                    for(i=0;i<checkedItems.length;i++){
                        node=checkedItems[i];
                        for(j=0,n=series.length;j<n;j++){
                           if(node.getAttribute('value')==series[j]['name']){
                               newSeries.push(series[j]);
                           }
                        }
                    }
                    resetSeries(newSeries);
                      return numChecked + ' of ' + numTotal + ' checked';
                   },
		click: function(event, ui){
//			alert(ui.value + ' ' + (ui.checked ? 'checked' : 'unchecked') );
		},
		beforeopen: function(){
//			alert("Select about to be opened...");
		},
		open: function(){
//			alert("Select opened!");
		},
		beforeclose: function(){
//			alert("Select about to be closed...");
		},
		close: function(){
//			alert("Select closed!");
		},
		checkAll: function(){
                    resetSeries(series);
		},
		uncheckAll: function(){
                    newSeries=[];
                    resetSeries(newSeries);
                    $("#machine_ms").find('span').eq(1).html('Select machines');
		},
		optgrouptoggle: function(event, ui){
			var values = $.map(ui.inputs, function(checkbox){
				return checkbox.value;
			}).join(", ");

                        
//			alert("<strong>Checkboxes " + (ui.checked ? "checked" : "unchecked") + ":</strong> " + values);
		}
	});
        
        function showGroup(){
            el1 = $("#group").multiselect({
                    selectedText: function(numChecked, numTotal, checkedItems){
                        newSeries=[];
                        for(i=0;i<checkedItems.length;i++){
                            node=checkedItems[i];
                            for(j=0,n=series.length;j<n;j++){
                               if(node.getAttribute('value')==series[j]['name']){
                                   newSeries.push(series[j]);
                               }
                            }
                        }
                        resetSeries(newSeries);
                          return numChecked + ' of ' + numTotal + ' checked';
                       },
                    click: function(event, ui){
    //			alert(ui.value + ' ' + (ui.checked ? 'checked' : 'unchecked') );
                    },
                    beforeopen: function(){
    //			alert("Select about to be opened...");
                    },
                    open: function(){
    //			alert("Select opened!");
                    },
                    beforeclose: function(){
    //			alert("Select about to be closed...");
                    },
                    close: function(){
    //			alert("Select closed!");
                    },
                    checkAll: function(a){
                        newSeries=[];
                        node=a.target;
                        options=$(node).find('option');
                        arr=[];
                        for(i=0;i<options.length;i++){
                            arr.push($(options[i]).val());
                        }
                        for(j=0,n=series.length;j<n;j++){
                           if(arr.indexOf(series[j]['name'])!=-1){
                               newSeries.push(series[j]);
                           }
                        }

                        resetSeries(newSeries);
                    },
                    uncheckAll: function(){
                        newSeries=[];
                        resetSeries(newSeries);
                        $("#group_ms").find('span').eq(1).html('Select machines');
                    },
                    optgrouptoggle: function(event, ui){
                            var values = $.map(ui.inputs, function(checkbox){
                                    return checkbox.value;
                            });


    //			alert("<strong>Checkboxes " + (ui.checked ? "checked" : "unchecked") + ":</strong> " + values);
                    }
            });
        }
        
        
    });
    
</script>
</body>
</html>

