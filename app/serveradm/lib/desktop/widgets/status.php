<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

class serveradm_desktop_widgets_status implements desktop_interface_widget
{   
    /**
     * 构造方法，初始化此类的某些对象
     * @param object 此应用的对象
     * @return null
     */
    public function __construct()
    {
        $this->app = app::get('serveradm');
    }
    
    /**
     * 获取桌面widgets的标题
     * @param null
     * @return null
     */
    public function get_title()
    {            
        return __("服务器信息");        
    }
    
    /**
     * 获取桌面widgets的html内容
     * @param null
     * @return string html内容
     */
    public function get_html()
    {
        $pagedata['sections'] =  $this->sections();

        $oStatus = kernel::single("serveradm_status");
        $pagedata['cache'] =  $oStatus->getCacheInfo();
        $pagedata['kvstore'] =  $oStatus->getKVStorageInfo();
        $pagedata['db'] =  $oStatus->getDatabaseInfo();
        $pagedata['xhprof'] =  $oStatus->getXHProfStatus();
        $pagedata['server'] =  $oStatus->getServerInfo();

        return view::make('serveradm/desktop/widgets/status.html', $pagedata)->render();
    }
    
    /**
     * 获取页面的当前widgets的classname的名称
     * @param null
     * @return string classname
     */
    public function get_className()
    {        
        return " valigntop status";
    }
    
    /**
     * 显示的位置和宽度
     * @param null
     * @return string 宽度数据
     */
    public function get_width()
    {          
        return "l-2";        
    }
    
    // sesctions
    private function sections(){
        return array(
                    array(
                        "name"=>__("缓存信息"),
                        "file"=>"serveradm/desktop/widgets/cache_status.html",
                    ),
                    array(
                        "name"=>__("数据库信息"),
                        "file"=>"serveradm/desktop/widgets/db_status.html",
                    ),
                    array(
                        "name"=>__("服务器信息"),
                        "file"=>"serveradm/desktop/widgets/server_status.html",
                    ),
                    array(
                        "name"=>__("XHPROF"),
                        "file"=>"serveradm/desktop/widgets/xhprof_status.html",
                    ),
        );
    }
}
