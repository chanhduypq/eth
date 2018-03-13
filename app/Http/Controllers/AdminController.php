<?php

namespace App\Http\Controllers;

use App\Helpers\NanopoolAPI;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $nanopool;

    public function __construct() {
        $this->nanopool = new NanopoolAPI(env('WALLET_NUMBER'));
    }

    public function index() {
        $groups = app('db')->select("select * from `groups`");
        $wallets = app('db')->select("select * from `wallets`");
        $groups2machines = app('db')->select("select * from `groups2machine`");

        $apiNanopool = new NanopoolAPI();
        $walletsArr = [];
        $relationsG2m = [];

        foreach ($wallets as $wallet) {
            $apiNanopool->setWalletAddress($wallet->address);
            $workersList = $apiNanopool->getListWorkders();

            foreach ($workersList['data'] as &$datum) {
                $datum['address'] = $wallet->address;
            }

            $walletsArr = array_merge($workersList['data'], $walletsArr);
        }

        foreach ($groups2machines as $groups2machine) {
            $relationsG2m[$groups2machine->machine_id] = $groups2machine->group_id;
        }

        return view('admin.index', [
            'groups' => $groups,
            'workersList' => $walletsArr,
            'relationsG2m' => $relationsG2m,
            'wallets' => $wallets
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector
     * @throws \Exception
     */
    public function deleteWallet(Request $request) {
        $walletId = $request->get('walletId');

        if(!$walletId) {
            throw new \Exception('Empty data');
        }

        app('db')->delete(sprintf('delete from wallets where address = "%s"', $walletId));

        return response()->json(['ok' => 1]);
    }


    public function deleteGroup(Request $request) {
        $groupId = $request->get('groupId');

        if(!$groupId) {
            throw new \Exception('Empty data');
        }


        try {
            app('db')->delete(sprintf('delete from groups where id = %d', $groupId));
        app('db')->delete(sprintf('delete from groups2machine where group_id = %d', $groupId));
        }
        catch(\Exception $e) {
            
        }
        

        return response()->json(['ok' => 1]);
    }


}
