<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_ctl_adminpanel extends desktop_controller{

    var $workground = 'desktop_ctl_system';

    function index(){

        $user = kernel::single('desktop_user');
        $menus = $this->app->model('menus');
        if($user->is_super()){
        $list = app::get('desktop')->model('menus')->getlist(
            'app_id,menu_title,addon,menu_path',array(
                'menu_type'=>'panelgroup',
                'disabled'=>0
        ));
        $pers = $menus->getList('permission',array('menu_type' => 'permission','disabled' => 0));
        foreach($pers as $val){
            $aper[] = $val['permission'];
        }
        foreach($list as $row){
            $group[$row['app_id'].'_'.$row['addon']] = array(
                    'icon'=>app::get($row['app_id'])->res_url.'/images/bundle/'.$row['menu_path'],
                    'title'=>$row['menu_title'],
                    'items'=>array(),
                );
        }

             $list = app::get('desktop')->model('menus')->getlist(
            'app_id,menu_title,menu_path,permission',array(
                'menu_type'=>'adminpanel',
                'disabled'=>0,
                'display' => 1,
        ));
        foreach($list as $row){
            if(!in_array($row['permission'],(array)$aper))
            continue;
            $p = strpos($row['menu_title'],':');
            if($p){
                $group_name = substr($row['menu_title'],0,$p);
                $row['menu_title'] = substr($row['menu_title'],$p+1);
                if(isset($group[$group_name])){
                    $group[$group_name]['items'][] = $row;
                }else{
                    //$group['base_other']['items'][] = $row;
                }
            }else{
                $group['base_other']['items'][] = $row;
            }

        }

        $pagedata['groups'] = $group;

        }
        else{
            $group = $user->group();
            $aPer = array();
            foreach($group as $val){
                #$sdf = $menus->dump($val);
                $aPer[] = $val;
            }

            $adminpanel = app::get('desktop')->model('menus')->getlist(
                'app_id,menu_title,menu_path',array(
                    'menu_type'=>'adminpanel',
                    'disabled'=>0,
                    'permission'=>$aPer,
                    'display' => 1,
                ));

            $list = app::get('desktop')->model('menus')->getlist(
                'app_id,menu_title,addon,menu_path',array(
                    'menu_type'=>'panelgroup',
                    'disabled'=>0
                ));
            $aTitle = array();
            foreach($adminpanel as $key=>$val){
                $aTmp = explode(':',$val['menu_title']);
                $aTitle[] = $aTmp[0];
            }
            $list_bak = array();
            foreach($list as $key=>$val){
                $fag = $val['app_id'].'_'.$val['addon'];
                if(in_array($fag,$aTitle))
                    $list_bak[] = $val;
            }
            $group = array();
            foreach($list_bak as $row){
                $group[$row['app_id'].'_'.$row['addon']] = array(
                    'icon'=>app::get($row['app_id'])->res_url.'/images/bundle/'.$row['menu_path'],
                    'title'=>$row['menu_title'],
                    'items'=>array(),
                );
            }
            $list = app::get('desktop')->model('menus')->getlist(
                'app_id,menu_title,menu_path',array(
                    'menu_type'=>'adminpanel',
                    'disabled'=>0,
                    'permission'=>$aPer,
                    'display' => 1,
                ));
            foreach($list as $row){
                $p = strpos($row['menu_title'],':');
                if($p){
                    $group_name = substr($row['menu_title'],0,$p);
                    $row['menu_title'] = substr($row['menu_title'],$p+1);
                    if(isset($group[$group_name])){
                        $group[$group_name]['items'][] = $row;
                    }else{
                        //$group['base_other']['items'][] = $row;
                    }
                }else{
                    $group['base_other']['items'][] = $row;
                }

            }

            $pagedata['groups'] = $group;
        }


        return $this->page('desktop/system/adminpanel.html', $pagedata);
    }

    function licence(){
        $this->url_frame(base_misc_url::licence());
    }

}
