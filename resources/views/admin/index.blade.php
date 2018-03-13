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
    <br>
    (Go to <a href="{{ route('boot') }}">main
        page</a>)
<br><br>
    <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li role="presentation" class="active"><a href="#wallets" role="tab" id="wallets-tab" data-toggle="tab"
                                                aria-controls="wallets" aria-expanded="true">Wallets</a></li>
            <li role="presentation" class=""><a href="#main" id="main-tab" role="tab" data-toggle="tab"
                                                aria-controls="main" aria-expanded="false">Machines</a></li>
            <li role="presentation" class=""><a href="#groups" role="tab" id="groups-tab" data-toggle="tab"
                                                      aria-controls="groups" aria-expanded="true">Groups</a></li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade active in" role="tabpanel" id="wallets" aria-labelledby="wallets-tab">

                @if($wallets)
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Wallet Hash</th>
                            <th>Wallet Name</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($wallets as $wallet)
                            <tr data-wallet-address="{{ $wallet->address }}">
                                <td>{{ $wallet->address }}</td>
                                <td>{{ $wallet->name }}</td>
                                <td>
                                    <button class="btn-warning btn-sm editWallet" data-toggle="modal" data-target="#myWalletEdit-{{ $wallet->id }}">Edit</button>
                                    <button class="btn-danger btn-sm deleteWallet">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif

                    @if(isset($wallets))
                        @foreach($wallets as $wallet)
                            <div class="modal fade" id="myWalletEdit-{{ $wallet->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <form method="post" action="{{ route('wallet.edit', ['wallet' => $wallet->address]) }}">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Edit wallet</h4>
                                            </div>
                                            <div class="modal-body">
                                                Name: <input type="text" name="name" value="{{ $wallet->name }}">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif

                <br>
                <button type="button" class="btn btn-primary btn-success" data-toggle="modal" data-target="#myModalWallet">
                    Add wallet
                </button>

                <!-- Modal -->
                <div class="modal fade" id="myModalWallet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <form method="post" action="{{ route('addwallet') }}">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Add Wallet</h4>
                                </div>
                                <div class="modal-body">
                                    Address: <input type="text" name="address"><br>
                                    Name: <input type="text" name="name">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" role="tabpanel" id="main" aria-labelledby="main-tab">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Machine ID</th>
                        <th>Group</th>
                        <th>Address</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($workersList as $worker)
                        <tr>
                            <td>{{ $worker['id'] }}</td>
                            <td>
                                <select class="changeGroup" data-machine-id="{{ $worker['id'] }}" data-wallet-address="{{ $worker['address'] }}">
                                    <option value="">No group</option>
                                    @foreach($groups as $group)
                                    <option value="{{ $group->id }}" @if(isset($relationsG2m[$worker['id']]) && $relationsG2m[$worker['id']] == $group->id) selected @endif>{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{{ $worker['address'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" role="tabpanel" id="groups" aria-labelledby="groups-tab">

            @if($groups)
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Group ID</th>
                        <th>Group Name</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($groups as $group)
                        <tr data-group-id="{{ $group->id }}">
                            <td>{{ $group->id }}</td>
                            <td>{{ $group->name }}</td>
                            <td>
                                <button class="btn-warning btn-sm editGroup" data-toggle="modal" data-target="#myModalEdit-{{ $group->id }}">Edit</button>
                                <button class="btn-danger btn-sm deleteGroup">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            <br>
            <button type="button" class="btn btn-primary btn-success" data-toggle="modal" data-target="#myModal">
                Add group
            </button>



            @if(isset($groups))
                @foreach($groups as $group)
                    <div class="modal fade" id="myModalEdit-{{ $group->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <form method="post" action="{{ route('group.edit', ['group' => $group->id]) }}">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Edit group</h4>
                                    </div>
                                    <div class="modal-body">
                                        Name: <input type="text" name="name" value="{{ $group->name }}">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <form method="post" action="{{ route('addgroup') }}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add group</h4>
                            </div>
                            <div class="modal-body">
                                Name: <input type="text" name="name">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){

        $('.deleteWallet').on('click', function(){

            var walletId = $(this).closest('tr').data('wallet-address');

            if(confirm('Delete wallet ?')) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('deleteWallet') }}",
                    data: { 'walletId' : walletId },
                    success: function(data) {
                        location.reload();
                    },
                    dataType: 'json'
                });
            }

        });

        $('.deleteGroup').on('click', function(){

            var groupId = $(this).closest('tr').data('group-id');

            if(confirm('Delete wallet ?')) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('deleteGroup') }}",
                    data: { 'groupId' : groupId },
                    success: function(data) {
                        location.reload();
                    },
                    dataType: 'json'
                });
            }

        });

        $('.changeGroup').on('change', function(){
            var machineId = $(this).data('machine-id'),
                groupId = $(this).val(),
                address = $(this).data('wallet-address');

            $.ajax({
                type: "POST",
                url: "{{ route('saveGroupRelation') }}",
                data: { 'machineId' : machineId, 'groupId' : groupId, 'address' : address },
                success: function(data) {
                    console.log(data);
                },
                dataType: 'json'
            });
        });
    });
</script>
</body>
</html>

