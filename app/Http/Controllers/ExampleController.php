<?php  namespace App\Http\Controllers;  use App\Helpers\NanopoolAPI; use App\Helpers\NanoSatisticProcessing; use Illuminate\Http\Request;   class ExampleController extends Controller {     public $via_proxy = true;    public $proxyAuth = 'galvin24x7:egor99';    private $wallets;    private $groups;    private $groups2machines;    private $datas;    private $machinesHasGroup = array();    private $machinesHasNoGroup = array();        /**     * @var NanopoolAPI     */    protected $nanopool;    public function __construct() {        set_time_limit(0);        ini_set('max_execution_time', 3600);                                $this->nanopool = new NanopoolAPI(env('WALLET_NUMBER'));        $this->wallets = app('db')->select("select * from `wallets`");        $apiNanopool = new NanopoolAPI();                foreach ($this->wallets as $wallet) {            $temps = app('db')->select("select * from `groups2machine` where wallet_address='".$wallet->address."'");            if(count($temps)==0){                $apiNanopool->setWalletAddress($wallet->address);                $data = $apiNanopool->getListWorkders($assoc = false);                if ($data->status == true) {                    $temp = $data->data;                    foreach ($temp as $t) {                        app('db')->insert(sprintf('insert into `groups2machine` (`group_id`, `machine_id`, `wallet_address`) VALUES (NULL, "%s", "%s")', $t->id, $wallet->address));                    }                }            }                    }                        $this->groups = app('db')->select("select * from `groups`");        $this->groups2machines = app('db')->select("select * from `groups2machine`");        $this->datas = app('db')->select("select * from `data`");        foreach ($this->groups2machines as $temp){            if($temp->group_id==''){                $this->machinesHasNoGroup[$temp->machine_id] = $temp->wallet_address;            }            else{                $this->machinesHasGroup[$temp->machine_id] = $temp->wallet_address;            }                    }            }        public function getGeneralInfoForwallet(Request $request){        $wallet = $request->get('wallet');        $apiNanopool = new NanopoolAPI();        $apiNanopool->setWalletAddress($wallet);        $generalInfo = $apiNanopool->getGeneralInfo();        return view('general_info_wallet', [            'generalInfo' => $generalInfo,        ]);    }    /**     * @return array     */    protected function getGeneralInformation(): array {        $generalInfo = $this->nanopool->getGeneralInfo();        $historyHashRate = $this->nanopool->getHistoryHashrate();        if (!$generalInfo['status'] || !$historyHashRate['status']) {            return $this->getGeneralInformation();        }        return [$generalInfo, $historyHashRate];    }    public function getHashratechartForMachine(Request $request){        $wallet = $request->get('wallet');        $id = $request->get('id');        $fromDate = $request->get('fromDate');        $toDate = $request->get('toDate');                $data = $this->curl_getcontent('https://api.nanopool.org/v1/eth/hashratechart/' . $wallet . '/' . $id);        $data = json_decode($data, true);        if($data['status']==true){            $data = $data['data'];        }        else{            $data = array();        }                $fromDateRoot = $fromDate;        $toDateRoot = $toDate;        if ($fromDate && $toDate) {            list($m, $d, $y) = explode("/", $fromDate);            $fromDate = ("$y-$m-$d 00:00:00");            list($m, $d, $y) = explode("/", $toDate);            $toDate = ("$y-$m-$d 23:59:59");        }                $worker = array();        $apiNanopool=new NanopoolAPI();        foreach ($this->wallets as $name) {            if ($name->address == $wallet) {                $apiNanopool->setWalletAddress($wallet);                $generalInfo = $apiNanopool->getGeneralInfo();                if($generalInfo['status']==true){                    foreach ($generalInfo['data']['workers'] as $w) {                        if ($w['id'] == $id) {                            $worker = $w;                            break;                        }                    }                }                            }            break;        }        if ($fromDateRoot && $toDateRoot) {            if (!isset($diff)) {                $fromDate1 = new \DateTime($fromDate);                $toDate1 = new \DateTime($toDate);                $diff = $toDate1->diff($fromDate1);            }            if ($diff->d == 7) {                unset($worker['h1']);                list($m, $d, $y) = explode("/", $fromDateRoot);                $fromDate = "$y-$m-$d 00:00:00";                $worker['d1'] = 0;                $toDate = date('Y-m-d 23:59:59', strtotime($fromDate . ' +1 day'));                $count = 0;                foreach ($data as $temp) {                    $date = date("Y-m-d", $temp['date']);                    $date = strtotime($date);                    if ($date >= strtotime($fromDate) && $date <= strtotime($toDate)) {                        $worker['d1'] += $temp['hashrate'];                        $count++;                    }                }                if ($count != 0) {                    $worker['d1'] = round(((float) $worker['d1']) / $count, 1);                }                for ($i = 2; $i <= 7; $i++) {                    $count = 0;                    $worker["d$i"] = 0;                    $fromDate = date('Y-m-d 00:00:00', strtotime($fromDate . ' +1 day'));                    $toDate = date('Y-m-d 23:59:59', strtotime($fromDate . ' +1 day'));                    foreach ($data as $temp) {                        $date = date("Y-m-d", $temp['date']);                        $date = strtotime($date);                        if ($date >= strtotime($fromDate) && $date <= strtotime($toDate)) {                            $worker["d$i"] += $temp['hashrate'];                            $count++;                        }                    }                    if ($count != 0) {                        $worker["d$i"] = round(((float) $worker["d$i"]) / $count, 1);                    }                }            } else if ($diff->d == 0 && $diff->m > 0) {                unset($worker['h1']);                list($m, $d, $y) = explode("/", $fromDateRoot);                $fromDate = "$y-$m-$d 00:00:00";                $worker['w1'] = 0;                $toDate = date('Y-m-d 23:59:59', strtotime($fromDate . ' +7 day'));                $count = 0;                foreach ($data as $temp) {                    $date = date("Y-m-d", $temp['date']);                    $date = strtotime($date);                    if ($date >= strtotime($fromDate) && $date <= strtotime($toDate)) {                        $worker['w1'] += $temp['hashrate'];                        $count++;                    }                }                if ($count != 0) {                    $worker['w1'] = round(((float) $worker['w1']) / $count, 1);                }                for ($i = 2; $i <= 4; $i++) {                    $count = 0;                    $worker["w$i"] = 0;                    $fromDate = date('Y-m-d 00:00:00', strtotime($fromDate . ' +7 day'));                    $toDate = date('Y-m-d 23:59:59', strtotime($fromDate . ' +7 day'));                    foreach ($data as $temp) {                        $date = date("Y-m-d", $temp['date']);                        $date = strtotime($date);                        if ($date >= strtotime($fromDate) && $date <= strtotime($toDate)) {                            $worker["w$i"] += $temp['hashrate'];                            $count++;                        }                    }                    if ($count != 0) {                        $worker["w$i"] = round(((float) $worker["w$i"]) / $count, 1);                    }                }            }        } else {            $count = 0;            $worker["all"] = 0;            foreach ($data as $temp) {                $worker["all"] += $temp['hashrate'];                $count++;            }            if ($count != 0) {                $worker["all"] = round(((float) $worker["all"]) / $count, 1);            }            if ($worker["all"] == 0) {                if (isset($worker['h1']) && isset($worker['h3']) && isset($worker['h6']) && isset($worker['h12']) && isset($worker['h24'])) {                    $sum = ($worker['h1'] + $worker['h3'] + $worker['h6'] + $worker['h12'] + $worker['h24']);                    $worker["all"] = round(((float) $sum) / 5, 1);                }            }            unset($worker['h1']);        }                $anyActivity = [];        $zeroActivity = [];        $average_hashrate = 0;        $avgHashRate = [];                if ($fromDateRoot && $toDateRoot) {            list($m, $d, $y) = explode("/", $fromDateRoot);            $fromDate = "$y-$m-$d 00:00:00";            list($m, $d, $y) = explode("/", $toDateRoot);            $toDate = "$y-$m-$d 23:59:59";        }                $data_new=array();        foreach ($data as &$datum) {            $avgHashRate[] = $datum['hashrate'];            if ($datum['shares'] === 0) {                $zeroActivity[] = $datum;            } else {                $anyActivity[] = $datum;            }                        $datum['date_string']=date("Y-m-d H:i:s",$datum['date']);            if(!$fromDateRoot||!$toDateRoot||($datum['date']<= strtotime($toDate)&&$datum['date']>= strtotime($fromDate))){                $data_new[]=$datum;            }                    }        if (!empty($avgHashRate)) {            $average_hashrate = round(array_sum($avgHashRate) / sizeof($avgHashRate), 2);        }        $anyActivityShares = $this->calculateInform($anyActivity);        $zeroActivityShares = $this->calculateInform($zeroActivity);                $online_time=number_format($anyActivityShares['actvTime'], 2);        $offline_time=number_format($zeroActivityShares['actvTime'], 2);        $total_shares=$anyActivityShares['shares'];                $worker['online_time']=$online_time;        $worker['offline_time']=$offline_time;        $worker['total_shares']=$total_shares;        $worker['average_hashrate']=$average_hashrate;        $worker['data'] =$data_new;        $worker['machine_id']=$id;        echo json_encode($worker);        exit;    }    public function getHashrateHistoryForMachine(Request $request){        $id = $request->get('id');        $time = $request->get('time');        $wallet = $request->get('wallet');        $apiNanopool = new NanopoolAPI();        $apiNanopool->setWalletAddress($wallet);        $graphFowWorker = $apiNanopool->retryConnect('getWorkerGraphHistory', [$id]);        if ($graphFowWorker['status']) {            foreach ($graphFowWorker['data'] as $item) {                                try {                    app('db')->insert("insert into `data` (`date`, `shares`, `hashrate`, `machine_id`) VALUES ('" . date('Y-m-d H:i:s',                             $item['date']) . "', '" . $item['shares'] . "', '" .                             $item['hashrate'] . "', '" . $id . "')");                } catch (\Exception $exception) {                }            }        }        $data = $this->getDataByTime($time, $id);                $anyActivity = [];        $zeroActivity = [];        $average_hashrate = 0;        $avgHashRate = [];        foreach ($data as $datum) {            $avgHashRate[] = $datum->hashrate;            if ($datum->shares === 0) {                $zeroActivity[] = $datum;            } else {                $anyActivity[] = $datum;            }        }        if (!empty($avgHashRate)) {            $average_hashrate = round(array_sum($avgHashRate) / sizeof($avgHashRate), 2);        }        $anyActivityShares = $this->calculateInform($anyActivity);        $zeroActivityShares = $this->calculateInform($zeroActivity);                $online_time=number_format($anyActivityShares['actvTime'], 2);        $offline_time=number_format($zeroActivityShares['actvTime'], 2);        $total_shares=$anyActivityShares['shares'];        echo json_encode(array(            'data'=>$data,            'online_time'=>$online_time,            'offline_time'=>$offline_time,            'total_shares'=>$total_shares,            'average_hashrate'=>$average_hashrate,        ));        exit;    }    protected function getAllWalletsMachines($fromDate, $toDate,$walletsInformation) {        $fromDateRoot = $fromDate;        $toDateRoot = $toDate;        if ($fromDate && $toDate) {            list($m, $d, $y) = explode("/", $fromDate);            $fromDate = ("$y-$m-$d 00:00:00");            list($m, $d, $y) = explode("/", $toDate);            $toDate = ("$y-$m-$d 23:59:59");        }        $apiNanopool = new NanopoolAPI();        $allMachines = [];        foreach ($this->wallets as $wallet) {            $apiNanopool->setWalletAddress($wallet->address);            $generalInfo=$apiNanopool->getGeneralInfo();             if($generalInfo['status']==true&&isset($generalInfo['data']['workers'])){                foreach ($generalInfo['data']['workers'] as &$worker) {                    $worker['address'] = $wallet->address;                    $worker['wallet_name'] = $wallet->name;                }            }            else{                $generalInfo['data']['workers']=array();            }                        $allMachines = array_merge($allMachines, $generalInfo['data']['workers']);        }        return $allMachines;    }    protected function getCommonWalletsInfo() {        $allInfoMachines = [];        foreach ($this->wallets as $wallet) {            $allInfoMachines[$wallet->address] = '';        }        return $allInfoMachines;    }    /**     * @return \Illuminate\View\View     */    public function boot(Request $request) {                $fromDate = $request->get('fromDate');        $toDate = $request->get('toDate');        $walletsInformation = $this->getCommonWalletsInfo();                $allMachines = $this->getAllWalletsMachines($fromDate, $toDate,$walletsInformation);                if(count($walletsInformation)){            $addresses= array_keys($walletsInformation);        }        else{            $addresses = array();        }        if ($fromDate) {            list($m, $d, $y) = explode("/", $fromDate);            $fromDate1 = "$y-$m-$d " . date('H:i:00');        } else {            $fromDate1 = null;        }        if ($toDate) {            list($m, $d, $y) = explode("/", $toDate);            $toDate1 = "$y-$m-$d " . date('H:i:00');        } else {            $toDate1 = null;        }        $day = date('m/d/Y', strtotime('-1 day'));        $week = date('m/d/Y', strtotime('-1 week'));        $month = date('m/d/Y', strtotime('-1 month'));        $today = date('m/d/Y', time());                if ($fromDate && $toDate) {            list($m, $d, $y) = explode("/", $fromDate);            $fromDate1 = "$y-$m-$d " . date('H:i:00');            list($m, $d, $y) = explode("/", $toDate);            $toDate1 = "$y-$m-$d " . date('H:i:00');            $fromDate1 = new \DateTime($fromDate1);            $toDate1 = new \DateTime($toDate1);            $diff = $toDate1->diff($fromDate1);            if ($diff->d == 1) {                $selected = 'day';            } else if ($diff->d == 7) {                $selected = 'week';            } else if ($diff->d == 0 && $diff->m > 0) {                $selected = 'month';            }        } else {            $selected = 'all';        }        return view('main', [            'generalInfo' => $allMachines,            'groups' => $this->groups,            'wallets' => $this->wallets,            'fromDate' => $fromDate,            'toDate' => $toDate,            'day' => $day,            'week' => $week,            'month' => $month,            'today' => $today,            'selected' => $selected,            'group'=>'all',        ]);    }    public function groupEdit(string $group, Request $request) {        $name = $request->get('name');        if (!$name) {            throw new \Exception('No data');        }        app('db')->update(sprintf('update groups set name = "%s" where id = %d', $name, $group));        return redirect(route('admin.index'));    }    public function walletEdit(string $wallet, Request $request) {        $name = $request->get('name');        if (!$name) {            throw new \Exception('No data');        }        app('db')->update(sprintf('update wallets set name = "%s" where address = "%s"', $name, $wallet));        return redirect(route('admin.index'));    }    public function payments(string $wallet) {        $apiNanopool = new NanopoolAPI();        $apiNanopool->setWalletAddress($wallet);        $payments = $apiNanopool->getAddressPayments();        $totalSumPayment = $this->getPaymentStatistic($payments['data']);        return view('payments', [            'payments' => $payments['data'],            'address' => $wallet,            'time' => 'all',            'totalSumPayment' => $totalSumPayment,        ]);    }    public function paymentHistory(string $wallet, string $time) {        $apiNanopool = new NanopoolAPI();        $apiNanopool->setWalletAddress($wallet);        $payments = $apiNanopool->getAddressPayments();        $paymentsArr = [];        if ($time == 'day') {            $from = date('Y-m-d 00:00:00', strtotime('-1 day'));        }        if ($time == 'week') {            $from = date('Y-m-d 00:00:00', strtotime('-1 week'));        }        if ($time == 'month') {            $from = date('Y-m-d 00:00:00', strtotime('-1 month'));        }        $to = date('Y-m-d 23:59:59', strtotime('today'));        foreach ($payments['data'] as $payment) {            $date = date('Y-m-d', $payment['date']);            if ($date > $from && $date < $to) {                $paymentsArr[] = $payment;            }        }        $totalSumPayment = $this->getPaymentStatistic($paymentsArr);        return view('payments', [            'payments' => $paymentsArr,            'address' => $wallet,            'time' => $time,            'totalSumPayment' => $totalSumPayment,        ]);    }    protected function getPaymentStatistic(array $data) {        $amounts = [];        foreach ($data as $datum) {            $amounts[] = $datum['amount'];        }        return round(array_sum($amounts), 2);    }    public function group(string $group, Request $request) {        $fromDate = $request->get('fromDate');        $toDate = $request->get('toDate');                $walletsInformation = $this->getCommonWalletsInfo();        $allMachines = $this->getAllWalletsMachines($fromDate, $toDate,$walletsInformation);        $noGroup = false;                if(count($walletsInformation)){            $addresses= array_keys($walletsInformation);        }        else{            $addresses = array();        }        $activeGroups = [];        if ($fromDate) {            list($m, $d, $y) = explode("/", $fromDate);            $fromDate1 = "$y-$m-$d " . date('H:i:00');        } else {            $fromDate1 = null;        }        if ($toDate) {            list($m, $d, $y) = explode("/", $toDate);            $toDate1 = "$y-$m-$d " . date('H:i:00');        } else {            $toDate1 = null;        }        $day = date('m/d/Y', strtotime('-1 day'));        $week = date('m/d/Y', strtotime('-1 week'));        $month = date('m/d/Y', strtotime('-1 month'));        $today = date('m/d/Y', time());        if ($group == 'nogroup') {            $groups2Machines = app('db')->select("select * from `groups` left join `groups2machine` on `groups`.id = groups2machine.group_id");        } else {            $groups2Machines = app('db')->select("select * from `groups` left join `groups2machine` on `groups`.id = groups2machine.group_id where `groups`.id = '" . $group . "'");        }        foreach ($groups2Machines as $group1) {            $activeGroups[] = $group1->machine_id;        }        if ($group == 'nogroup') {            foreach ($allMachines as $key => $worker) {                if (in_array($worker['id'], $activeGroups)) {                    unset($allMachines[$key]);                }            }            $noGroup = true;        } else {            foreach ($allMachines as $key => $worker) {                if (!in_array($worker['id'], $activeGroups)) {                    unset($allMachines[$key]);                }            }        }        if ($fromDate && $toDate) {            list($m, $d, $y) = explode("/", $fromDate);            $fromDate1 = "$y-$m-$d " . date('H:i:00');            list($m, $d, $y) = explode("/", $toDate);            $toDate1 = "$y-$m-$d " . date('H:i:00');            $fromDate1 = new \DateTime($fromDate1);            $toDate1 = new \DateTime($toDate1);            $diff = $toDate1->diff($fromDate1);            if ($diff->d == 1) {                $selected = 'day';            } else if ($diff->d == 7) {                $selected = 'week';            } else if ($diff->d == 0 && $diff->m > 0) {                $selected = 'month';            }        } else {            $selected = 'all';        }        return view('main', [            'generalInfo' => $allMachines,            'groups' => $this->groups,            'groupMain' => $group,            'noGroup' => $noGroup,            'wallets' => $this->wallets,            'day' => $day,            'week' => $week,            'month' => $month,            'today' => $today,            'fromDate' => $fromDate,            'toDate' => $toDate,            'selected' => $selected,            'group'=>$group,        ]);    }    /**     * @param string $id     * @param string $time     * @return \Illuminate\View\View     */    public function workerHistory(string $wallet, string $id, string $time) {        if ($time == 'all') {            $from_date = $to_date = '';        } else if ($time == 'day') {            $to_date = date('m/d/Y');            $time1 = strtotime(date("Y-m-d") . ' -0 years -0 months -1 days');            $from_date = date("m/d/Y", $time1);        } else if ($time == 'week') {            $to_date = date('m/d/Y');            $time1 = strtotime(date("Y-m-d") . ' -0 years -0 months -7 days');            $from_date = date("m/d/Y", $time1);        } else if ($time == 'month') {            $to_date = date('m/d/Y');            $time1 = strtotime(date("Y-m-d") . ' -0 years -1 months -0 days');            $from_date = date("m/d/Y", $time1);        }                        return view('worker_history', [            'workerId' => $id,            'time' => $time,            'address' => $wallet,            'from_date' => $from_date,            'to_date' => $to_date,        ]);    }        public function multipleWorkerHistory(string $group, string $time) {        $machines = array();        if ($group == 'all') {            $machines = $this->groups2machines;        } else {            if (is_numeric($group)) {                foreach ($this->groups2machines as $machine) {                    if ($machine->group_id == $group) {                        $machines[] = $machine;                    }                                    }            }            else{                foreach ($this->groups2machines as $machine) {                    if ($machine->group_id == '') {                        $machines[] = $machine;                    }                                    }            }        }                if ($time == 'all') {            $from_date = $to_date = '';        } else if ($time == 'day') {            $to_date = date('m/d/Y');            $time1 = strtotime(date("Y-m-d") . ' -0 years -0 months -1 days');            $from_date = date("m/d/Y", $time1);        } else if ($time == 'week') {            $to_date = date('m/d/Y');            $time1 = strtotime(date("Y-m-d") . ' -0 years -0 months -7 days');            $from_date = date("m/d/Y", $time1);        } else if ($time == 'month') {            $to_date = date('m/d/Y');            $time1 = strtotime(date("Y-m-d") . ' -0 years -1 months -0 days');            $from_date = date("m/d/Y", $time1);        }        return view('multiple_worker_history', [            'time' => $time,            'group' => $group,            'machines' => json_encode($machines),            'from_date' => $from_date,            'to_date' => $to_date,        ]);    }        public function getHashrateHistoryForMultiMachine(Request $request){        $group = $request->get('group');        $time = $request->get('time');                $graphFowWorker = array();        $groupIds = array();        $anyActivity = [];        $zeroActivity = [];        $average_hashrate = 0;        $avgHashRate = [];        if ($group == 'all') {                        $machines= $this->groups2machines;        } else if (is_numeric($group)) {            $machines = array();            foreach ($this->groups2machines as $groups2machines) {                if ($groups2machines->group_id == $group) {                    $machines[] = $groups2machines;                }            }        } else {            $machines= $this->machinesHasNoGroup;        }        $data=array();        $machineIds=array();        foreach ($machines as $machine) {            $apiNanopool = new NanopoolAPI();            $machineIds[] = $machine->machine_id ;            $apiNanopool->setWalletAddress($machine->wallet_address);            $temp = $apiNanopool->retryConnect('getWorkerGraphHistory', [$machine->machine_id]);            if ($temp['status']) {                foreach ($temp['data'] as $item) {                    try {                        app('db')->insert("insert into `data` (`date`, `shares`, `hashrate`, `machine_id`) VALUES ('" . date('Y-m-d H:i:s', $item['date']) . "', '" . $item['shares'] . "', '" . $item['hashrate'] . "', '" . $id . "')");                    } catch (\Exception $exception) {                    }                }            }                        $graphFowWorker = array_merge($graphFowWorker, $temp);            $data[$machine->machine_id] = $this->getDataByTime($time, $machine->machine_id);            foreach ($data[$machine->machine_id] as $datum) {                $avgHashRate[] = $datum->hashrate;                if ($datum->shares === 0) {                    $zeroActivity[] = $datum;                } else {                    $anyActivity[] = $datum;                }            }        }        if (!empty($avgHashRate)) {            $average_hashrate = round(array_sum($avgHashRate) / sizeof($avgHashRate), 2);        }        $anyActivityShares = $this->calculateInform($anyActivity);        $zeroActivityShares = $this->calculateInform($zeroActivity);                $online_time=number_format($anyActivityShares['actvTime'], 2);        $offline_time=number_format($zeroActivityShares['actvTime'], 2);        $total_shares=$anyActivityShares['shares'];                echo json_encode(array(            'data'=>$data,            'online_time'=>$online_time,            'offline_time'=>$offline_time,            'total_shares'=>$total_shares,            'average_hashrate'=>$average_hashrate,        ));        exit;                    }         protected function getDataByTime1(string $time, $machineIds) {        if ($time == 'day') {            $from = date('Y-m-d 00:00:00', strtotime('-1 day'));        }        if ($time == 'week') {            $from = date('Y-m-d 00:00:00', strtotime('-1 week'));        }        if ($time == 'month') {            $from = date('Y-m-d 00:00:00', strtotime('-1 month'));        }        $to = date('Y-m-d 23:59:59', strtotime('today'));        $all=array();        $counts=array(0);        foreach ($machineIds as $machineId){            if ($time == 'all') {                $data = app('db')->select("select * from `data` where machine_id = '{$machineId}' order by `date` asc");            } else {                $data = app('db')->select("select * from `data` where `date` BETWEEN '" . $from . "' AND '" . $to . "' AND machine_id = '{$machineId}' order by `date` asc");            }            $all[$machineId]=$data;            $counts[]=count($data);        }        $result=array();        for($i=0;$i<max($counts);$i++){            foreach ($all as $key=>$value){                if(isset($value[$i])){                    $result[]=$value[$i];                }                            }        }        return $result;    }    protected function getDataByTime(string $time, string $machineId=null) {        if ($time == 'day') {            $from = date('Y-m-d 00:00:00', strtotime('-1 day'));        }        if ($time == 'week') {            $from = date('Y-m-d 00:00:00', strtotime('-1 week'));        }        if ($time == 'month') {            $from = date('Y-m-d 00:00:00', strtotime('-1 month'));        }        $to = date('Y-m-d 23:59:59', strtotime('today'));        if (is_numeric($machineId)) {            if ($time == 'all') {                $data = app('db')->select("select * from `data` where machine_id = '{$machineId}' order by `date` asc");            } else {                $data = app('db')->select("select * from `data` where `date` BETWEEN '" . $from . "' AND '" . $to . "' AND machine_id = '{$machineId}' order by `date` asc");            }        } else {            if ($time == 'all') {                $data = app('db')->select("select * from `data` order by `date` asc");            } else {                $data = app('db')->select("select * from `data` where `date` BETWEEN '" . $from . "' AND '" . $to . "' order by `date` asc");            }        }        return $data;    }    public function calculateInform(array $data) {        $shares = 0;        foreach ($data as $datum) {            if(is_array($datum)){                $shares += $datum['shares'];            }            else{                $shares += $datum->shares;            }                    }        return [            'shares' => $shares,            'actvTime' => sizeof($data) * 10 / 60,        ];    }    /**     * @param array $graphData     * @return string     */    protected function prepareGraph(array $graphData): string {        $graphDataViewOpen = [];        $data = [];        if (!$graphData) {            return '';        }        foreach ($graphData['data'] as $item) {            if(!is_numeric($item['date'])){                $date = strtotime($item['date']);            }            else{                $date = $item['date'];            }            $key = date('Y', $date) . date('d', $date) . date('m', $date);            if (isset($graphDataViewOpen[$key]['hashrate'])) {                $graphDataViewOpen[$key]['hashrate'] += $item['hashrate'];            } else {                $graphDataViewOpen[$key]['hashrate'] = $item['hashrate'];            }            if (isset($graphDataViewOpen[$key]['count'])) {                $graphDataViewOpen[$key]['count'] += 1;            } else {                $graphDataViewOpen[$key]['count'] = 1;            }        }        foreach ($graphData['data'] as $item) {            if(!is_numeric($item['date'])){                $date = strtotime($item['date']);            }            else{                $date = $item['date'];            }            $key = date('Y', $date) . date('d', $date) . date('m', $date);            $avgHashrate = $graphDataViewOpen[$key]['hashrate'] / $graphDataViewOpen[$key]['count'];            $data[$key]['hashrate'] = round($avgHashrate);            $data[$key]['date'] = $date;        }        $graphDataViewOpen = [];        foreach ($data as $item) {            $date = $item['date'];            $graphDataViewOpen[$date] = '[Date.UTC(' . date('Y', $date) . ',' . (date('m', $date) - 1) . ', ' . date('d', $date) . '), ' . (isset($item['hashrate']) ? $item['hashrate'] : 0) . ']';        }        ksort($graphDataViewOpen);        return implode(', ', $graphDataViewOpen);    }    public function addGroup(Request $request) {        if (empty($request->get('name'))) {            throw new \Exception('Empty data');        }        app('db')->insert(sprintf('insert into `groups` (`name`) VALUES ("%s")', $request->get('name')));        return redirect(route('admin.index'));    }    public function addWallet(Request $request) {        if (empty($request->get('address'))) {            throw new \Exception('Empty data');        }        app('db')->insert(sprintf('insert into `wallets` (`name`, `address`) VALUES ("%s", "%s")', $request->get('name'), $request->get('address')));        return redirect(route('admin.index'));    }    public function saveGroupRelation(Request $request) {        $machineId = $request->get('machineId');        $groupId = $request->get('groupId');        $walletAddress = $request->get('address');        /* tuetc app('db')->delete(sprintf('delete from `groups2machine` WHERE BINARY `machine_id` = "%s"', $machineId));*/        if ($groupId) {            app('db')->insert(sprintf('update `groups2machine` set `group_id`="%d" where `machine_id`="%s" and `wallet_address`="%s"', $groupId, $machineId, $walletAddress));        }        return response()->json(['success' => 'ok']);    }    private function curl_getcontent($url, $json = false, $referer = false, $count = 0) {        $headers = array();        $headers[] = "Accept-Encoding: gzip, deflate";        $headers[] = "Accept-Language: en-US,en;q=0.9";        $headers[] = "Upgrade-Insecure-Requests: 1";        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";        if ($json != false) {            $headers[] = "X-Requested-With: XMLHttpRequest";            $headers[] = "Accept: application/json";            $headers[] = "Content-Type: application/x-www-form-urlencoded";        } else {            $headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8";        }        $headers[] = "Cache-Control: max-age=0";        $headers[] = "Connection: keep-alive";        if ($referer != false) {            $headers[] = "Referer: " . $referer;        }        if (strrpos($url, 'shopee.sg/api/v1/items') !== false) {            $headers[] = "X-Csrftoken: Xg7jJJwJ4r6fcrtPDchtGilnpfaAB4YO";            $headers[] = "Cookie: _ga=GA1.2.680186883.1519700856; _gid=GA1.2.1946785702.1519700856; cto_lwid=6b9cee5e-f4f1-41c6-bdb7-9a3648cb988c; csrftoken=Xg7jJJwJ4r6fcrtPDchtGilnpfaAB4YO; __BWfp=c1519700860546xa2fcd5d59; SPC_IA=-1; SPC_U=-; SPC_EC=-; bannerShown=true; SPC_SC_TK=; UYOMAPJWEMDGJ=; SPC_SC_UD=; SPC_F=zRjUetudjMWMgmUmr3f8Tij0vF6L3p0I; REC_T_ID=5c6c2e48-1b6b-11e8-9b1a-1866dab29c0a; SPC_T_ID=\"99jVmjgK9KZL0SnPMX/yuwLLv9M3sEGDo+J3VZT9ZSQx3lifdMmK2MmdqjtqdRttt3ZgPL+lyYVOwmvMZ1z5kZsGi/X9Sfz54Vps8e6Eq1w=\"; SPC_SI=qqdhd4n2jlz4i9124ah7o2wr8m4gaafz; SPC_T_IV=\"Eqx+GcOke9cn5Sl3jATR4A==\"; _gat=1";        }        $ch = curl_init();        if ($this->via_proxy) {            curl_setopt($ch, CURLOPT_PROXY, 'http://' . $this->getProxy());            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);        }        if ($json != false) {            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);            curl_setopt($ch, CURLOPT_POST, 1);        } else {            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");        }        curl_setopt($ch, CURLOPT_URL, $url);        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');        $content = curl_exec($ch);        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);        curl_close($ch);        if (($status != 200 && $status != 404) || trim($content) == '' || (strpos($url, 'abc') !== false && strpos($content, '</html>') !== false) && $count < 4) {            sleep(1);            $count++;            return $this->curl_getcontent($url, $json, $referer, $count);        }        return $content;    }    private function getProxy() {        $f_contents = file("proxies.txt");        $line = trim($f_contents[rand(0, count($f_contents) - 1)]);        return $line;    }}  