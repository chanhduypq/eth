<?phpnamespace App\Http\Controllers;use App\Helpers\NanopoolAPI;use App\Helpers\NanoSatisticProcessing;use Illuminate\Http\Request;class ExampleController extends Controller {    /**     * @var NanopoolAPI     */    protected $nanopool;    public function __construct() {        set_time_limit(0);        ini_set('max_execution_time', 1200);        $this->nanopool = new NanopoolAPI(env('WALLET_NUMBER'));    }    /**     * @return array     */    protected function getGeneralInformation(): array {        $generalInfo = $this->nanopool->getGeneralInfo();        $historyHashRate = $this->nanopool->getHistoryHashrate();        if (!$generalInfo['status'] || !$historyHashRate['status']) {            return $this->getGeneralInformation();        }        return [$generalInfo, $historyHashRate];    }    protected function getAllWalletsMachines($fromDate, $toDate) {        $fromDateRoot = $fromDate;        $toDateRoot = $toDate;        if ($fromDate && $toDate) {            list($m, $d, $y) = explode("/", $fromDate);            $fromDate = ("$y-$m-$d 00:00:00");            list($m, $d, $y) = explode("/", $toDate);            $toDate = ("$y-$m-$d 23:59:59");        }        $wallets = app('db')->select("select * from `wallets`");        $apiNanopool = new NanopoolAPI();        $allMachines = [];        foreach ($wallets as $wallet) {            $apiNanopool->setWalletAddress($wallet->address);            $generalInfo = $apiNanopool->getGeneralInfo();            foreach ($generalInfo['data']['workers'] as &$worker) {                $worker['address'] = $wallet->address;                $worker['wallet_name'] = $wallet->name;                                $data = file_get_contents('https://api.nanopool.org/v1/eth/hashratechart/' . $wallet->address . '/' . $worker['id']);                $data = json_decode($data, true);                $data = $data['data'];                if ($fromDateRoot && $toDateRoot) {                    if(!isset($diff)){                        $fromDate1 = new \DateTime($fromDate);                        $toDate1 = new \DateTime($toDate);                        $diff = $toDate1->diff($fromDate1);                    }                                        if ($diff->d == 7) {                        unset($worker['h1']);                        list($m, $d, $y) = explode("/", $fromDateRoot);                        $fromDate = "$y-$m-$d 00:00:00";                        $worker['d1'] = 0;                        $toDate = date('Y-m-d 23:59:59', strtotime($fromDate . ' +1 day'));                        $count=0;                        foreach ($data as $temp) {                            $date = date("Y-m-d", $temp['date']);                            $date = strtotime($date);                            if ($date >= strtotime($fromDate) && $date <= strtotime($toDate)) {                                $worker['d1'] += $temp['hashrate'];                                $count++;                            }                        }                        if($count!=0)                         $worker['d1']=round(((float)$worker['d1'])/$count,1);                        for ($i = 2; $i <= 7; $i++) {                            $count=0;                            $worker["d$i"] = 0;                            $fromDate = date('Y-m-d 00:00:00', strtotime($fromDate . ' +1 day'));                            $toDate = date('Y-m-d 23:59:59', strtotime($fromDate . ' +1 day'));                            foreach ($data as $temp) {                                $date = date("Y-m-d", $temp['date']);                                $date = strtotime($date);                                if ($date >= strtotime($fromDate) && $date <= strtotime($toDate)) {                                    $worker["d$i"] += $temp['hashrate'];                                    $count++;                                }                            }                            if($count!=0) {                                $worker["d$i"]=round(((float)$worker["d$i"])/$count,1);                            }                                                    }                    }                    else if($diff->d==0&&$diff->m>0){                        unset($worker['h1']);                        list($m, $d, $y) = explode("/", $fromDateRoot);                        $fromDate = "$y-$m-$d 00:00:00";                        $worker['w1'] = 0;                        $toDate = date('Y-m-d 23:59:59', strtotime($fromDate . ' +7 day'));                        $count=0;                        foreach ($data as $temp) {                            $date = date("Y-m-d", $temp['date']);                            $date = strtotime($date);                            if ($date >= strtotime($fromDate) && $date <= strtotime($toDate)) {                                $worker['w1'] += $temp['hashrate'];                                $count++;                            }                        }                        if($count!=0) {                            $worker['w1']=round(((float)$worker['w1'])/$count,1);                        }                                                for ($i = 2; $i <= 4; $i++) {                            $count=0;                            $worker["w$i"] = 0;                            $fromDate = date('Y-m-d 00:00:00', strtotime($fromDate . ' +7 day'));                            $toDate = date('Y-m-d 23:59:59', strtotime($fromDate . ' +7 day'));                            foreach ($data as $temp) {                                $date = date("Y-m-d", $temp['date']);                                $date = strtotime($date);                                if ($date >= strtotime($fromDate) && $date <= strtotime($toDate)) {                                    $worker["w$i"] += $temp['hashrate'];                                    $count++;                                }                            }                            if($count!=0) {                                $worker["w$i"]=round(((float)$worker["w$i"])/$count,1);                            }                                                    }                    }                }                else{                    unset($worker['h1']);                    $count=0;                    $worker["all"] = 0;//                    if($wallet->address=='0x76483a4d6eaebb26bc05e303a16aae993f4c3770'){//                        echo '<pre>';//                        var_dump($data);//                        echo '</pre>';//                        exit;//                    }                    foreach ($data as $temp) {                        $worker["all"] += $temp['hashrate'];                        $count++;                    }                    if($count!=0) {                        $worker["all"]=round(((float)$worker["all"])/$count,1);                    }                                    }            }            $allMachines = array_merge($allMachines, $generalInfo['data']['workers']);        }        return $allMachines;    }    protected function getCommonWalletsInfo() {        $wallets = app('db')->select("select * from `wallets`");        $apiNanopool = new NanopoolAPI();        $allInfoMachines = [];        foreach ($wallets as $wallet) {            $apiNanopool->setWalletAddress($wallet->address);            $generalInfo = $apiNanopool->getGeneralInfo();            $allInfoMachines[] = $generalInfo;        }        return $allInfoMachines;    }    /**     * @return \Illuminate\View\View     */    public function boot(Request $request) {        $fromDate = $request->get('fromDate');        $toDate = $request->get('toDate');        $wallets = app('db')->select("select * from `wallets`");        $groups = app('db')->select("select * from `groups`");        $allMachines = $this->getAllWalletsMachines($fromDate, $toDate);        //        echo '<pre>';//        var_dump($allMachines);//        echo '</pre>';//        exit;        $walletsInformation = $this->getCommonWalletsInfo();        $addresses = array();        for ($i = 0; $i < count($walletsInformation); $i++) {            $addresses[] = $walletsInformation[$i]['data']['account'];        }        if ($fromDate) {            list($m, $d, $y) = explode("/", $fromDate);            $fromDate1 = "$y-$m-$d " . date('H:i:00');        } else {            $fromDate1 = null;        }        if ($toDate) {            list($m, $d, $y) = explode("/", $toDate);            $toDate1 = "$y-$m-$d " . date('H:i:00');        } else {            $toDate1 = null;        }        $statisticNano = new NanoSatisticProcessing(null, $fromDate1, $toDate1);        $statisticData = $statisticNano->getStatisticInformation($addresses, $fromDate, $toDate);        $day = date('m/d/Y', strtotime('-1 day'));        $week = date('m/d/Y', strtotime('-1 week'));        $month = date('m/d/Y', strtotime('-1 month'));        $today = date('m/d/Y', time());        return view('main', [            'generalInfo' => $allMachines,            'groups' => $groups,            'wallets' => $wallets,            'walletsInformation' => $walletsInformation,            'fromDate' => $fromDate,            'toDate' => $toDate,            'statisticData' => $statisticData,            'day' => $day,            'week' => $week,            'month' => $month,            'today' => $today,        ]);    }    public function groupEdit(string $group, Request $request) {        $name = $request->get('name');        if (!$name) {            throw new \Exception('No data');        }        app('db')->update(sprintf('update groups set name = "%s" where id = %d', $name, $group));        return redirect(route('admin.index'));    }    public function walletEdit(string $wallet, Request $request) {        $name = $request->get('name');        if (!$name) {            throw new \Exception('No data');        }        app('db')->update(sprintf('update wallets set name = "%s" where address = "%s"', $name, $wallet));        return redirect(route('admin.index'));    }    public function payments(string $wallet) {        $apiNanopool = new NanopoolAPI();        $apiNanopool->setWalletAddress($wallet);        $payments = $apiNanopool->getAddressPayments();        $totalSumPayment = $this->getPaymentStatistic($payments['data']);        return view('payments', [            'payments' => $payments['data'],            'address' => $wallet,            'time' => 'all',            'totalSumPayment' => $totalSumPayment,        ]);    }    public function paymentHistory(string $wallet, string $time) {        $apiNanopool = new NanopoolAPI();        $apiNanopool->setWalletAddress($wallet);        $payments = $apiNanopool->getAddressPayments();        $paymentsArr = [];        if ($time == 'day') {            $from = date('Y-m-d 00:00:00', strtotime('-1 day'));        }        if ($time == 'week') {            $from = date('Y-m-d 00:00:00', strtotime('-1 week'));        }        if ($time == 'month') {            $from = date('Y-m-d 00:00:00', strtotime('-1 month'));        }        $to = date('Y-m-d 23:59:59', strtotime('today'));        foreach ($payments['data'] as $payment) {            $date = date('Y-m-d', $payment['date']);            if ($date > $from && $date < $to) {                $paymentsArr[] = $payment;            }        }        $totalSumPayment = $this->getPaymentStatistic($paymentsArr);        return view('payments', [            'payments' => $paymentsArr,            'address' => $wallet,            'time' => $time,            'totalSumPayment' => $totalSumPayment,        ]);    }    protected function getPaymentStatistic(array $data) {        $amounts = [];        foreach ($data as $datum) {            $amounts[] = $datum['amount'];        }        return round(array_sum($amounts), 2);    }    public function group(string $group, Request $request) {        $fromDate = $request->get('fromDate');        $toDate = $request->get('toDate');        $allMachines = $this->getAllWalletsMachines($fromDate, $toDate);        $wallets = app('db')->select("select * from `wallets`");        $groups = app('db')->select("select * from `groups`");        $noGroup = false;        $walletsInformation = $this->getCommonWalletsInfo();        $addresses = array();        for ($i = 0; $i < count($walletsInformation); $i++) {            $addresses[] = $walletsInformation[$i]['data']['account'];        }        $activeGroups = [];        if ($fromDate) {            list($m, $d, $y) = explode("/", $fromDate);            $fromDate1 = "$y-$m-$d " . date('H:i:00');        } else {            $fromDate1 = null;        }        if ($toDate) {            list($m, $d, $y) = explode("/", $toDate);            $toDate1 = "$y-$m-$d " . date('H:i:00');        } else {            $toDate1 = null;        }        $statisticNano = new NanoSatisticProcessing($group, $fromDate1, $toDate1);        if ($group == 'nogroup') {            $statisticNano->setWithoutGroup();        }        $statisticData = $statisticNano->getStatisticInformation($addresses, $fromDate, $toDate);        $day = date('m/d/Y', strtotime('-1 day'));        $week = date('m/d/Y', strtotime('-1 week'));        $month = date('m/d/Y', strtotime('-1 month'));        $today = date('m/d/Y', time());        if ($group == 'nogroup') {            $groups2Machines = app('db')->select("select * from `groups` left join `groups2machine` on `groups`.id = groups2machine.group_id");        } else {            $groups2Machines = app('db')->select("select * from `groups` left join `groups2machine` on `groups`.id = groups2machine.group_id where `groups`.id = '" . $group . "'");        }        foreach ($groups2Machines as $group1) {            $activeGroups[] = $group1->machine_id;        }        if ($group == 'nogroup') {            foreach ($allMachines as $key => $worker) {                if (in_array($worker['id'], $activeGroups)) {                    unset($allMachines[$key]);                }            }            $noGroup = true;        } else {            foreach ($allMachines as $key => $worker) {                if (!in_array($worker['id'], $activeGroups)) {                    unset($allMachines[$key]);                }            }        }        return view('main', [            'generalInfo' => $allMachines,            'groups' => $groups,            'groupMain' => $group,            'noGroup' => $noGroup,            'wallets' => $wallets,            'walletsInformation' => $walletsInformation,            'statisticData' => $statisticData,            'day' => $day,            'week' => $week,            'month' => $month,            'today' => $today,            'fromDate' => $fromDate,            'toDate' => $toDate,        ]);    }    /**     * @param string $id     * @param string $time     * @return \Illuminate\View\View     */    public function workerHistory(string $wallet, string $id, string $time) {        $apiNanopool = new NanopoolAPI();        $apiNanopool->setWalletAddress($wallet);        $graphFowWorker = $apiNanopool->retryConnect('getWorkerGraphHistory', [$id]);        $anyActivity = [];        $zeroActivity = [];        $totalShares = 0;        $averageHashRate = 0;        $avgHashRate = [];        if ($graphFowWorker['status']) {            foreach ($graphFowWorker['data'] as $item) {                try {                    app('db')->insert("insert into `data` (`date`, `shares`, `hashrate`, `machine_id`) VALUES ('" . date('Y-m-d H:i:s', $item['date']) . "', '" . $item['shares'] . "', '" . $item['hashrate'] . "', '" . $id . "')");                } catch (\Exception $exception) {                                    }            }        }        $data = $this->getDataByTime($time, $id);        foreach ($data as $datum) {            $avgHashRate[] = $datum->hashrate;            $totalShares += $datum->shares;            if ($datum->shares === 0) {                $zeroActivity[] = $datum;            } else {                $anyActivity[] = $datum;            }        }        if (!empty($avgHashRate)) {            $averageHashRate = round(array_sum($avgHashRate) / sizeof($avgHashRate), 2);        }        $anyActivityShares = $this->calculateInform($anyActivity);        $zeroActivityShares = $this->calculateInform($zeroActivity);        return view('worker_history', [            'workerHistory' => $this->prepareGraph($graphFowWorker),            'workerId' => $id,            'totalShares' => $totalShares,            'averageHashRate' => $averageHashRate,            'workerData' => $data,            'anyActivityShares' => $anyActivityShares,            'zeroActivityShares' => $zeroActivityShares,            'time' => $time,            'address' => $wallet        ]);    }    protected function getDataByTime(string $time, string $machineId) {        if ($time == 'day') {            $from = date('Y-m-d 00:00:00', strtotime('-1 day'));        }        if ($time == 'week') {            $from = date('Y-m-d 00:00:00', strtotime('-1 week'));        }        if ($time == 'month') {            $from = date('Y-m-d 00:00:00', strtotime('-1 month'));        }        $to = date('Y-m-d 23:59:59', strtotime('today'));        if ($time == 'all') {            $data = app('db')->select("select * from `data` where machine_id = '{$machineId}'");        } else {            $data = app('db')->select("select * from `data` where `date` BETWEEN '" . $from . "' AND '" . $to . "' AND machine_id = '{$machineId}'");        }        return $data;    }    public function calculateInform(array $data) {        $shares = 0;        foreach ($data as $datum) {            $shares += $datum->shares;        }        return [            'shares' => $shares,            'actvTime' => sizeof($data) * 10 / 60,        ];    }    /**     * @param array $graphData     * @return string     */    protected function prepareGraph(array $graphData): string {        $graphDataViewOpen = [];        $data = [];        if (!$graphData) {            return '';        }        foreach ($graphData['data'] as $item) {            $date = $item['date'];            $key = date('Y', $date) . date('d', $date) . date('m', $date);            if (isset($graphDataViewOpen[$key]['hashrate'])) {                $graphDataViewOpen[$key]['hashrate'] += $item['hashrate'];            } else {                $graphDataViewOpen[$key]['hashrate'] = $item['hashrate'];            }            if (isset($graphDataViewOpen[$key]['count'])) {                $graphDataViewOpen[$key]['count'] += 1;            } else {                $graphDataViewOpen[$key]['count'] = 1;            }        }        foreach ($graphData['data'] as $item) {            $date = $item['date'];            $key = date('Y', $date) . date('d', $date) . date('m', $date);            $avgHashrate = $graphDataViewOpen[$key]['hashrate'] / $graphDataViewOpen[$key]['count'];            $data[$key]['hashrate'] = round($avgHashrate);            $data[$key]['date'] = $date;        }        $graphDataViewOpen = [];        foreach ($data as $item) {            $date = $item['date'];            $graphDataViewOpen[$date] = '[Date.UTC(' . date('Y', $date) . ',' . (date('m', $date) - 1) . ', ' . date('d', $date) . '), ' . (isset($item['hashrate']) ? $item['hashrate'] : 0) . ']';        }        ksort($graphDataViewOpen);        return implode(', ', $graphDataViewOpen);    }    public function addGroup(Request $request) {        if (empty($request->get('name'))) {            throw new \Exception('Empty data');        }        app('db')->insert(sprintf('insert into `groups` (`name`) VALUES ("%s")', $request->get('name')));        return redirect(route('admin.index'));    }    public function addWallet(Request $request) {        if (empty($request->get('address'))) {            throw new \Exception('Empty data');        }        app('db')->insert(sprintf('insert into `wallets` (`name`, `address`) VALUES ("%s", "%s")', $request->get('name'), $request->get('address')));        return redirect(route('admin.index'));    }    public function saveGroupRelation(Request $request) {        $machineId = $request->get('machineId');        $groupId = $request->get('groupId');        $walletAddress = $request->get('address');        app('db')->delete(sprintf('delete from `groups2machine` WHERE BINARY `machine_id` = "%s"', $machineId));        if ($groupId) {            app('db')->insert(sprintf('insert into `groups2machine` (`group_id`, `machine_id`, `wallet_address`) VALUES ("%d", "%s", "%s")', $groupId, $machineId, $walletAddress));        }        return response()->json(['success' => 'ok']);    }}