<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topshop_controller extends base_routing_controller
{

    /**
     * 页面不需要menu
     */
    public $nomenu = false;

    public function __construct($app)
    {
        pamAccount::setAuthType('sysshop');
        $this->app = $app;
        $this->sellerId = pamAccount::getAccountId();
        $this->sellerName = pamAccount::getLoginName();
        $this->shopId = app::get('topshop')->rpcCall('shop.get.loginId',array('seller_id'=>$this->sellerId),'seller');
        $action = route::current()->getActionName();
        $actionArr = explode('@',$action);
        if( $actionArr['0'] != 'topshop_ctl_passport' )
        {
            if( !$this->sellerId )
            {
                redirect::action('topshop_ctl_passport@signin')->send();exit;
            }

            if( !$this->shopId &&  $actionArr[0] != 'topshop_ctl_enterapply')
            {
                redirect::action('topshop_ctl_enterapply@apply')->send();exit;
            }
        }
    }

    /**
     * @brief 检查是否登录
     *
     * @return bool
     */
    public function checklogin()
    {
        if($this->sellerId) return true;

        return false;
    }

    /**
     * @brief 错误或者成功输出
     *
     * @param string $status
     * @param stirng $url
     * @param string $msg
     * @param string $method
     * @param array $params
     *
     * @return string
     */
    public function splash($status='success',$url=null,$msg=null,$ajax=true){
        $status = ($status == 'failed') ? 'error' : $status;
        //如果需要返回则ajax
        if($ajax==true||request::ajax()){
            return response::json(array(
                $status => true,
                'message'=>$msg,
                'redirect' => $url,
            ));
        }

        if($url && !$msg){//如果有url地址但是没有信息输出则直接跳转
            return redirect::to($url);
        }
    }

    public function isValidMsg($status)
    {
        $status = ($status == 'true') ? 'true' : 'false';
        $res['valid'] = $status;
        return response::json($res);
    }

    /**
     * @brief 商家中心页面加载，默认包含商家中心头、尾、导航、和左边栏
     *
     * @param string $view  html路径
     * @param stirng $app   html路径所在app
     *
     * @return html
     */
    public function page($view, $pagedata = array())
    {
        $sellerData = shopAuth::getSellerData();
        $topshopPageParams['seller'] = $sellerData;
        $pagedata['shopId'] = $this->shopId;
        $topshopPageParams['path'] = $this->runtimePath;//设置面包屑

        if( $this->contentHeaderTitle )
        {
            $topshopPageParams['contentTitle'] = $this->contentHeaderTitle;
        }

        //当前页面调用的action
        $topshopPageParams['currentActionName']= route::current()->getActionName();

        $menuArr = $this->__getMenu();
        if( $menuArr && !$this->nomenu )
        {
            $topshopPageParams['navbar'] = $menuArr['navbar'];
            $topshopPageParams['sidebar'] = $menuArr['sidebar'];
            $topshopPageParams['allMenu'] = $menuArr['all'];
        }

        $topshopPageParams['view'] = $view;

        $pagedata['topshop'] = $topshopPageParams;

        $pagedata['icon'] =  kernel::base_url(1).'/public/statics/favicon.ico';

        if( !$this->tmplName )
        {
            $this->tmplName = 'topshop/tmpl/page.html';
        }

        return view::make($this->tmplName, $pagedata);
    }

    public function set_tmpl($tmpl)
    {
        $tmplName = 'topshop/tmpl/'.$tmpl.'.html';
        $this->tmplName = $tmplName;
    }

    /**
     * @brief 获取到商家中心的导航菜单和左边栏菜单
     *
     * @return array $res
     */
    private function __getMenu()
    {
        $currentPermission = shopAuth::getSellerPermission();

        $defaultActionName = route::current()->getActionName();
        $shopMenu = config::get('shop');

        $shortcutMenuAction = $this->getShortcutMenu();
        $sidebar['commonUser']['label'] = '常用菜单';
        $sidebar['commonUser']['shortcutMenu'] = true;
        $sidebar['commonUser']['active'] = true; //是否展开
        $sidebar['commonUser']['icon'] = 'glyphicon glyphicon-heart';
        //$sidebar['commonUser']['menu'] = $commonUserMenu;

        foreach( (array)$shopMenu as $menu => $row )
        {
            if( $row['display'] === false ) continue;

            foreach( (array)$row['menu'] as $k=>$params )
            {
                //编辑常用菜单使用
                if( $params['display'] !== false && ( !$currentPermission || in_array($params['as'],$currentPermission )) )
                {
                    $allMenu[$menu]['label'] = $row['label'];
                    if( in_array($params['action'], $shortcutMenuAction) )
                    {
                        $sidebar['commonUser']['menu'][] =  $params;
                        $params['isShortcutMenu'] = true;
                    }

                    $allMenu[$menu]['menu'][] =  $params;
                }

                if($row['shopIndex'] || !$currentPermission || ($params['display'] && in_array($params['as'],$currentPermission) ))
                {
                    if( !$navbar[$menu] )
                    {
                        $navbar[$menu]['label'] = $row['label'];
                        $navbar[$menu]['icon'] = $row['icon'];
                        $navbar[$menu]['action'] = $navbar[$menu]['action'] ?  $navbar[$menu]['action'] : $params['action'];
                        $navbar[$menu]['default'] = false;
                    }
                }

                //如果为当前的路由则高亮
                if( !$navbar[$menu]['default'] && $params['action'] == $defaultActionName && $navbar[$menu] )
                {
                    $navbar[$menu]['default'] = true;
                    $selectMenu = $menu;
                }
            }

            if( !$row['shopIndex'] && $selectMenu ==  $menu)
            {
                foreach( (array)$row['menu'] as $k=>$params )
                {
                    $sidebar[$menu]['active'] = true;
                    $sidebar[$menu]['label'] = $row['label'];
                    $sidebar[$menu]['icon'] = $row['icon'];
                    if( !$currentPermission || in_array($params['as'],$currentPermission) )
                    {
                        $params['default'] = ($params['action'] == $defaultActionName) ? true : false;
                        $sidebar[$menu]['menu'][] =  $params;
                    }
                }
            }
        }

        $res['all'] = $allMenu;
        $res['navbar'] = $navbar;
        $res['sidebar'] = $sidebar;
        return $res;
    }

    public function setShortcutMenu($data)
    {
        return app::get('topshop')->setConf('shortcutMenuAction.'.$this->sellerId, $data);
    }

    public function getShortcutMenu()
    {
        return app::get('topshop')->getConf('shortcutMenuAction.'.$this->sellerId);
    }

    /**
     * 用于指示商家操作者的标志
     * @return array 商家登录用户信息
     */
    public function operator()
    {
        return array(
            'user_type' => 'seller',
            'op_id' => pamAccount::getAccountId(),
            'op_account' => pamAccount::getLoginName(),
        );
    }

}

