<?php
namespace UnionPay;

class UnionPay
{

    const SIGN_CERT_PATH = '/assets/test/acp_test_sign.pfx';

    const VERIFY_CERT_PATH = '/assets/test/acp_test_verify_sign.cer';

    const SIGN_CERT_PWD = '000000';


    static public function sign(&$param) {
        $root = getcwd();

        if (isset($params['signature'])) {
            unset($params['signature']);
        }

        $cert = self::getPfxData($root . self::SIGN_CERT_PATH, self::SIGN_CERT_PWD)['cert'];
        openssl_x509_read($cert);
        $param['certId'] = openssl_x509_parse($cert)['serialNumber'];
        $pfx = self::getPfxData($root . self::SIGN_CERT_PATH, self::SIGN_CERT_PWD)['pkey'];

        $sign_data = self::createUrl($param);
        $sha1_data = sha1($sign_data, false);
        $sign_flag = openssl_sign($sha1_data, $signature, $pfx, OPENSSL_ALGO_SHA1);
        if ($sign_flag) {
            $param['signature'] = base64_encode($signature);
            return true;
        } else
            return false;
    }

    static public function verify($param) {
        $root = getcwd();
        if (!isset($param['signature']) || !is_array($param))
            return false;

        $signature = $param['signature'];
        unset($param['signature']);

        $public_key = file_get_contents($root . self::VERIFY_CERT_PATH);
        $signature = base64_decode($signature);
        $param_str = self::createUrl($param);
        $param_str = sha1($param_str, false);
        $flag = openssl_verify($param_str, $signature, $public_key, OPENSSL_ALGO_SHA1);
        return $flag;
    }

    static protected function createUrl($param, $encode = false) {
        if (!is_array($param) || empty($param))
            return '';

        ksort($param);                                      //sort the array with index

        $encoded_arr = Array();
        if ($encode)                                        //save encoded 'key=value' to encoded_arr
            foreach ($param as $k => $i)
                array_push($encoded_arr, urlencode($k) . '=' . urlencode($i));
        else
            foreach ($param as $k => $i)
                array_push($encoded_arr, $k . '=' . $i);

        return implode('&', $encoded_arr);
    }

    static protected function getCerData($path) {
        $x509data = file_get_contents($path);
        openssl_x509_read($x509data);
        $cert = openssl_x509_parse($x509data);
        return $cert;
    }

    static protected function getPfxData($path, $password) {
        $p12data = file_get_contents($path);
        openssl_pkcs12_read($p12data, $certs, $password);
        return $certs;
    }
}