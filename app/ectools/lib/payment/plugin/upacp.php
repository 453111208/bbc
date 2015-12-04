<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * upacp银联（unipay）
 * @auther shopex ecstore dev dev@shopex.cn
 * @version 0.1
 * @package ectools.lib.payment.plugin
 */
final class ectools_payment_plugin_upacp extends ectools_payment_app implements ectools_interface_payment_app {

	/**
	 * @var string 支付方式名称
	 */
    public $name = '中国银联网关支付（新）';
    /**
     * @var string 支付方式接口名称
     */
    public $app_name = 'upacp';
     /**
     * @var string 支付方式key
     */
    public $app_key = 'upacp';
	/**
	 * @var string 中心化统一的key
	 */
	public $app_rpc_key = 'upacp';
	/**
	 * @var string 统一显示的名称
	 */
    public $display_name = '中国银联网关支付（新）';
    /**
	 * @var string 货币名称
	 */
    public $curname = 'CNY';
    /**
	 * @var string 当前支付方式的版本号
	 */
    public $ver = '1.0';
    /**
     * @var string 当前支付方式所支持的平台
     */
    public $platform = 'ispc';
    public $supportCurrency = array("CNY"=>"01");
    public $sdk_front_trans_url = 'https://gateway.95516.com/gateway/api/frontTransReq.do';
    //测试站点
    //public $sdk_front_trans_url = 'https://101.231.204.80:5000/gateway/api/frontTransReq.do';
    public $version = '5.0.0';


    /**
	 * 构造方法
	 * @param null
	 * @return boolean
	 */
    public function __construct($app){
		parent::__construct($app);
        //$this->callback_url = $this->app->base_url(true)."/apps/".basename(dirname(__FILE__))."/".basename(__FILE__);
		$this->notify_url = kernel::openapi_url('openapi.ectools_payment/parse/' . $this->app->app_id . '/ectools_payment_plugin_upacp', 'callback');
		if (preg_match("/^(http):\/\/?([^\/]+)/i", $this->notify_url, $matches))
		{
			$this->notify_url = str_replace('http://','',$this->notify_url);
			$this->notify_url = preg_replace("|/+|","/", $this->notify_url);
			$this->notify_url = "http://" . $this->notify_url;


		}
		else
		{
			$this->notify_url = str_replace('https://','',$this->notify_url);
			$this->notify_url = preg_replace("|/+|","/", $this->notify_url);
			$this->notify_url = "https://" . $this->notify_url;
		}

		$this->callback_url = kernel::openapi_url('openapi.ectools_payment/parse/' . $this->app->app_id . '/ectools_payment_plugin_upacp', 'callback');
		if (preg_match("/^(http):\/\/?([^\/]+)/i", $this->callback_url, $matches))
		{
			$this->callback_url = str_replace('http://','',$this->callback_url);
			$this->callback_url = preg_replace("|/+|","/", $this->callback_url);
			$this->callback_url = "http://" . $this->callback_url;
		}
		else
		{
			$this->callback_url = str_replace('https://','',$this->callback_url);
			$this->callback_url = preg_replace("|/+|","/", $this->callback_url);
			$this->callback_url = "https://" . $this->callback_url;
		}

    }

    /**
     * 后台支付方式列表关于此支付方式的简介
     * @param null
     * @return string 简介内容
     */
    public function admin_intro(){
        return '中国银联支付（UnionPay）是银联电子支付服务有限公司主要从事以互联网等新兴渠道为基础的网上支付。（适用于中国银联的新签约用户）';
    }

