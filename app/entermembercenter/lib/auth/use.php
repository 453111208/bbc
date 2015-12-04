<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class entermembercenter_auth_use
{
	public function pre_auth_uses()
	{
		if(!base_enterprise::ent_id() || !base_enterprise::ent_ac() || !base_enterprise::ent_email()){
			//判断数据是否是中心过来的
			return false;
		}else{
			base_enterprise::set_version();
			base_enterprise::set_token();
			if (!base_enterprise::is_valid('json',base_enterprise::ent_id())){
				return false;
			}
		}

		return true;
	}

	public function pre_ceti_use()
	{
		if(!base_certificate::certi_id() || !base_certificate::token()){
			return false;
		}

		return true;
	}

	public function login_verify()
	{
        #    $lastcheck = app::get('entermembercenter')->getConf('last_certifi_check');
        #    if($lastcheck && time() <= (int) $lastcheck + 30 * 24 * 60 * 60) {
        #        return '';
        #    }

		if (!$this->pre_auth_uses()&&!$this->pre_ceti_use()){
            $active_url = url::route('shopadmin', ['app' => 'entermembercenter', 'ctl' => 'register']);
			header('Location: '.$active_url);exit;
		}elseif (!$this->pre_auth_uses()){
			$pagedata['enterprise_url'] = config::get('link.shop_user_enterprise');
            $pagedata['callback_url'] = base64_encode(url::route('shopadmin', ['app' => 'entermembercenter', 'ctl' => 'register', 'act' => 'active']));
            return view::make('entermembercenter/login_verify.html', $pagedata)->render();
		}else{
            //app::get('entermembercenter')->setConf('last_certifi_check', time());
			return '';
		}
	}

	public function active_top_html()
	{
		/** 获取证书，企业号的验证 **/
        $active_url = url::route('shopadmin', ['app' => 'entermembercenter', 'ctl' => 'register']);
		$pagedata['active_url'] = $active_url;
		return view::make('entermembercenter/desktop_active_top.html', $pagedata)->render();
	}
}
