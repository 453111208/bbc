<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

interface sysuser_interface_trust
{
    public function getLogoUrl();

    public function setCallbackUrl($url);
    
    public function getCallbackUrl();
    
    public function setView($view);

    public function getView();

    /**
	 * 获取access token
	 *
	 * @param string $state
	 * @return string
	 */
    public function getAccessToken($code = null);

    public function getOpenId();

    public function getUserInfo();

    public function getUserFlag();

    public function generateAccessToken($code);

    public function generateOpenId();

    public function generateUserInfo();

    /**
	 * 获取plugin autherize url
	 *
	 * @param string $state
	 * @return string
	 */
    public function getAuthorizeUrl($state);
}
