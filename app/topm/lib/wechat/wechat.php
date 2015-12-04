<?php
class topm_wechat_wechat{
    public function get_code($appid, $redirect_uri, $response_type='code', $scope='snsapi_base', $state='STATE')
    {
        $api_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
        $api_url = $api_url.'appid='.$appid.'&redirect_uri='.urlencode($redirect_uri).'&response_type='.$response_type.'&scope='.$scope.'&state='.$state.'#wechat_redirect';
        header('Location:'.$api_url);
        exit;
    }

    public function get_openid_by_code($appid, $secret, $code, $grant_type='authorization_code')
    {
        $result = $this->get_access_token($appid, $secret, $code, $grant_type);
        return $result['openid'];
    }

    public function get_access_token($appid, $secret, $code, $grant_type='authorization_code')
    {
        $api_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
        $api_url = $api_url.'appid='.$appid;
        $api_url = $api_url.'&secret='.$secret;
        $api_url = $api_url.'&code='.$code;
        $api_url = $api_url.'&grant_type='.$grant_type;
        $httpclient = kernel::single('base_httpclient');
        $response = $httpclient->set_timeout(6)->get($api_url);
        $result = json_decode($response, true);
        return $result;
    }

    // 判断是否来自微信浏览器
    public function from_weixin() {
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }

    public function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
