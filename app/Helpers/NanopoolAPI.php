<?php
/**
 * Created by PhpStorm.
 * User: egorlarionov1
 * Date: 16.01.2018
 * Time: 19:40
 */

namespace App\Helpers;


class NanopoolAPI
{
    protected $walletNumber;

    public function __construct(string $walletNumber = null) {
        $this->walletNumber = $walletNumber;
    }

    /**
     * @param string $walletAddress
     */
    public function setWalletAddress(string $walletAddress) {
        $this->walletNumber = $walletAddress;
    }

    /**
     * @return array
     */
    public function getAccountBalance()
    {
        $data = file_get_contents('https://api.nanopool.org/v1/eth/balance/' . $this->walletNumber);

        return $this->json2Array($data);
    }

    public function getAverageHashrate24Hours()
    {
        $data = file_get_contents('https://api.nanopool.org/v1/eth/avghashratelimited/' . $this->walletNumber . '/24');

        return $this->json2Array($data);
    }

    public function getListWorkders()
    {
        $data = file_get_contents('https://api.nanopool.org/v1/eth/workers/' . $this->walletNumber);

        return $this->json2Array($data);
    }

    public function getHistoryHashrate()
    {
        $data = file_get_contents('https://api.nanopool.org/v1/eth/hashratechart/' . $this->walletNumber);

        return $this->json2Array($data);
    }

    public function getCurrentHashrate()
    {
        $data = file_get_contents('https://api.nanopool.org/v1/eth/hashrate/' . $this->walletNumber);

        return $this->json2Array($data);
    }

    public function getGeneralInfo()
    {
        $data = file_get_contents('https://api.nanopool.org/v1/eth/user/' . $this->walletNumber);

        return $this->json2Array($data);
    }

    public function getWorkerGraphHistory(string $workerId)
    {
        $data = file_get_contents('https://api.nanopool.org/v1/eth/hashratechart/' . $this->walletNumber . '/' . $workerId);

        return $this->json2Array($data);
    }

    public function getAddressPayments()
    {
        $data = file_get_contents('https://api.nanopool.org/v1/eth/payments/' . $this->walletNumber);

        return $this->json2Array($data);
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function retryConnect(string $method, array $args)
    {
        if(!method_exists($this, $method)) {
            throw new \LogicException('No method');
        }

        //TODO: more args need pass
        $response = $this->$method($args[0]);

        if(!$response['status']) {
            return $this->retryConnect($method, $args);
        }

        return $response;
    }

    /**
     * @param string $json2Array
     * @return mixed
     */
    protected function json2Array(string $json2Array)
    {
        return json_decode($json2Array, true);
    }
}