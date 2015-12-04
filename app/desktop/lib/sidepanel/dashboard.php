<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_sidepanel_dashboard{

    function __construct($app){
        $this->app = $app;
    }
####根据工作组显示侧边栏菜单
    function get_output(){ 
        $act = app::get('desktop')->model('menus')->getList(
            'menu_id,app_id,menu_title,menu_path,workground',
            array('menu_type'=>'workground','disabled'=>0)
        );
        $user = kernel::single('desktop_user');  
        if($user->is_super()){
            $aData = $act;
        }
        else{
            $group = $user->group();
            $meuns = app::get('desktop')->model('menus');
            $data = array();
            foreach($group as $key=>$val){
            $aTmp = $meuns->workgroup($val);
               foreach($aTmp as $val ){
               $data[] =$val;
          }
      }
            $aData = $data;
        }
        $menu_id = array();
        $wrokground = array(); 
        foreach((array)$aData as $value){
            if(!in_array($value['menu_id'],(array)$menu_id)){
                $workground[] = $value;
            }
            $menu_id[] = $value['menu_id'];
        }
        $pagedata['actions'] = $workground;
        $pagedata['side'] = "sidepanel";
        return view::make('desktop/sidepanel.html', $pagedata)->render();
    }
}
