<?php

class base_rpc_validate
{
    static public function isValidate($params)
    {
        $token     = base_shopnode::token();
        $sign_type = $params['sign_type'];
        $sign      = $params['sign'];
        unset($params['sign']);
        return ( $sign_type == "MD5" && $sign == self::sign($params, $token) );
    }


    static public function sign($params, $token) {
        return strtoupper(md5(strtoupper(md5(self::assemble($params))).$token));
    }

    static public function assemble($params) {
        if(!is_array($params))  return null;
        ksort($params, SORT_STRING);
        $sign = '';
        foreach($params AS $key=>$val){
            if(is_null($val))   continue;
            if(is_bool($val))   $val = ($val) ? 1 : 0;
            $sign .= $key . (is_array($val) ? self::assemble($val) : $val);
        }
        return $sign;
    }
}

