<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */

class desktop_cert_certcheck
{
	function __construct($app)
        {
		$this->app = $app;
	}
    function check($app)
    {
		$opencheck = false;
		$objCertchecks = kernel::servicelist("desktop.cert.check");
        foreach ($objCertchecks as $objCertcheck)
        {
            if(method_exists($objCertcheck , 'certcheck') && $objCertcheck->certcheck()){
				$opencheck = true;
				break;
			}
        }
	    if(!$opencheck || $this->is_internal_ip() || $this->is_demosite()) return ;

        $activation_arr = app::get('desktop')->getConf('activation_code');
        logger::info('activation_code:'.var_export($activation_arr,1));
		if($activation_arr) return ;
		else
		{
			echo $this->error_view();
			exit;
		}
    }

	function getform()
	{
		$pagedata['res_url'] = $this->app->res_url;
		$pagedata['auth_error_msg'] = $auth_error_msg;
		return view::make('desktop/active_code_form.html', $pagedata);
	}

    function error_view($auth_error_msg=null)
    {
		$pagedata['res_url'] = $this->app->res_url;
		$pagedata['auth_error_msg'] = $auth_error_msg;
		return view::make('desktop/active_code.html', $pagedata);
	}
	/**
	  *		ocs :
	  * 	$method = 'active.do_active'
	  *		$ac = 'SHOPEX_ACTIVE'
	  *
	  *		其它产品默认
	  */
	function check_code($code=null,$method='oem.do_active',$ac = 'SHOPEX_OEM')
	{
		if(!$code)return false;
		$certificate_id = base_certificate::certi_id();
		if(!$certificate_id)base_certificate::register();
		$certificate_id = base_certificate::certi_id();
		$token =  base_certificate::token();
        $data = array(
            'certi_app'=>$method,
            'certificate_id'=>$certificate_id,
            'active_key'=>$_POST['auth_code'],
            'ac'=>md5($certificate_id.$ac)
        );
        logger::info("LICENSE_CENTER_INFO:".print_r($data,1));
		$result = kernel::single('base_httpclient')->post(config::get('link.license_center'),$data);
        logger::info("LICENSE_CENTER_INFO:".print_r($result,1));
		$result = json_decode($result,true);
		return $result;
	}

	function check_certid()
	{
		$params['certi_app'] = 'open.login';
        $this->Certi = base_certificate::get('certificate_id');
        $this->Token = base_certificate::get('token');
        $params['certificate_id']  = $this->Certi;
        $params['format'] = 'json';
        /** 增加反查参数result和反查基础地址url **/
        $code = md5(microtime());
        base_kvstore::instance('ecos')->store('net.login_handshake',$code);
        $params['result'] = $code;
		$obj_apps = app::get('base')->model('apps');
        $tmp = $obj_apps->getList('*',array('app_id'=>'base'));
        $app_xml = $tmp[0];
        $params['version'] = $app_xml['local_ver'];
        $params['url'] = kernel::base_url(1);
        /** end **/
        $token = $this->Token;
        $str   = '';
        ksort($params);
        foreach($params as $key => $value){
            $str.=$value;
        }
        $params['certi_ac'] = md5($str.$token);
        $http = kernel::single('base_httpclient');
        $http->set_timeout(20);
        $result = $http->post(config::get('link.license_center'),$params);
        $api_result = stripslashes($result);
        $api_arr = json_decode($api_result,true);
		return $api_arr;
	}
	function listener_login($params)
	{
		$opencheck = false;
		$objCertchecks = kernel::servicelist("desktop.cert.check");
        foreach ($objCertchecks as $objCertcheck)
        {
            if(method_exists($objCertcheck , 'certcheck') && $objCertcheck->certcheck()){
				$opencheck = true;
				break;
			}
        }
	    if(!$opencheck || $this->is_internal_ip() || $this->is_demosite()) return ;

		if($params['type'] === pamAccount::getAuthType('desktop'))
		{
			$result = $this->check_certid();
			if($result['res'] == 'succ' && $result['info']['valid'])
			{
				return ;
			}
            else
            {
                unset($_SESSION['account'][$params['type']]);
                switch($result['msg']){
                    case "invalid_version":
                        $msg = "版本号有误，查看mysql是否运行正常"; break;
                    case "RegUrlError":
                        $msg = "你当前使用的域名与激活码所绑定的域名不一致。</br>如果你确认需要更改域名，请将“老域名”，“新域名”，“shopexid”，“激活码”发送至邮箱：ecstore_service@shopex.cn</br>如果不是更改域名，请使用激活码所绑定的域名来登陆ECstore。</br>"; break;
                    case "SessionError":
                        $msg = "中心请求网店API失败!请找服务商或自行检测网络，保证网络正常。"; break;
                    case "license_error":
                        $msg = "证书号错误!请确认config/certi.php文件真的存在！"; break;
                    case "method_not_exist":
                        $msg = "接口方法不存在!"; break;
                    case "method_file_not_exist":
                        $msg = "接口文件不存在!"; break;
                    case "NecessaryArgsError":
                        $msg = "缺少必填参数!"; break;
                    case "ProductTypeError":
                        $msg = "产品类型错误!"; break;
                    case "UrlFormatUrl":
                        $msg = "URL格式错误!"; break;
                    case "invalid_sign":
                        $msg = "验签错误!"; break;
                    default:
                        $msg = null;break;
                }
                if($result == null){
                    $msg = "请检测您的服务器域名解析是否正常！";
                }

                $pagedata['msg'] = ($msg)?$msg:"";
                $pagedata['url'] = $url = url::route('shopadmin');
                $pagedata['code_url'] = url::route('shopadmin', array('app' => 'desktop', 'ctl' => 'code', 'act' => 'error_view'));
                return view::make('desktop/codetip.html', $pagedata);
            }
        }

	}

    /*
     * 检测当环境是外网demo站点时的跳过激活检测
     */
    function is_demosite(){
        if(defined('DEV_CHECKDEMO') && DEV_CHECKDEMO){
            return true;
        }
    }

	function is_internal_ip()
	{
        $ip = $this->remote_addr();
        if($ip=='127.0.0.1' || $ip=='::1'){
            return true;
        }

		$ip = ip2long($ip);
		$net_a = ip2long('10.255.255.255') >> 24; //A类网预留ip的网络地址
		$net_b = ip2long('172.31.255.255') >> 20; //B类网预留ip的网络地址
		$net_c = ip2long('192.168.255.255') >> 16; //C类网预留ip的网络地址
		return $ip >> 24 === $net_a || $ip >> 20 === $net_b || $ip >> 16 === $net_c;
    }


	function remote_addr()
	{
		if(!isset($GLOBALS['_REMOTE_ADDR_'])){
			$addrs = array();

			if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
				foreach( array_reverse( explode( ',',  $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) as $x_f )
				{
					$x_f = trim($x_f);

					if ( preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $x_f ) )
					{
						$addrs[] = $x_f;
					}
				}
			}

			$GLOBALS['_REMOTE_ADDR_'] = isset($addrs[0])?$addrs[0]:$_SERVER['REMOTE_ADDR'];
		}
		return $GLOBALS['_REMOTE_ADDR_'];
	}
}
