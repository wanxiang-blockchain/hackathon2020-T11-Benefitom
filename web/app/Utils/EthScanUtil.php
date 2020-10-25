<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2018-12-24
 * Time: 17:09
 */

namespace App\Utils;


use App\Helpers\Ethdecoder\InputDataDecoder;

class EthScanUtil
{
    const URL = 'http://api.etherscan.io/api';
    const APP_TOKEN = 'V72IGP54CWSNI1UXR93TQP46NQ79VSWGAY';

    const ABI = '[{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_value","type":"uint256"}],"name":"approve","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transferFrom","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"version","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_owner","type":"address"}],"name":"balanceOf","outputs":[{"name":"balance","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transfer","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_value","type":"uint256"},{"name":"_extraData","type":"bytes"}],"name":"approveAndCall","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"_owner","type":"address"},{"name":"_spender","type":"address"}],"name":"allowance","outputs":[{"name":"remaining","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"inputs":[{"name":"_initialAmount","type":"uint256"},{"name":"_tokenName","type":"string"},{"name":"_decimalUnits","type":"uint8"},{"name":"_tokenSymbol","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"name":"_from","type":"address"},{"indexed":true,"name":"_to","type":"address"},{"indexed":false,"name":"_value","type":"uint256"}],"name":"Transfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"_owner","type":"address"},{"indexed":true,"name":"_spender","type":"address"},{"indexed":false,"name":"_value","type":"uint256"}],"name":"Approval","type":"event"}]';

    public static function fetchTransatcionStatus($id)
    {
        $res = @file_get_contents(self::URL . '?module=transaction&action=getstatus&txhash=' . $id . '&apikey=' . self::APP_TOKEN);
        if (!$res){
            return null;
        }
        \Log::debug($id, json_decode($res, true));
        return json_decode($res, true);
    }

    public static function fetchTransatcion($id)
    {
        $data = [
            "jsonrpc" => "2.0",
            "method" => "eth_getTransactionByHash",
            "params" => [
                $id
            ],
            "id" => 1
        ];
        try{
            $res = HttpUtil::json('https://mainnet.infura.io/v3/afa4ff38b56e430aba274aae97abbad2', json_encode($data));
            if (!$res){
                return null;
            }
            $res2 = json_decode($res, true);
            if (!$res2){
                return null;
            }
            if (empty($res2['result']['blockNumber'])){
                // 未上链
                return null;
            }
            $inputData = $res2['result']['input'];
            $abiArray = json_decode(self::ABI);
            $decoder = new InputDataDecoder($abiArray);
            $decoded = $decoder->decodeData($inputData);
            if (empty($decoded->inputs)) {
                return null;
            }
            $decoded->inputs[0] = substr($decoded->inputs[0], -40);
            $decoded->inputs[1] = hexdec($decoded->inputs[1]) / 1.0E+18;
            return $decoded;
        }catch (\Exception $e){
            \Log::error($e->getTraceAsString());
            return null;
        }
    }

    public static function getTransactionReceipt($id)
    {
        $data = [
            "jsonrpc" => "2.0",
            "method" => "eth_getTransactionReceipt",
            "params" => [
                $id
            ],
            "id" => 1
        ];
        try{
            $res = HttpUtil::json('https://mainnet.infura.io/v3/afa4ff38b56e430aba274aae97abbad2', json_encode($data));
            if (!$res){
                return null;
            }
            $res2 = json_decode($res, true);
            if (!$res2){
                return null;
            }
            if (empty($res2['result'])){
                // 未上链
                return null;
            }
            return $res2['result'];
        }catch (\Exception $e){
            \Log::error($e->getTraceAsString());
            return null;
        }
    }
}
