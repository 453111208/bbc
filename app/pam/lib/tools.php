<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class pam_tools
{
	/**
	 * 验证登录注册用户名类型
	 *
	 * 获取前台注册登录用户类型(用户名,邮箱，手机号码)
	 *
	 * @param  string $account
	 * @return string
	 */
    public function checkLoginNameType($loginName)
    {
        /*if( empty($loginName) )
        {
            throw new \LogicException(app::get('pam')->_('请输入用户名'));
        }*/

        if($loginName && strpos($loginName,'@'))
        {
            if( !preg_match("/^[a-z\d][a-z\d_.]*@[\w-]+(?:\.[a-z]{2,})+$/",$loginName) )
            {
                throw new \LogicException(app::get('pam')->_('请输入正确的邮箱地址'));
            }
            $type = 'email';
        }
        elseif(preg_match("/^1[34578]{1}[0-9]{9}$/",$loginName))
        {
            $type = 'mobile';
        }
        else
        {
            $type = 'login_account';
        }
        return $type;
    }
}