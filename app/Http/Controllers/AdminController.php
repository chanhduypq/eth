<?php namespace App\Http\Controllers;use App\Helpers\NanopoolAPI;use Illuminate\Http\Request;class AdminController extends Controller{    protected $nanopool;        private $wallets;    private $groups;    private $groups2machines;    private $datas;    private $machinesHasGroup = array();    private $machinesHasNoGroup = array();    public function __construct() {        set_time_limit(0);        ini_set('max_execution_time', 3600);        $this->nanopool = new NanopoolAPI(env('WALLET_NUMBER'));                $this->wallets = app('db')->select("select * from `wallets`");        $hasUpdate=false;        $apiNanopool = new NanopoolAPI();        foreach ($this->wallets as $temp){            if($temp->general_info==''){                $apiNanopool->setWalletAddress($temp->address);                $generalInfo = $apiNanopool->getGeneralInfo();                $generalInfo= json_encode($generalInfo);                $generalInfo= str_replace("'", "\'", $generalInfo);                app('db')->insert("update wallets set general_info ='$generalInfo' where address='".$temp->address."';");                $hasUpdate=true;            }        }        if($hasUpdate){            sleep(1);            $this->wallets = app('db')->select("select * from `wallets`");        }                foreach ($this->wallets as $wallet) {            $temps = app('db')->select("select * from `groups2machine` where wallet_address='".$wallet->address."'");            if(count($temps)==0){                $apiNanopool->setWalletAddress($wallet->address);                $data = $apiNanopool->getListWorkders($assoc = false);                if ($data->status == true) {                    $temp = $data->data;                    foreach ($temp as $t) {                        app('db')->insert(sprintf('insert into `groups2machine` (`group_id`, `machine_id`, `wallet_address`) VALUES (NULL, "%s", "%s")', $t->id, $wallet->address));                    }                }            }                    }                        $this->groups = app('db')->select("select * from `groups`");        $this->groups2machines = app('db')->select("select * from `groups2machine`");        $this->datas = app('db')->select("select * from `data`");        foreach ($this->groups2machines as $temp){            if($temp->group_id==''){                $this->machinesHasNoGroup[$temp->machine_id] = $temp->wallet_address;            }            else{                $this->machinesHasGroup[$temp->machine_id] = $temp->wallet_address;            }                    }    }    public function index() {        $groups = $this->groups;        $wallets = $this->wallets;        $groups2machines = $this->groups2machines;        $apiNanopool = new NanopoolAPI();        $walletsArr = [];        $relationsG2m = [];        foreach ($wallets as $wallet) {            $workersList['data']=array();             foreach ($groups2machines as $groups2machine){                 if($wallet->address==$groups2machine->wallet_address){                     $workersList['data'][]=array('id'=>$groups2machine->machine_id,'address'=>$wallet->address);                 }             }            $walletsArr = array_merge($workersList['data'], $walletsArr);        }        foreach ($groups2machines as $groups2machine) {            $relationsG2m[$groups2machine->machine_id] = $groups2machine->group_id;        }        return view('admin.index', [            'groups' => $groups,            'workersList' => $walletsArr,            'relationsG2m' => $relationsG2m,            'wallets' => $wallets        ]);    }    /**     * @param Request $request     * @return \Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector     * @throws \Exception     */    public function deleteWallet(Request $request) {        $walletId = $request->get('walletId');        if(!$walletId) {            throw new \Exception('Empty data');        }        app('db')->delete(sprintf('delete from wallets where address = "%s"', $walletId));        return response()->json(['ok' => 1]);    }    public function deleteGroup(Request $request) {        $groupId = $request->get('groupId');        if(!$groupId) {            throw new \Exception('Empty data');        }        try {            app('db')->delete(sprintf('delete from groups where id = %d', $groupId));        app('db')->delete(sprintf('delete from groups2machine where group_id = %d', $groupId));        }        catch(\Exception $e) {        }        return response()->json(['ok' => 1]);    }}