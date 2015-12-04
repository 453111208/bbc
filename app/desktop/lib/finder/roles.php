<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


/* TODO: Add code here */
class desktop_finder_roles{
    var $column_control = '角色操作';
    function __construct($app){
        $this->app= app::get('desktop');
        $this->obj_roles = kernel::single('desktop_roles');
    }

    function column_control(&$colList, $list)
    {
        foreach($list as $k => $row)
        {
            $pagedata['role_id'] = $row['role_id'];
            $colList[$k] = view::make('desktop/users/href.html', $pagedata)->render();
        }
    }

    function detail_indo($param_id){
        $opctl = $this->app->model('roles');
        $menus = $this->app->model('menus');
        $sdf_roles = $opctl->dump($param_id);
        $pagedata['roles'] = $sdf_roles;
        $workground = unserialize($sdf_roles['workground']);
        foreach((array)$workground as $v){
            #$sdf = $menus->dump($v);
            $menuname = $menus->getList('*',array('menu_type' =>'menu','permission' => $v));
            foreach($menuname as $val){
                $menu_workground[] = $val['workground'];
            }
        }
        $menu_workground = array_unique((array)$menu_workground);
        $workgrounds = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'workground','disabled'=>0,'display'=>1));
        foreach($workgrounds as $k => $v){
            $workgrounds[$k]['permissions'] = $this->obj_roles->get_permission_per($v['menu_id'],$workground);
            if(in_array($v['workground'],(array)$menu_workground)){
                $workgrounds[$k]['checked'] = 1;

            }
        }
        $widgets = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'widgets'));

            foreach($widgets as $key=>$widget){
                if(in_array($widget['addon'],$workground))
                    $__widgets[] = $widget;
            }
        $pagedata['workgrounds'] = $workgrounds;
        $pagedata['adminpanels'] = $this->obj_roles->get_adminpanel($param_id,$workground,$flg);
        $pagedata['widgets'] = $__widgets;
        $pagedata['others'] = $this->obj_roles->get_others($workground,$othersflg);
        $pagedata['othersflg'] = $othersflg;
        $pagedata['flg'] = $flg;
        return view::make('desktop/users/users_roles.html', $pagedata)->render(); 
    }
}
?>
