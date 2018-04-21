<?php namespace App\Helpers;class NanoSatisticProcessing{    public $via_proxy = true;    public $proxyAuth = 'galvin24x7:egor99';    /**     * @var false|null|string     */    protected $from = null;    /**     * @var false|null|string     */    protected $to = null;    /**     * @var null     */    protected $group;    /**     * @var bool     */    protected $withoutGroup = false;    /**     * @var     */    protected $data;    public function __construct($group = null, string $from = null, string $to = null) {        if($from && $to) {            $this->from = date('Y-m-d 00:00:00', strtotime($from));            $this->to = date('Y-m-d 23:59:59', strtotime($to));        }        $this->group = $group;    }    public function setWithoutGroup() {        $this->withoutGroup = true;    }    /**     * @return array     */    public function getStatisticInformation($addresses,$fromDate, $toDate) {                if (trim($fromDate) != '' && trim($toDate) != '') {            list($m, $d, $y) = explode("/", $fromDate);            $fromDate1 = "$y-$m-$d " . date('H:i:00');            list($m, $d, $y) = explode("/", $toDate);            $toDate1 = "$y-$m-$d " . date('H:i:00');            $fromDate1 = new \DateTime($fromDate1);            $toDate1 = new \DateTime($toDate1);            $diff = $toDate1->diff($fromDate1);            if($diff->d==0&&$diff->m>0){                $hours = $diff->days * 24;            }            else{                $hours = $diff->d * 24;            }        } else {            $hours = '';        }        if($this->group && !$this->withoutGroup) {            $this->getDataForCurrentGroupWallets();        }        elseif ($this->withoutGroup) {            $this->getDataForNonGroupMachines();        }        else {            $this->getDataForAllWallets();        }        return [            'totalShares' => $this->getTotalShares($addresses,$fromDate, $toDate),            'avgHashrate' => $this->getAvgHashrate($addresses,$hours),            'onlineTime' => $this->getOnlineTime($addresses,$fromDate, $toDate),            'offlineTime' => $this->getOfflineTime($addresses,$fromDate, $toDate),        ];    }    protected function getDataForNonGroupMachines() {        $groups = app('db')->select(sprintf('select DISTINCT(machine_id) from `groups2machine`'));        $groups = collect($groups)->map(function($x){            return $x->machine_id;        })->toArray();        if(!$this->from && !$this->to) {            $sql = sprintf('select * from `data` where `machine_id` NOT IN ("%s")', implode('","', $groups));        }        else {            $sql = sprintf("select * from `data` where (`date` BETWEEN '%s' AND '%s') AND `machine_id` NOT IN ('%s')", implode("', '", $groups));        }        $this->data = app('db')->select($sql);    }    /**     * @return float|int     */    protected function getOnlineTime($addresses,$fromDate, $toDate) {        $anyActivity = [];        foreach ($this->data as $datum) {            $anyActivity[] = $datum;        }        return round(sizeof($anyActivity) * 10 / 60, 2);    }    /**     * @return float|int     */    protected function getOfflineTime($addresses,$fromDate, $toDate) {        $nonActivity = [];        foreach ($this->data as $datum) {            if($datum->shares === 0) {                $nonActivity[] = $datum;            }        }        return round(sizeof($nonActivity) * 10 / 60, 2);    }    protected function getAvgHashrate($addresses,$hours) {        $avgHashRate = 0;        foreach ($addresses as $address) {            if (is_numeric($hours)) {                $data = $this->curl_getcontent("https://api.nanopool.org/v1/eth/avghashratelimited/$address/$hours");                $temp = json_decode($data, true);                if ($temp['status'] == FALSE) {                    $data = $this->curl_getcontent("https://api.nanopool.org/v1/eth/avghashrate/$address");                }            } else {                $data = $this->curl_getcontent("https://api.nanopool.org/v1/eth/avghashrate/$address");            }            $temp = json_decode($data, true);            if ($temp['status'] == true) {                if (is_array($temp['data'])) {                    $avgHashRate += round((float) (array_sum($temp['data']) / count($temp['data'])), 2);                } else {                    $avgHashRate += round((float) $temp['data'], 2);                }            } else {                $avgHashRate += 0;            }        }                return $avgHashRate;        $avgHashRate = [];        foreach ($this->data as $datum) {            $avgHashRate[] = $datum->hashrate;        }        if($avgHashRate) {            $avgHashRate = round(array_sum($avgHashRate)/sizeof($avgHashRate), 2);        }        else {            $avgHashRate = 0;        }        return $avgHashRate;    }    protected function getTotalShares($addresses,$fromDate, $toDate) {        $totalShares = 0;        /*if (trim($fromDate) != '' && trim($toDate) != '') {            list($m, $d, $y) = explode("/", $fromDate);            $fromDate = strtotime("$y-$m-$d");            list($m, $d, $y) = explode("/", $toDate);            $toDate = strtotime("$y-$m-$d");        }        foreach ($addresses as $address) {            $data = $this->curl_getcontent("https://api.nanopool.org/v1/eth/shareratehistory/$address");            $temp = json_decode($data, true);            $data = $temp['data'];            foreach ($data as $temp) {                $date = date("Y-m-d", $temp['date']);                $date = strtotime($date);                if (is_numeric($fromDate) && is_numeric($toDate)) {                    if ($date >= $fromDate && $date <= $toDate) {                        $totalShares += $temp['shares'];                    }                } else {                    $totalShares += $temp['shares'];                }            }        }        return $totalShares;*/        foreach ($this->data as $datum) {            $totalShares += $datum->shares;        }        return $totalShares;    }    /**     * get data     */    protected function getDataForAllWallets() {        if(!$this->from && !$this->to) {            $sql = sprintf('select * from `data`');        }        else {            $sql = sprintf("select * from `data` where `date` BETWEEN '%s' AND '%s'", $this->from, $this->to);        }        $this->data = app('db')->select($sql);    }    /**     * get data     */    protected function getDataForCurrentGroupWallets() {        $groups = app('db')->select(sprintf('select DISTINCT(machine_id) from `groups2machine` WHERE group_id = %d', $this->group));        $groups = collect($groups)->map(function($x){            return $x->machine_id;        })->toArray();        if(!$this->from && !$this->to) {            $sql = sprintf('select * from `data` WHERE machine_id IN ("%s")', implode('", "', $groups));        }        else {            $sql = sprintf("select * from `data` where `date` BETWEEN '%s' AND '%s' AND machine_id IN ('%s')", $this->from, $this->to, implode("', '", $groups));        }        $this->data = app('db')->select($sql);    }        private function curl_getcontent($url, $json = false, $referer = false, $count = 0) {        $headers = array();        $headers[] = "Accept-Encoding: gzip, deflate";        $headers[] = "Accept-Language: en-US,en;q=0.9";        $headers[] = "Upgrade-Insecure-Requests: 1";        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";        if ($json != false) {            $headers[] = "X-Requested-With: XMLHttpRequest";            $headers[] = "Accept: application/json";            $headers[] = "Content-Type: application/x-www-form-urlencoded";        } else {            $headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8";        }        $headers[] = "Cache-Control: max-age=0";        $headers[] = "Connection: keep-alive";        if ($referer != false) {            $headers[] = "Referer: " . $referer;        }        if (strrpos($url, 'shopee.sg/api/v1/items') !== false) {            $headers[] = "X-Csrftoken: Xg7jJJwJ4r6fcrtPDchtGilnpfaAB4YO";            $headers[] = "Cookie: _ga=GA1.2.680186883.1519700856; _gid=GA1.2.1946785702.1519700856; cto_lwid=6b9cee5e-f4f1-41c6-bdb7-9a3648cb988c; csrftoken=Xg7jJJwJ4r6fcrtPDchtGilnpfaAB4YO; __BWfp=c1519700860546xa2fcd5d59; SPC_IA=-1; SPC_U=-; SPC_EC=-; bannerShown=true; SPC_SC_TK=; UYOMAPJWEMDGJ=; SPC_SC_UD=; SPC_F=zRjUetudjMWMgmUmr3f8Tij0vF6L3p0I; REC_T_ID=5c6c2e48-1b6b-11e8-9b1a-1866dab29c0a; SPC_T_ID=\"99jVmjgK9KZL0SnPMX/yuwLLv9M3sEGDo+J3VZT9ZSQx3lifdMmK2MmdqjtqdRttt3ZgPL+lyYVOwmvMZ1z5kZsGi/X9Sfz54Vps8e6Eq1w=\"; SPC_SI=qqdhd4n2jlz4i9124ah7o2wr8m4gaafz; SPC_T_IV=\"Eqx+GcOke9cn5Sl3jATR4A==\"; _gat=1";        }        $ch = curl_init();        if ($this->via_proxy) {            curl_setopt($ch, CURLOPT_PROXY, 'http://' . $this->getProxy());            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);        }        if ($json != false) {            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);            curl_setopt($ch, CURLOPT_POST, 1);        } else {            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");        }        curl_setopt($ch, CURLOPT_URL, $url);        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');        $content = curl_exec($ch);        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);        curl_close($ch);        if (($status != 200 && $status != 404) || trim($content) == '' || (strpos($url, 'abc') !== false && strpos($content, '</html>') !== false) && $count < 4) {            sleep(1);            $count++;            return $this->curl_getcontent($url, $json, $referer, $count);        }        return $content;    }    private function getProxy() {        $f_contents = file("proxies.txt");        $line = trim($f_contents[rand(0, count($f_contents) - 1)]);        return $line;    }}