     /**
     * 后台配置参数设置
     * @param null
     * @return array 配置参数列表
     */
    public function setting(){
        return array(
                    'pay_name'=>array(
                        'title'=>app::get('ectools')->_('支付方式名称'),
                        'type'=>'string',
						'validate_type' => 'required',
                    ),
                    'mer_id'=>array(
                        'title'=>app::get('ectools')->_('商户代码'),
                        'type'=>'string',
                        'validate_type' => 'required',
                    ),
                    'sdk_sign_cert_path'=>array(
                        'title'=>app::get('ectools')->_('签名证书'),
                        'type'=>'file',
                        'validate_type' => 'required',
                        'label'=>app::get('ectools')->_('文件后缀名为.pfx'),
                    ),
                    'sign_cert_pwd'=>array(
                        'title'=>app::get('ectools')->_('签名证书密码'),
                        'type'=>'string',
						'validate_type' => 'required',
                    ),

                    'order_by' =>array(
                        'title'=>app::get('ectools')->_('排序'),
                        'type'=>'string',
                        'label'=>app::get('ectools')->_('整数值越小,显示越靠前,默认值为1'),
                    ),

                    'pay_fee'=>array(
                        'title'=>app::get('ectools')->_('交易费率'),
                        'type'=>'pecentage',
						'validate_type' => 'number',
                    ),
                    'pay_brief'=>array(
                        'title'=>app::get('ectools')->_('支付方式简介'),
                         'type'=>'textarea',
                    ),
                    'pay_desc'=>array(
                        'title'=>app::get('ectools')->_('描述'),
                        'type'=>'html',
						'includeBase' => true,
                    ),
					'pay_type'=>array(
						'title'=>app::get('ectools')->_('支付类型(是否在线支付)'),
                        'type'=>'radio',
                        'options'=>array('false'=>app::get('ectools')->_('否'),'true'=>app::get('ectools')->_('是')),
                        'name' => 'pay_type',
					),
                    'support_cur'=>array(
                        'title'=>app::get('ectools')->_('支持币种'),
                        'type'=>'text hidden cur',
                        'options'=>$this->arrayCurrencyOptions,
                    ),
					'status'=>array(
						'title'=>app::get('ectools')->_('是否开启此支付方式'),
						'type'=>'radio',
						'options'=>array('false'=>app::get('ectools')->_('否'),'true'=>app::get('ectools')->_('是')),
						'name' => 'status',
					),
                );
    }
	/**
     * 前台支付方式列表关于此支付方式的简介
     * @param null
     * @return string 简介内容
     */
    public function intro(){
        return '中国银联支付（UnionPay）是银联电子支付服务有限公司主要从事以互联网等新兴渠道为基础的网上支付。';
    }

	/**
     * 提交支付信息的接口
     * @param array 提交信息的数组
     * @return mixed false or null
     */
    public function dopay($payment){
        // 初始化日志
        $this->sdk_sign_cert_pwd = $this->getConf('sign_cert_pwd', __CLASS__);
        $this->mer_id=$this->getConf('mer_id', __CLASS__);
        $this->sdk_verify_cert_dir = DATA_DIR . '/cert/payment_plugin_upacp/';
        $this->sdk_sign_cert_path = $this->sdk_verify_cert_dir.trim($this->getConf('sdk_sign_cert_path', __CLASS__));
        $params = array(
                'version' => '5.0.0',               //版本号
                'encoding' => 'utf-8',              //编码方式
                'certId' => $this->getSignCertId(),           //证书ID
                'txnType' => '01',              //交易类型
                'txnSubType' => '01',               //交易子类
                'bizType' => '000201',              //业务类型
                'frontUrl' =>  $this->notify_url,        //前台通知地址
                'backUrl' => $this->callback_url,       //后台通知地址
                'signMethod' => '01',       //签名方法
                'channelType' => '07',      //渠道类型，07-PC，08-手机
                'accessType' => '0',        //接入类型
                'merId' => $this->mer_id,               //商户代码，请改自己的测试商户号
                'orderId' => $payment['payment_id'],    //商户订单号
                'txnTime' => date('YmdHis'),    //订单发送时间
                'txnAmt' => number_format($payment['cur_money'],2,".","")*100,      //交易金额，单位分
                'currencyCode' => '156',    //交易币种
                'defaultPayType' => '0001', //默认支付方式
                //'orderDesc' => '订单描述',  //订单描述，网关支付和wap支付暂时不起作用
                'reqReserved' =>' 透传信息', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
                );
        $this->sign ( $params );
        $front_uri = $this->sdk_front_trans_url;
        $html_form = $this->create_html ( $params, $front_uri );
        echo $html_form ;
        exit;


    }

    public function getSignCertId (){
            return $this->getCertId ( $this->sdk_sign_cert_path  );
    }

    public function getCertId($cert_path) {
        $pkcs12certdata = file_get_contents ( $cert_path );
        openssl_pkcs12_read ( $pkcs12certdata, $certs, $this->sdk_sign_cert_pwd );
        $x509data = $certs ['cert'];
        openssl_x509_read ( $x509data );
        $certdata = openssl_x509_parse ( $x509data );
        $cert_id = $certdata ['serialNumber'];
        return $cert_id;
    }

    /**
     * 签名
     *
     * @param String $params_str
     */
    public function sign(&$params) {

        if(isset($params['transTempUrl'])){
            unset($params['transTempUrl']);
        }
        // 转换成key=val&串
        $params_str = $this->coverParamsToString ( $params );
        $params_sha1x16 = sha1 ( $params_str, FALSE );
        // 签名证书路径
        $cert_path = $this->sdk_sign_cert_path;
        $private_key = $this->getPrivateKey ( $cert_path );
        //var_dump($cert_path,$private_key);exit;
        // 签名
        $sign_falg = openssl_sign ( $params_sha1x16, $signature, $private_key, OPENSSL_ALGO_SHA1 );
        if ($sign_falg) {
            $signature_base64 = base64_encode ( $signature );
            $params ['signature'] = $signature_base64;
        } else {
              echo  "签名失败";
              exit;
        }

    }

