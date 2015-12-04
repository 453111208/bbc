<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topc_controller extends base_routing_controller
{

    /**
     * 定义当前平台
     */
    public $platform = 'pc';

    /**
     * 控制器指定的布局(layout), 具体到文件
     *
     * @var \Illuminate\View\View
     */
    private $layout = null;

    /**
     * 控制器指定的布局标识, 会调取用户配置, 以决定最终应用的布局.
     *
     * @var \Illuminate\View\View
     */
    private $layoutFlag = 'default';

    public function __construct()
    {
    }

    protected function setLayout($layout)
    {
        $this->layout = $layout;
    }

    protected function setLayoutFlag($layoutFlag)
    {
        $this->layoutFlag = $layoutFlag;
    }


    public function set_cookie($name,$value,$expire=false,$path=null){
        if(!$this->cookie_path){
            $this->cookie_path = kernel::base_url().'/';
        }
        $this->cookie_life = $this->cookie_life > 0 ? $this->cookie_life : 315360000;
        $expire = $expire === false ? time()+$this->cookie_life : $expire;
        setcookie($name,$value,$expire,$this->cookie_path);
        $_COOKIE[$name] = $value;
    }
    /**
     * page
     *
     * @param  boolean $realpath
     * @return base_view_object_interface | string
     */
    public function page($view = null, $data = array())
    {
        $themeName = ($params['theme'])?$params['theme']:kernel::single('site_theme_base')->get_default('pc');
        $theme = theme::uses($themeName);
        $layout = $this->layout;
        if (!$layout)
        {
            $layoutFlag = !is_null($this->layoutFlag) ? $this->layoutFlag : 'defalut';
            $tmplObj = kernel::single('site_theme_tmpl');
            $layout = $tmplObj->get_default($this->layoutFlag, $themeName);
            $layout = $layout ? $layout : (($tmpl_default = $tmplObj->get_default('default', $themeName)) ? $tmpl_default : 'default.html');
        }
        $theme->layout($layout);

        if (! is_null($view))
        {
            $theme->of($view, $data);
        }
        return $theme->render();
    }

    /*
     * 结果处理
     * @var string $status
     * @var string $url
     * @var string $msg
     * @var boolean $ajax
     * @var array $data
     * @access public
     * @return void
     */

    public function splash($status='success', $url=null , $msg=null,$ajax=false){
        $status = ($status == 'failed') ? 'error' : $status;
        //如果需要返回则ajax
        if($ajax==true||request::ajax()){
            //status: error/success
            return response::json(array(
                $status => true,
                'message'=>$msg,
                'redirect' => $url,
            ));
        }

        if($url && !$msg){//如果有url地址但是没有信息输出则直接跳转
            return redirect::action($url);
        }

        $this->setLayoutFlag('splash');
        $pagedata['msg'] = $msg;
        return $this->page('topc/splash/error.html', $pagedata);
    }

    /**
     * 用于指示卖家操作者的标志
     * @return array 买家登录用户信息
     */
    public function operator()
    {
        return array(
            'account_type' => 'buyer',
            'op_id' => userAuth::id(),
            'op_account' => userAuth::getLoginName(),
        );
    }

}


