<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysuser_passport_trust_trust
{
    protected $trusts;

    protected $flag;


    public function __construct()
    {
    }

    public function make($flag)
    {
        if (!isset($this->trusts[$flag]))
        {
            $trustClass = 'sysuser_plugin_'.$flag;
            if (!class_exists($trustClass)) throw \ErrorException("class {$trustClass} not exists.");
            $this->trusts[$flag] = kernel::single($trustClass);
        }
        return $this->trusts[$flag];
    }

    /*
    public function authorize($flag, $state, $params, $redirectUri)
    {
        $trust = $this->make($flag);
        $trust->setCallbackUrl($redirectUri);
        $trust->authorize($state, $params);
        return $trust->getUserFlag();
    }
    */
    
    public function binded($userFlag)
    {
        if (!$userFlag) throw new \LogicException('Must be have user flag');
        $trustModel = app::get('sysuser')->model('trustinfo');
        if ($row = $trustModel->getRow('user_id', ['user_flag' => $userFlag]))
        {
            return $row['user_id'];
        }
        return false;
    }

    public function bind($userId, $userFlag)
    {
        $trustModel = app::get('sysuser')->model('trustinfo');
        $data = ['user_id' => $userId, 'user_flag' => $userFlag];
        return $trustModel->insert($data);
    }
    

}
