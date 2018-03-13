<?php

namespace App\Http\Controllers;

use App\Helpers\NanopoolAPI;
use App\Helpers\NanoSatisticProcessing;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * @var NanopoolAPI
     */
    protected $nanopool;

    public function __construct() {
        $this->nanopool = new NanopoolAPI(env('WALLET_NUMBER'));
    }

    /**
     * @return array
     */
    protected function getGeneralInformation(): array {
        $generalInfo = $this->nanopool->getGeneralInfo();
        $historyHashRate = $this->nanopool->getHistoryHashrate();

        if(!$generalInfo['status'] || !$historyHashRate['status']) {
            return $this->getGeneralInformation();
        }

        return [$generalInfo, $historyHashRate];
    }


    protected function getAllWalletsMachines() {
        $wallets = app('db')->select("select * from `wallets`");
        $apiNanopool = new NanopoolAPI();
        $allMachines = [];

        foreach ($wallets as $wallet) {
            $apiNanopool->setWalletAddress($wallet->address);
            $generalInfo = $apiNanopool->getGeneralInfo();

            foreach ($generalInfo['data']['workers'] as &$worker) {
                $worker['address'] = $wallet->address;
                $worker['wallet_name'] = $wallet->name;
            }

            $allMachines = array_merge($allMachines, $generalInfo['data']['workers']);
        }

        return $allMachines;
    }

    protected function getCommonWalletsInfo() {
        $wallets = app('db')->select("select * from `wallets`");
        $apiNanopool = new NanopoolAPI();
        $allInfoMachines = [];

        foreach ($wallets as $wallet) {
            $apiNanopool->setWalletAddress($wallet->address);
            $generalInfo = $apiNanopool->getGeneralInfo();
            $allInfoMachines[] = $generalInfo;
        }

        return $allInfoMachines;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function boot(Request $request) {
        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');

        $statisticNano = new NanoSatisticProcessing(null, $fromDate, $toDate);
        $statisticData = $statisticNano->getStatisticInformation();

        $wallets = app('db')->select("select * from `wallets`");
        $groups = app('db')->select("select * from `groups`");

        $allMachines= $this->getAllWalletsMachines();
        $walletsInformation = $this->getCommonWalletsInfo();

        $day = date('m/d/Y', strtotime('-1 day'));
        $week = date('m/d/Y', strtotime('-1 week'));
        $month = date('m/d/Y', strtotime('-1 month'));
        $today = date('m/d/Y', time());

        return view('main', [
            'generalInfo' => $allMachines,
            'groups' => $groups,
            'wallets' => $wallets,
            'walletsInformation' => $walletsInformation,

            'fromDate' => $fromDate,
            'toDate' => $toDate,

            'statisticData' => $statisticData,
            'day' => $day,
            'week' => $week,
            'month' => $month,
            'today' => $today,
        ]);
    }

    public function groupEdit(string $group, Request $request) {
        $name = $request->get('name');

        if(!$name) {
            throw new \Exception('No data');
        }

        app('db')->update(sprintf('update groups set name = "%s" where id = %d', $name, $group));

        return redirect(route('admin.index'));
    }

   public function walletEdit(string $wallet, Request $request) {
        $name = $request->get('name');

        if(!$name) {
            throw new \Exception('No data');
        }

        app('db')->update(sprintf('update wallets set name = "%s" where address = "%s"', $name, $wallet));

        return redirect(route('admin.index'));
    }

    public function payments(string $wallet) {
        $apiNanopool = new NanopoolAPI();
        $apiNanopool->setWalletAddress($wallet);
        $payments = $apiNanopool->getAddressPayments();

        $totalSumPayment = $this->getPaymentStatistic($payments['data']);

        return view('payments', [
            'payments' => $payments['data'],
            'address' => $wallet,
            'time' => 'all',
            'totalSumPayment' => $totalSumPayment,
        ]);
    }

    public function paymentHistory(string $wallet, string $time) {
        $apiNanopool = new NanopoolAPI();
        $apiNanopool->setWalletAddress($wallet);
        $payments = $apiNanopool->getAddressPayments();
        $paymentsArr = [];

        if($time == 'day') {
            $from = date('Y-m-d 00:00:00', strtotime('-1 day'));
        }

        if($time == 'week') {
            $from = date('Y-m-d 00:00:00', strtotime('-1 week'));
        }

        if($time == 'month') {
            $from = date('Y-m-d 00:00:00', strtotime('-1 month'));
        }

        $to = date('Y-m-d 23:59:59', strtotime('today'));

        foreach ($payments['data'] as $payment) {
            $date = date('Y-m-d', $payment['date']);

            if($date > $from && $date < $to) {
                $paymentsArr[] = $payment;
            }
        }

        $totalSumPayment = $this->getPaymentStatistic($paymentsArr);

        return view('payments', [
            'payments' => $paymentsArr,
            'address' => $wallet,
            'time' => $time,
            'totalSumPayment' => $totalSumPayment,
        ]);
    }


    protected function getPaymentStatistic(array $data) {
        $amounts = [];

        foreach ($data as $datum) {
            $amounts[] = $datum['amount'];
        }

        return round(array_sum($amounts), 2);
    }

    public function group(string $group, Request $request) {
        $allMachines= $this->getAllWalletsMachines();
        $wallets = app('db')->select("select * from `wallets`");
        $groups = app('db')->select("select * from `groups`");
        $noGroup = false;
        $walletsInformation = $this->getCommonWalletsInfo();
        $activeGroups = [];

        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');

        $statisticNano = new NanoSatisticProcessing($group, $fromDate, $toDate);

        if($group == 'nogroup') {
            $statisticNano->setWithoutGroup();
        }

        $statisticData = $statisticNano->getStatisticInformation();

        $day = date('m/d/Y', strtotime('-1 day'));
        $week = date('m/d/Y', strtotime('-1 week'));
        $month = date('m/d/Y', strtotime('-1 month'));
        $today = date('m/d/Y', time());

        if($group == 'nogroup') {
            $groups2Machines = app('db')->select("select * from `groups` left join `groups2machine` on `groups`.id = groups2machine.group_id");
        }
        else {
            $groups2Machines = app('db')->select("select * from `groups` left join `groups2machine` on `groups`.id = groups2machine.group_id where `groups`.id = '" . $group . "'");
        }

        foreach ($groups2Machines as $group1) {
            $activeGroups[] = $group1->machine_id;
        }

        if($group == 'nogroup') {
            foreach ($allMachines as $key => $worker) {
                if(in_array($worker['id'], $activeGroups)) {
                    unset($allMachines[$key]);
                }
            }

            $noGroup = true;
        }
        else {
            foreach ($allMachines as $key => $worker) {
                if(!in_array($worker['id'], $activeGroups)) {
                    unset($allMachines[$key]);
                }
            }
        }

        return view('main', [
            'generalInfo' => $allMachines,
            'groups' => $groups,
            'groupMain' => $group,
            'noGroup' => $noGroup,
            'wallets' => $wallets,
            'walletsInformation' => $walletsInformation,
            'statisticData' => $statisticData,
            'day' => $day,
            'week' => $week,
            'month' => $month,
            'today' => $today,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);
    }


    /**
     * @param string $id
     * @param string $time
     * @return \Illuminate\View\View
     */
    public function workerHistory(string $wallet, string $id, string $time) {
        $apiNanopool = new NanopoolAPI();
        $apiNanopool->setWalletAddress($wallet);
        $graphFowWorker = $apiNanopool->retryConnect('getWorkerGraphHistory', [$id]);

        $anyActivity = [];
        $zeroActivity = [];
        $totalShares = 0;
        $averageHashRate = 0;
        $avgHashRate = [];

        if($graphFowWorker['status']) {
            foreach ($graphFowWorker['data'] as $item) {
                try {
                    app('db')->insert("insert into `data` (`date`, `shares`, `hashrate`, `machine_id`) VALUES ('".date('Y-m-d H:i:s', $item['date'])."', '".$item['shares']."', '".$item['hashrate']."', '".$id."')");
                }
                catch (\Exception $exception) {
                }
            }
        }

        $data = $this->getDataByTime($time, $id);

        foreach ($data as $datum) {
            $avgHashRate[] = $datum->hashrate;
            $totalShares += $datum->shares;

            if($datum->shares === 0) {
                $zeroActivity[] = $datum;
            }
            else {
                $anyActivity[] = $datum;
            }
        }

        if(!empty($avgHashRate)) {
            $averageHashRate = round(array_sum($avgHashRate)/sizeof($avgHashRate), 2);
        }

        $anyActivityShares = $this->calculateInform($anyActivity);
        $zeroActivityShares = $this->calculateInform($zeroActivity);

        return view('worker_history', [
            'workerHistory' => $this->prepareGraph($graphFowWorker),
            'workerId' => $id,
            'totalShares' => $totalShares,
            'averageHashRate' => $averageHashRate,
            'workerData' => $data,
            'anyActivityShares' => $anyActivityShares,
            'zeroActivityShares' => $zeroActivityShares,
            'time' => $time,
            'address' => $wallet
        ]);
    }

    protected function getDataByTime(string $time, string $machineId) {

        if($time == 'day') {
            $from = date('Y-m-d 00:00:00', strtotime('-1 day'));
        }

        if($time == 'week') {
            $from = date('Y-m-d 00:00:00', strtotime('-1 week'));
        }

        if($time == 'month') {
            $from = date('Y-m-d 00:00:00', strtotime('-1 month'));
        }

        $to = date('Y-m-d 23:59:59', strtotime('today'));

        if($time == 'all') {
            $data = app('db')->select("select * from `data` where machine_id = '{$machineId}'");
        }
        else {
            $data = app('db')->select("select * from `data` where `date` BETWEEN '" . $from . "' AND '" . $to . "' AND machine_id = '{$machineId}'");
        }

        return $data;

    }

    public function calculateInform(array $data) {
        $shares = 0;

        foreach($data as $datum) {
            $shares += $datum->shares;
        }

        return [
            'shares' => $shares,
            'actvTime' => sizeof($data) * 10 / 60,
        ];
    }

    /**
     * @param array $graphData
     * @return string
     */
    protected function prepareGraph(array $graphData): string {

        $graphDataViewOpen = [];
        $data = [];

        if(!$graphData) {
            return '';
        }

        foreach ($graphData['data'] as $item) {
            $date = $item['date'];
            $key = date('Y', $date) . date('d', $date) . date('m', $date);

            if(isset($graphDataViewOpen[$key]['hashrate'])) {
                $graphDataViewOpen[$key]['hashrate'] += $item['hashrate'];
            }
            else {
                $graphDataViewOpen[$key]['hashrate'] = $item['hashrate'];
            }

            if(isset($graphDataViewOpen[$key]['count'])) {
                $graphDataViewOpen[$key]['count'] += 1;
            }
            else {
                $graphDataViewOpen[$key]['count'] = 1;
            }
        }

        foreach ($graphData['data'] as $item) {
            $date = $item['date'];
            $key = date('Y', $date) . date('d', $date) . date('m', $date);

            $avgHashrate = $graphDataViewOpen[$key]['hashrate'] / $graphDataViewOpen[$key]['count'];

            $data[$key]['hashrate'] = round($avgHashrate);
            $data[$key]['date'] = $date;
        }

        $graphDataViewOpen = [];

        foreach ($data as $item) {
            $date = $item['date'];
            $graphDataViewOpen[$date] = '[Date.UTC(' . date('Y', $date) . ',' . (date('m', $date) - 1) . ', ' . date('d', $date) . '), ' . (isset($item['hashrate']) ? $item['hashrate'] : 0) . ']';
        }

        ksort($graphDataViewOpen);

        return implode(', ', $graphDataViewOpen);
    }

    public function addGroup(Request $request) {

        if(empty($request->get('name'))) {
            throw new \Exception('Empty data');
        }

        app('db')->insert(sprintf('insert into `groups` (`name`) VALUES ("%s")', $request->get('name')));

        return redirect(route('admin.index'));
    }

    public function addWallet(Request $request) {

        if(empty($request->get('address'))) {
            throw new \Exception('Empty data');
        }

        app('db')->insert(sprintf('insert into `wallets` (`name`, `address`) VALUES ("%s", "%s")', $request->get('name'), $request->get('address')));

        return redirect(route('admin.index'));
    }


    public function saveGroupRelation(Request $request) {
        $machineId = $request->get('machineId');
        $groupId = $request->get('groupId');
        $walletAddress = $request->get('address');

        app('db')->delete(sprintf('delete from `groups2machine` WHERE BINARY `machine_id` = "%s"', $machineId));

        if($groupId) {
            app('db')->insert(sprintf('insert into `groups2machine` (`group_id`, `machine_id`, `wallet_address`) VALUES ("%d", "%s", "%s")', $groupId, $machineId, $walletAddress));
        }

        return response()->json(['success' => 'ok']);
    }
}