    public function coverParamsToString($params) {
        $sign_str = '';
        // 排序
        ksort ( $params );
        foreach ( $params as $key => $val ) {
            if ($key == 'signature') {
                continue;
            }
            $sign_str .= sprintf ( "%s=%s&", $key, $val );
        }
        return substr ( $sign_str, 0, strlen ( $sign_str ) - 1 );
    }

    public function getPrivateKey($cert_path) {
        $pkcs12 = file_get_contents ( $cert_path );
        openssl_pkcs12_read ( $pkcs12, $certs, $this->sdk_sign_cert_pwd );
        return $certs ['pkey'];
    }

    public function create_html($params, $action) {

        $encodeType = isset ( $params ['encoding'] ) ? $params ['encoding'] : 'UTF-8';
        $html = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset={$encodeType}\" /></head><body onload=\"javascript:document.pay_form.submit();\"><form id=\"pay_form\" name=\"pay_form\" action=\"{$action}\" method=\"post\">";
        foreach ( $params as $key => $value ) {
            $html .= "    <input type=\"hidden\" name=\"{$key}\" id=\"{$key}\" value=\"{$value}\" />\n";
        }
        $html .= '<input type="submit" type="hidden"></form></body></html>';
        return $html;
    }

    /**
     * 生成支付表单 - 自动提交
     * @params null
     * @return null
     */
    public function gen_form()
    {
    }

    /**
	 * 校验方法
	 * @param null
	 * @return boolean
	 */
    public function is_fields_valiad(){
        return true;
    }

	/**
	 * 支付后返回后处理的事件的动作
	 * @params array - 所有返回的参数，包括POST和GET
	 * @return null
	 */
    public function callback(&$recv)
	{
        $this->sdk_sign_cert_pwd = $this->getConf('sign_cert_pwd', __CLASS__);
        $this->mer_id=$this->getConf('mer_id', __CLASS__);
        $this->sdk_verify_cert_dir = DATA_DIR . '/cert/payment_plugin_upacp/';

        $this->sdk_sign_cert_path = $this->sdk_verify_cert_dir.trim($this->getConf('sdk_sign_cert_path', __CLASS__));
        if($this->is_return_vaild($recv) === 1){
            if($recv['respMsg']=='success' || $recv['respMsg'] == 'Success!' ){
                $ret['payment_id'] = $recv['orderId'];
                $ret['account'] = $mer_id;
                $ret['currency'] = 'CNY';
                $ret['money'] = number_format(($recv['txnAmt']/100),2,".","");
                $ret['paycost'] = '0.000';
                $ret['cur_money'] = $ret['money'];
                $ret['t_payed'] = strtotime($recv['notify_time']) ? strtotime($recv['notify_time']) : time();
                $ret['pay_app_id'] = "upacp";
                $ret['pay_type'] = 'online';
                $ret['status'] = 'succ';

             }
            else{
                $message = 'fail';
                $ret['status'] = 'invalid';
            }
        }else{
            $message = 'Invalid Sign';
            $ret['status'] = 'invalid';
        }

		return $ret;
    }

    public function is_return_vaild($params) {
        // 公钥
        $public_key = $this ->getPulbicKeyByCertId ( $params ['certId'] );
        if(empty($public_key))
        {
            echo 'key_error';
            return null;
        }

        // 签名串
        $signature_str = $params ['signature'];
        unset ( $params ['signature'] );
        $params_str = $this->coverParamsToString ( $params );
        $signature = base64_decode ( $signature_str );
        $params_sha1x16 = sha1 ( $params_str, FALSE );
        $isSuccess = openssl_verify ( $params_sha1x16, $signature,$public_key, OPENSSL_ALGO_SHA1 );
        return $isSuccess;
    }

    public function getPulbicKeyByCertId($certId) {
        // 证书目录

        $filePath = ROOT_DIR . "/app/ectools/lib/payment/plugin/upacp/" . "UpopRsaCert.cer";
        if ($this->getCertIdByCerPath ( $filePath ) == $certId) {
            return  $this->getPublicKey ( $filePath );
         }
         else
         {
             return null;
         }

    }

    /**
     * 取证书ID(.cer)
     *
     * @param unknown_type $cert_path
     */
    public function getCertIdByCerPath($cert_path) {
        $x509data = file_get_contents ( $cert_path );
        openssl_x509_read ( $x509data );
        $certdata = openssl_x509_parse ( $x509data );
        $cert_id = $certdata ['serialNumber'];
        return $cert_id;
    }

    /**
     * 取证书公钥 -验签
     *
     * @return string
     */
    public function getPublicKey($cert_path) {
        return file_get_contents ( $cert_path );
    }


}
