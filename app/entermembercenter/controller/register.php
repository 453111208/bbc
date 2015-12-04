<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class entermembercenter_ctl_register extends base_routing_controller
{

    function index()
	{

        $pagedata['conf'] = base_setup_config::deploy_info();
		$pagedata['enterprise_url'] = config::get('link.shop_user_enterprise');
		//$pagedata['callback_url'] = base64_encode(app::get('desktop')->base_url(1).'?app=entermembercenter&ctl=register&act=active');
        $pagedata['callback_url'] = base64_encode(url::route('shopadmin', ['app' => 'entermembercenter', 'ctl' => 'register', 'act' => 'active']));
		$output = view::make('entermembercenter/register.html', $pagedata)->render();
		return str_replace('%BASE_URL%',kernel::base_url(1),$output);
    }

	function active()
	{
		if(($_GET['ent_id'] && $_GET['ent_ac'] &&  $_GET['ent_sign'] && $_GET['ent_email'])){
			//判断数据是否是中心过来的
			if(md5($_GET['ent_id'] . $_GET['ent_ac'] . 'ShopEXUser')==$_GET['ent_sign']){
				//检测企业帐号是否正确
				base_enterprise::set_version();
				base_enterprise::set_token();
				if (!base_enterprise::is_valid('json',$_GET['ent_id'])){
					header("Content-type: text/html; charset=utf-8");
                    $active_url = url::route('shopadmin', array('app' => 'entermembercenter', 'ctl' => 'register'));
                    header('Location:'.$active_url);exit;
				}else{
					$arr_enterprise = array(
						'ent_id'=>$_GET['ent_id'],
						'ent_ac'=>$_GET['ent_ac'],
						'ent_email'=>$_GET['ent_email'],
					);
					base_enterprise::set_enterprise_info($arr_enterprise);
					if (!base_certificate::certi_id()|| !base_certificate::token()){
						base_certificate::register();
					}
					if (!base_shopnode::node_id()&&base_certificate::certi_id()&&base_certificate::token()){
						$obj_buildin = kernel::single('base_shell_buildin');
						$obj_buildin->command_active_node_id('ceti_node_id');
					}
				}
			}
		}else{
			header("Content-type: text/html; charset=utf-8");
            $active_url = url::route('shopadmin', ['app' => 'entermembercenter', 'ctl' => 'register']);
			header('Location:'.$active_url);exit;
		}

        $url = url::route('shopadmin');
		$url = base64_encode($url);
		$login_html = '?ctl=passport&act=index&url='.$url;
		header("Content-type: text/html; charset=utf-8");
		header('Location:'.$login_html);exit;
	}
}

