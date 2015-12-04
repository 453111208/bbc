<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_ctl_roles extends desktop_controller{

    var $workground = 'desktop_ctl_system';

    public function __construct($app)
    {
        parent::__construct($app);
        $this->obj_roles = kernel::single('desktop_roles');
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    function index(){
        return $this->finder('desktop_mdl_roles',array(
            'title'=>app::get('desktop')->_('角色'),
            'actions'=>array(
                            array('label'=>app::get('desktop')->_('新建角色'),'href'=>'?ctl=roles&act=addnew','target'=>'dialog::{title:\''.app::get('desktop')->_('新建角色').'\'}'),
                        )
            ));
    }

    function addnew(){
        $workgrounds = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'workground','disabled'=>0,'display'=>1));
        $pagedata['workgrounds'] = $workgrounds;
        $widgets = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'widgets'));
        $pagedata['widgets'] = $widgets;
        foreach($workgrounds as $k => $v)
        {
            $workgrounds[$k]['permissions'] = $this->obj_roles->get_permission_per($v['menu_id'],array());
        }

        $pagedata['workgrounds'] = $workgrounds;
        $pagedata['adminpanels'] = $this->obj_roles->get_adminpanel(null,array());
        //$pagedata['others'] = $this->obj_roles->get_others();

        //桌面挂件权限
        $html1 = '';
        foreach($pagedata['widgets'] as $key1=>$val1){
            if($val1['checked']){
                $html1 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' checked='checked' name='workground[]' value=".$val1['addon'].">".$val1['menu_title']."</li>";
            }else{
                $html1 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' name='workground[]' value=".$val1['addon'].">".$val1['menu_title']."</li>";
            }
        }
        $pagedata['menus1'] = "<ul><li><input class='parent' type=\"checkbox\">全选(桌面挂件权限)<ul>".$html1."</ul></li></ul>";

        //控制面板权限
        $html2 = '';
        foreach($pagedata['adminpanels'] as $key2=>$val2){
            if($val2['checked']){
                $html2 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' checked='checked' name='workground[]' value=".$val2['permission'].">".$val2['menu_title']."</li>";
            }else{
                $html2 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' name='workground[]' value=".$val2['permission'].">".$val2['menu_title']."</li>";
            }
        }
        $pagedata['menus2'] = "<ul><li><input class='parent' type=\"checkbox\">全选(控制面板权限)<ul>".$html2."</ul></li></ul>";

        //业务权限
        $treedata=array();
        foreach($pagedata['workgrounds'] as $key3=>$val3){
            $mgrpname['mgrpname'][] = $val3['menu_title'];
            $treedata[] = $this->getTree($val3['permissions'],'0');
        }
        foreach($treedata as $kmgrp=>$vmgrp){
            $treedata[$kmgrp][0]['mgrpname'] = $mgrpname['mgrpname'][$kmgrp];
        }
        foreach($treedata as $item){
            $html = $this->procHTML($item);
            $pagedata['menus3'][]= $html['html'];
        }

        /*其他权限
        #$vv3 = $this->getTree($pagedata['others'],'0');
        #$base_v3 = array('property'=>array('name'=>'其他', 'hasCheckbox'=>false), 'children'=>$vv3);
        */

        return $this->page('desktop/users/add_roles.html', $pagedata);
    }

    function save()
    {
        $this->begin();
        $roles = $this->app->model('roles');
        if($roles->validate($_POST,$msg))
        {
            if($roles->save($_POST))
            {
                $this->adminlog("添加、编辑角色[{$_POST['role_name']}]", 1);
                $this->end(true,app::get('desktop')->_('保存成功'));
            }
            else
            {
                $this->adminlog("添加、编辑角色[{$_POST['role_name']}]", 0);
                $this->end(false,app::get('desktop')->_('保存失败'));
            }
        }
        else
        {
            $this->adminlog("添加、编辑角色[{$_POST['role_name']}]", 0);
            $this->end(false,$msg);
        }
    }

    function getTree($data, $pId){
        $tree = '';
        foreach($data as $k => $v){
           if($v['parent'] == $pId){         //父亲找到儿子
               $v['parent'] = $this->getTree($data, $v['permission']);
               $tree[] = $v;
               //unset($data[$k]);
           }
        }
        return $tree;
    }

    function edit($roles_id){
        $param_id = $roles_id;
        $this->begin();
        if($_POST){
            if($_POST['role_name']==''){
                 $this->end(false,app::get('desktop')->_('工作组名称不能为空'));
            }
            if(!$_POST['workground']){
                //$_POST['workground'] = '';
                $this->end(false,app::get('desktop')->_('请至少选择一个权限'));
            }
            $opctl = $this->app->model('roles');
            $result = $opctl->check_gname($_POST['role_name']);
            if($result && ($result!=$_POST['role_id'])) {$this->end(false,app::get('desktop')->_('该工作组名称已存在'));}
            if($opctl->save($_POST)){
                 $this->end(true,app::get('desktop')->_('保存成功'));
            }else{
               $this->end(false,app::get('desktop')->_('保存失败'));
            }

            }
        else{
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
                    $widgets[$key]['checked'] = true;
            }

            $pagedata['widgets'] = $widgets;
            $pagedata['workgrounds'] = $workgrounds;
            $pagedata['adminpanels'] = $this->obj_roles->get_adminpanel($param_id,$workground);
            //$pagedata['others'] = $this->obj_roles->get_others($workground);

            //桌面挂件权限
            $html1 = '';
            $checkall = false;
            foreach($pagedata['widgets'] as $key1=>$val1){
                if($val1['checked']){
                    $html1 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' checked='checked' name='workground[]' value=".$val1['addon'].">".$val1['menu_title']."</li>";
                    $checkall = true;
                }else{
                    $html1 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' name='workground[]' value=".$val1['addon'].">".$val1['menu_title']."</li>";
                    $checkall = false;
                }
            }
            $pagedata['menus1'] = "<ul><li><input class='parent'".($checkall?" checked='checked'":"")." type=\"checkbox\">全选(桌面挂件权限)<ul>".$html1."</ul></li></ul>";

            //控制面板权限
            $html2 = '';
            $checkall = false;
            foreach($pagedata['adminpanels'] as $key2=>$val2){
                if($val2['checked']){
                    $html2 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' checked='checked' name='workground[]' value=".$val2['permission'].">".$val2['menu_title']."</li>";
                    $checkall = true;
                }else{
                    $html2 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' name='workground[]' value=".$val2['permission'].">".$val2['menu_title']."</li>";
                    $checkall = false;
                }
            }
            $pagedata['menus2'] = "<ul><li><input class='parent'".($checkall?" checked='checked'":"")." type=\"checkbox\">全选(控制面板权限)<ul>".$html2."</ul></li></ul>";

            //业务权限
            $treedata=array();
            foreach($pagedata['workgrounds'] as $key3=>$val3){//原始权限信息列表
                $mgrpname['mgrpname'][] = $val3['menu_title'];
                $treedata[] = $this->getTree($val3['permissions'],'0');
            }
            foreach($treedata as $kmgrp=>$vmgrp){//权限分组信息
                $treedata[$kmgrp][0]['mgrpname'] = $mgrpname['mgrpname'][$kmgrp];
            }
            foreach($treedata as $item){//权限列表生成
                $html = $this->procHTML($item);
                $pagedata['menus3'][]= $html['html'];
                $checkarr[] = $html['checkall'];
            }
            $checked_all = false;
            foreach ($checkarr as $key) {
                if($key == 'true') {
                    $checked_all = true;
                }
                else {
                    $checked_all = false;
                }
            }
            $pagedata['checked_all'] = $checked_all;
            /*其他权限
            #$vv3 = $this->getTree($pagedata['others'],'0');
            #$base_v3 = array('property'=>array('name'=>'其他', 'hasCheckbox'=>false), 'children'=>$vv3);
            */

            return $this->page('desktop/users/edit_roles.html', $pagedata);
            }
    }


    function procHTML($tree){
        $html = '';
        $checkall = 'false';
        foreach($tree as $k=>$t){
            if($t['mgrpname']){
                $html .= "<li style='text-align:left;font-weight:bold;font-style:italic;'>".$t['mgrpname'];
            }
            if($t['parent'] == ''){
                if($t['checked']){
                $html .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf'  type='checkbox' checked='checked' name='workground[]' value=".$t['permission'].">".$t['menu_title'];
                $checkall = 'true';
                }else{
                $html .= "<li style='padding-left:25px;text-align:left;'><input   class='leaf' type='checkbox' name='workground[]' value=".$t['permission'].">{$t['menu_title']}</li>";
                $checkall = 'false';
                }
            }else{
                if($t['checked']){
                $html .= "<li style='padding-left:25px;text-align:left;'><input  class='parent leaf'  type='checkbox' checked='checked' name='workground[]' value=".$t['permission'].">".$t['menu_title'];
                $checkall = 'true';
                }else{
                $html .= "<li style='padding-left:25px;text-align:left;'><input  class='parent leaf'  type='checkbox' name='workground[]' value=".$t['permission'].">".$t['menu_title'];
                $checkall = 'false';
                }
                $str = $this->procHTML($t['parent']);
                $html .= $str['html'];
                $html = $html."</li>";
            }
        }
        //return $html ? "<ul>".$html."</ul>" : $html;
        return array(
            "html"=>"<ul>".$html."</ul>",
            "checkall"=>$checkall
        );
    }

}
