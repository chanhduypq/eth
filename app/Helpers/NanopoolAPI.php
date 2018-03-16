<?php /** * Created by PhpStorm. * User: egorlarionov1 * Date: 16.01.2018 * Time: 19:40 */namespace App\Helpers;class NanopoolAPI{    public $via_proxy = true;    public $proxyAuth = 'galvin24x7:egor99';    protected $walletNumber;    public function __construct(string $walletNumber = null) {        $this->walletNumber = $walletNumber;    }    /**     * @param string $walletAddress     */    public function setWalletAddress(string $walletAddress) {        $this->walletNumber = $walletAddress;    }    /**     * @return array     */    public function getAccountBalance()    {        $data = $this->curl_getcontent('https://api.nanopool.org/v1/eth/balance/' . $this->walletNumber);        return $this->json2Array($data);    }    public function getAverageHashrate24Hours()    {        $data = $this->curl_getcontent('https://api.nanopool.org/v1/eth/avghashratelimited/' . $this->walletNumber . '/24');        return $this->json2Array($data);    }    public function getListWorkders($assoc=true)    {        $data = $this->curl_getcontent('https://api.nanopool.org/v1/eth/workers/' . $this->walletNumber);        if ($assoc == false) {            return json_decode($data);        }        return $this->json2Array($data);    }    public function getHistoryHashrate()    {        $data = $this->curl_getcontent('https://api.nanopool.org/v1/eth/hashratechart/' . $this->walletNumber);        return $this->json2Array($data);    }    public function getCurrentHashrate()    {        $data = $this->curl_getcontent('https://api.nanopool.org/v1/eth/hashrate/' . $this->walletNumber);        return $this->json2Array($data);    }    public function getGeneralInfo()    {        $data = $this->curl_getcontent('https://api.nanopool.org/v1/eth/user/' . $this->walletNumber);        return $this->json2Array($data);    }        public function getWorkerList()    {        $data = $this->curl_getcontent("https://api.nanopool.org/v1/eth/workers/".$this->walletNumber);        $data = $this->json2Array($data);        if ($data['status'] == false) {            return array();        }        return $data['data'];    }        public function getWorkerGraphHistory(string $workerId)    {        $data = $this->curl_getcontent('https://api.nanopool.org/v1/eth/hashratechart/' . $this->walletNumber . '/' . $workerId);        return $this->json2Array($data);    }    public function getAddressPayments()    {        $data = $this->curl_getcontent('https://api.nanopool.org/v1/eth/payments/' . $this->walletNumber);        return $this->json2Array($data);    }    /**     * @param string $method     * @param array $args     * @return mixed     */    public function retryConnect(string $method, array $args)    {        if(!method_exists($this, $method)) {            throw new \LogicException('No method');        }        $response = $this->$method($args[0]);        if(!$response['status']) {            return $this->retryConnect($method, $args);        }        return $response;    }    /**     * @param string $json2Array     * @return mixed     */    protected function json2Array(string $json2Array)    {        return json_decode($json2Array, true);    }        private function curl_getcontent($url, $json = false, $referer = false, $count = 0) {        $headers = array();        $headers[] = "Accept-Encoding: gzip, deflate";        $headers[] = "Accept-Language: en-US,en;q=0.9";        $headers[] = "Upgrade-Insecure-Requests: 1";        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";        if ($json != false) {            $headers[] = "X-Requested-With: XMLHttpRequest";            $headers[] = "Accept: application/json";            $headers[] = "Content-Type: application/x-www-form-urlencoded";        } else {            $headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8";        }        $headers[] = "Cache-Control: max-age=0";        $headers[] = "Connection: keep-alive";        if ($referer != false) {            $headers[] = "Referer: " . $referer;        }        if (strrpos($url, 'shopee.sg/api/v1/items') !== false) {            $headers[] = "X-Csrftoken: Xg7jJJwJ4r6fcrtPDchtGilnpfaAB4YO";            $headers[] = "Cookie: _ga=GA1.2.680186883.1519700856; _gid=GA1.2.1946785702.1519700856; cto_lwid=6b9cee5e-f4f1-41c6-bdb7-9a3648cb988c; csrftoken=Xg7jJJwJ4r6fcrtPDchtGilnpfaAB4YO; __BWfp=c1519700860546xa2fcd5d59; SPC_IA=-1; SPC_U=-; SPC_EC=-; bannerShown=true; SPC_SC_TK=; UYOMAPJWEMDGJ=; SPC_SC_UD=; SPC_F=zRjUetudjMWMgmUmr3f8Tij0vF6L3p0I; REC_T_ID=5c6c2e48-1b6b-11e8-9b1a-1866dab29c0a; SPC_T_ID=\"99jVmjgK9KZL0SnPMX/yuwLLv9M3sEGDo+J3VZT9ZSQx3lifdMmK2MmdqjtqdRttt3ZgPL+lyYVOwmvMZ1z5kZsGi/X9Sfz54Vps8e6Eq1w=\"; SPC_SI=qqdhd4n2jlz4i9124ah7o2wr8m4gaafz; SPC_T_IV=\"Eqx+GcOke9cn5Sl3jATR4A==\"; _gat=1";        }        $ch = curl_init();        if ($this->via_proxy) {            curl_setopt($ch, CURLOPT_PROXY, 'http://' . $this->getProxy());            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);        }        if ($json != false) {            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);            curl_setopt($ch, CURLOPT_POST, 1);        } else {            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");        }        curl_setopt($ch, CURLOPT_URL, $url);        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');        $content = curl_exec($ch);        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);        curl_close($ch);        if (($status != 200 && $status != 404) || trim($content) == '' || (strpos($url, 'abc') !== false && strpos($content, '</html>') !== false) && $count < 4) {            sleep(1);            $count++;            return $this->curl_getcontent($url, $json, $referer, $count);        }        return $content;    }    private function getProxy() {        $f_contents = file("proxies.txt");        $line = trim($f_contents[rand(0, count($f_contents) - 1)]);        return $line;    }}