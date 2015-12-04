<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class pam_trust_user
{
	/**
	 * session storage
	 *
	 * @var string
	 */
    public $storage = null;

	/**
	 * Create a new Trust user
	 *
	 * @param  mixed  $items
	 * @return void
	 */
    public function __construct()
    {
        kernel::single('base_session')->start();
        $this->storage = new pam_trust_sessionStorage($_SESSION['TRUST']);
    }

    /**
	 * 获取callback url地址
	 *
	 * todo: 目前只支持topc, 没有支持topm 
	 *
	 * @param string $flag
	 * @return string
	 */
    public function getCallbackUrl($flag, $redirectAction)
    {
        return url::action($redirectAction, array('flag' => $flag));
    }

    /**
	 * 获取是信任登陆是否启用
	 *
	 * @return bool
	 */
    public function enabled()
    {
        return app::get('sysuser')->getConf('trustlogin_rule')['status'] == 1 ? true : false;
    }

    /**
	 * 信任登陆callback认证
	 *
	 * @param string $flag
     * @param string $view
     * @param array $params
	 * @return array
	 */
    public function authorize($flag, $view, $redirectAction, $params)
    {
        // 检验state
        if ($this->getStateCode() !== $params['state']) throw new \LogicException(app::get('pam')->_('验证失效'));

        // 二次校验
        // 如果已经跟对应的oauth server验证过, 并且还没过期
        if ($this->storage->get('validated') === true && $this->storage->get('luckymall_expires_in') >= time())
        {
            $res = $this->storage->all();
        }
        else
        {
            // 首次校验
            $res = app::get('pam')->rpcCall('user.trust.authorize',
                                            ['flag' => $flag,
                                             'view' => $view,
                                             'state' => $this->getStateCode(),
                                             'redirect_uri' => $this->getCallbackUrl($flag, $redirectAction),
                                             'params' => json_encode($params)]);

            $this->storage->merge($res);
            // 首次校验通过后, 标识已经验证过, 并标识过期时间20分钟 
            $this->storage->put('validated', true);
            $this->storage->put('luckymall_expires_in', time() + 60 * 20);
        }
        return $res;
    }

    /**
	 * 自生成用户名注册并绑定
	 *
	 * @return int
	 */
    public function bindDefaultCreateUser()
    {
        //$userFlag = $_SESSION['TRUST']['user_flag'];
        $userFlag = $this->storage->get('user_flag');
        $loginName = $userFlag;
        $password = time();

        $userId = userAuth::signUp($loginName, $password, $password);

        $this->bind($userId);
        
        return $userId;
    }

    /**
	 * 自生成用户名注册并绑定
	 *
	 * @return int
	 */
    public function bind($userId)
    {
        $userFlag = $this->storage->get('user_flag', null);
        if ($userFlag === null) throw new \LogicException(app::get('pam')->_('数据过期或处理异常'));
        
        kernel::single('sysuser_passport_trust_trust')->bind($userId, $userFlag);

        // 绑定后设置
        $this->storage->put('user_id', $userId);
        $this->storage->put('binded', true);
    }

    /**
	 * 获取state
	 *
	 * @return int
	 */
    public function getStateCode()
    {
        return $this->storage->get('TRUST_STATE_CODE');
    }

    /**
	 * 生成state并保存
	 *
	 * @return int
	 */
    public function generateStateCode()
    {
        $state = md5(uniqid(rand(), TRUE));
        
        $this->storage->put('TRUST_STATE_CODE', $state);
        
        return $state;
    }

    /**
	 * 获取TRUST列表
	 *
	 * param string $view
	 * param string $redirectAction
	 * @return int
	 */
    public function getTrustInfoList($view = 'web', $redirectAction)
    {
        $this->storage->clear();
        $trustManager = kernel::single('sysuser_passport_trust_manager');

        $state = $this->generateStateCode();
        $trustCollection = collect($trustManager->getTrusts());
        $trustCollection = $trustCollection->filter(function ($trust) {
            return $trust->getStatus() == 1;
        });
        $trustCollection->each(function ($trust) use ($view, $state, $redirectAction, &$trustInfoList) {
            $flag = $trust->getFlag();
            $callbackUrl = $this->getCallbackUrl($flag, $redirectAction);
            $trust->setCallbackUrl($callbackUrl)
                  ->setView($view);
            $trustInfoList[] = $trust->getFrontInfo($state);
        });
        return $trustInfoList;
    }
}
