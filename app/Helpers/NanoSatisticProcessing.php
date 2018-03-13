<?php


namespace App\Helpers;


class NanoSatisticProcessing
{
    /**
     * @var false|null|string
     */
    protected $from = null;

    /**
     * @var false|null|string
     */
    protected $to = null;

    /**
     * @var null
     */
    protected $group;

    /**
     * @var bool
     */
    protected $withoutGroup = false;

    /**
     * @var
     */
    protected $data;

    public function __construct($group = null, string $from = null, string $to = null) {

        if($from && $to) {
            $this->from = date('Y-m-d 00:00:00', strtotime($from));
            $this->to = date('Y-m-d 23:59:59', strtotime($to));
        }

        $this->group = $group;
    }

    public function setWithoutGroup() {
        $this->withoutGroup = true;
    }

    /**
     * @return array
     */
    public function getStatisticInformation() {

        if($this->group && !$this->withoutGroup) {
            $this->getDataForCurrentGroupWallets();
        }
        elseif ($this->withoutGroup) {
            $this->getDataForNonGroupMachines();
        }
        else {
            $this->getDataForAllWallets();
        }

        return [
            'totalShares' => $this->getTotalShares(),
            'avgHashrate' => $this->getAvgHashrate(),
            'onlineTime' => $this->getOnlineTime(),
            'offlineTime' => $this->getOfflineTime(),
        ];
    }

    protected function getDataForNonGroupMachines() {

        $groups = app('db')->select(sprintf('select DISTINCT(machine_id) from `groups2machine`'));

        $groups = collect($groups)->map(function($x){
            return $x->machine_id;
        })->toArray();

        if(!$this->from && !$this->to) {
            $sql = sprintf('select * from `data` where `machine_id` NOT IN ("%s")', implode('","', $groups));
        }
        else {
            $sql = sprintf("select * from `data` where (`date` BETWEEN '%s' AND '%s') AND `machine_id` NOT IN ('%s')", implode("', '", $groups));
        }

        $this->data = app('db')->select($sql);
    }

    /**
     * @return float|int
     */
    protected function getOnlineTime() {
        $anyActivity = [];

        foreach ($this->data as $datum) {
            $anyActivity[] = $datum;
        }

        return round(sizeof($anyActivity) * 10 / 60, 2);
    }

    /**
     * @return float|int
     */
    protected function getOfflineTime() {
        $nonActivity = [];

        foreach ($this->data as $datum) {
            if($datum->shares === 0) {
                $nonActivity[] = $datum;
            }
        }

        return round(sizeof($nonActivity) * 10 / 60, 2);
    }

    protected function getAvgHashrate() {
        $avgHashRate = [];

        foreach ($this->data as $datum) {
            $avgHashRate[] = $datum->hashrate;
        }

        if($avgHashRate) {
            $avgHashRate = round(array_sum($avgHashRate)/sizeof($avgHashRate), 2);
        }
        else {
            $avgHashRate = 0;
        }

        return $avgHashRate;
    }

    protected function getTotalShares() {
        $totalShares = 0;

        foreach ($this->data as $datum) {
            $totalShares += $datum->shares;
        }

        return $totalShares;
    }

    /**
     * get data
     */
    protected function getDataForAllWallets() {

        if(!$this->from && !$this->to) {
            $sql = sprintf('select * from `data`');
        }
        else {
            $sql = sprintf("select * from `data` where `date` BETWEEN '%s' AND '%s'", $this->from, $this->to);
        }

        $this->data = app('db')->select($sql);
    }

    /**
     * get data
     */
    protected function getDataForCurrentGroupWallets() {
        $groups = app('db')->select(sprintf('select DISTINCT(machine_id) from `groups2machine` WHERE group_id = %d', $this->group));

        $groups = collect($groups)->map(function($x){
            return $x->machine_id;
        })->toArray();

        if(!$this->from && !$this->to) {
            $sql = sprintf('select * from `data` WHERE machine_id IN ("%s")', implode('", "', $groups));
        }
        else {
            $sql = sprintf("select * from `data` where `date` BETWEEN '%s' AND '%s' AND machine_id IN ('%s')", $this->from, $this->to, implode("', '", $groups));
        }

        $this->data = app('db')->select($sql);
    }
}