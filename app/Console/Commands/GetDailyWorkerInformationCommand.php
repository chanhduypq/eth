<?php
/**
 * File defines class for a console command to send
 * email notifications to users
 *
 * PHP version >= 7.0
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */

namespace App\Console\Commands;


use App\Helpers\NanopoolAPI;
use Illuminate\Console\Command;



/**
 * Class deletePostsCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class GetDailyWorkerInformationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "information:workers";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ethWorker = new NanopoolAPI();
        $wallets = app('db')->select("select * from `wallets`");

        foreach ($wallets as $wallet) {
            $ethWorker->setWalletAddress($wallet->address);

            $workersList = $ethWorker->getListWorkders();

            foreach ($workersList['data'] as $item) {
                $graphFowWorker = $ethWorker->retryConnect('getWorkerGraphHistory', [$item['id']]);

                echo $item['id'] . PHP_EOL;

                foreach ($graphFowWorker['data'] as $item1) {
                    try {
                        app('db')->select("insert into `data` (`date`, `shares`, `hashrate`, `machine_id`) VALUES ('".date('Y-m-d H:i:s', $item1['date'])."', '".$item1['shares']."', '".$item1['hashrate']."', '".$item['id']."')");
                    }
                    catch (\Exception $exception) {
                    }
                }
            }
        }



    }
}
