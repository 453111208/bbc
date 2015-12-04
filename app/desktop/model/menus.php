<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_mdl_menus extends dbeav_model{

   //根据工作组获得菜单
   var $defaultOrder = array('menu_order', ' ASC');
   
   function menu($get,$defaultWorkground){ 
        $menu_type = 'menu';
        $workground = '';
        $aper = array();
                $this->user = kernel::single('desktop_user');
                if($this->user->is_super()){
                    $pers = $this->getList('permission',array('menu_type' => 'permission','disabled' => 0));
                    foreach($pers as $val){
                        $aper[] = $val['permission'];
                    }
                }
                else{
                    $group = $this->user->group();
                    foreach($group as $v){
                        #$data = $this->dump($v);
                        $aper[] = $v;
                    }
                }
                
                
        $menu_path = "app=".$get['app']."&ctl=".$get['ctl']."&act=".$get['act'];
        $aData = $this->getList('*',array('menu_type' => 'menu','menu_path' => $menu_path));
        if(count($aData) == 0){
            $aPanel = $this->getList('*',array('menu_type' => 'adminpanel','menu_path' => $menu_path));
            if(count($aPanel) == 0){
                if($defaultWorkground){
                    $workground = $defaultWorkground;
                    
                }
                else{ 
                    return null;
                    }
            }
            else{
                $menu_title  = $aPanel[0]['menu_title'];
                $adminpanel_wg = $this->adminpanel($aper);
                return $adminpanel_wg;
                
            }
            
         }
        if(count($aData) > 1){
            foreach($aData as $k => $row){
                $addon = unserialize($row['addon']);
                $flag = true;
                foreach((array)$addon['url_params'] as $field => $val){
                    if($get[$field] != $val){
                        $flag = false;
                        break;
                    }
                }
                if($flag){
                    $workground = $aData[$k]['workground'];
                }
            }
        }elseif(count($aData) == 1){
            $workground = $aData[0]['workground'];
        }else{
           # $menu_type = 'workground';
        }
       
        $menu = $this->getList('*',array('menu_type' => $menu_type,'workground' => $workground,'display'=>1,'permission' => $aper));
        
        //        echo '<pre>';
        //var_dump(array('menu_type' => $menu_type,'workground' => $workground,'display'=>1,'permission' => $aper));
        $menu_group = array();
        $data_menu = array();
        foreach($menu as $val){
             if($val['menu_group']) $menu_group[] = $val['menu_group'];
        }
        $menu_group = array_unique($menu_group);
        $tmp = array();
        $tmp['menugroup'] = '';
        foreach($menu_group as $key_=>$value){
            $data_menu[$key_]['menugroup'] = $value;
            foreach($menu as $res){
                if($res['menu_title']) $res['menu_title'] = app::get('desktop')->_($res['menu_title']);
                $url_params = unserialize($res['addon']);       
                if(count($url_params['url_params'])>0){
                    $query = '&'.http_build_query($url_params['url_params']);
                    $res['menu_path'] = $res['menu_path'].$query;
                }
                if(!$res['menu_group']){
                    $tmp['menu'][$res['menu_id']] = $res;
                }else{
                    //$res['menu_group'] = app::get('desktop')->_($res['menu_group']);
                    if($res['menu_group'] ==$value){
                        $data_menu[$key_]['menu'][] = $res;
                    }
                }
            }
        }
        $data_menu['nogroup'] = $tmp;
        return $data_menu;
   } 
   
   //根据permission ID获取工作组菜单
   
   function workgroup($permission_id){
       #$sdf = $this->dump($permission_id);
       $data = $this->getList('*',array('menu_type'=>'menu','permission'=>$permission_id,'display' =>1));
       $menu_group = array();
       $workground_menu = array();
       foreach($data as $val){
           $workground[] = $val['workground'];
       }
       $workground = @array_unique($workground);
       if(is_array($workground)){
       foreach($workground as $value){
          $aTmp = $this->getList('*',array('menu_type' => 'workground','workground' => $value,'display' => 1));
          if(!$aTmp[0]) break;
          $workground_menu[] = $aTmp[0]; 
       }
        }//if
       return $workground_menu;
   }
   
   //根据permission ID获得工作组ID
   function wrokground($permission_id){
   $sdf = $this->dump($permission_id);
   $data = $this->getList('menu_id',array('menu_type' => 'workground','permission' => $sdf['addon']));
   return $data;        
   }
   
   
   //根据$_GET获得permission_id
   
   function permissionId($get){
       $menu_path = "app=".$get['app']."&ctl=".$get['ctl']."&act=".$get['act'];
       $aData = $this->getList('*',array('menu_path' => $menu_path));
       if(count($aData) > 1){
            foreach($aData as $k => $row){
                $addon = unserialize($row['addon']);
                $flag = true;
                foreach((array)$addon['url_params'] as $field => $val){
                    if($get[$field] != $val){
                        $flag = false;
                        break;
                    }
                }
                if($flag){
                    if($row['disabled'] == 1)
                    {
                        echo "链接不可用";
                        exit;
                    }
                   $permission = $row['permission'];
                   $res_data = $this->getList('permission',array('menu_type' => 'permission','permission' => $permission,'disabled' => 0));
                   $permission_data = $res_data[0]['permission'];
                    
                }
            }
        }elseif(count($aData) == 1){
           if($aData[0]['disabled'] == 1)
                    {
                        echo "链接不可用";
                        exit;
                    }
           $permission = $aData[0]['permission'];
           $per_data = $this->getList('permission',array('menu_type' => 'permission','permission' => $permission,'disabled' => 0));
           $permission_data = $per_data[0]['permission'];
        }//
        else{            
            $permission_data = '0';
        }
       return $permission_data;
   }
   
   // //根据permission ID获得子菜单
   
   function get_menu($permission_id){
       #$sdf = $this->dump($permission_id);
       $data = $this->getList('*',array('menu_type' => 'menu','permission' => $permission_id,'display' =>1));
       return $data;
   }
   function get_current_workground($get){
       $menu_path = "app=".$get['app']."&ctl=".$get['ctl']."&act=".$get['act'];
       if(!$menu_path) return;
        $aData = $this->getList('*',array('menu_type' => 'menu','menu_path' => $menu_path));
        if(count($aData) == 0) return null;
        if(count($aData) > 1){
            foreach($aData as $k => $row){
                $addon = unserialize($row['addon']);
                $flag = true;
                foreach((array)$addon['url_params'] as $field => $val){
                    if($get[$field] != $val){
                        $flag = false;
                        break;
                    }
                }
                if($flag){
                    $workground = $aData[$k]['workground'];
                    break;
                }
            }
        }else{
            $workground = $aData[0]['workground'];
        }
        

       $rows = $this->getList('*',array('menu_type'=>'workground','workground' => $workground));
       return $rows[0];
   }
   
   //控制面板中属于同一panelgroup
   
   function adminpanel($aper){
            $admin_data = array();
            $panelgroups = $this->getList('*',array('menu_type' => 'panelgroup','disabled' => 0));
            $aData = $this->getList('*',array('menu_type' => 'adminpanel','permission' => $aper, 'display' => 1, 'disabled' => 0));
            foreach($panelgroups as $key=>$val){
                $menu_title = $val['app_id'].'_'.$val['addon'];
                $admin_data[$key]['menugroup'] = $val['menu_title'];
                foreach($aData as $row){
                    if(strpos($row['menu_title'],$menu_title) !==false){
                        $aTmp = explode(':',$row['menu_title']);
                        $row['menu_title'] = $aTmp[1]; 
                        $admin_data[$key]['menu'][] = $row; 
                    }
                }
            }
            foreach($admin_data as $key=>$v){
                if(!$v['menu']) unset($admin_data[$key]);
            }
            return $admin_data;
           
   }
   
   //获取menu的menuID,menugroupId,workgroundId
   
   function get_allid($get){
       $get['ctl'] = $get['ctl']?$get['ctl']:'default';
       $get['act'] = $get['act']?$get['act']:'index';
       $get['app'] = $get['app']?$get['app']:'desktop';
       $menu_path = "app=".$get['app']."&ctl=".$get['ctl']."&act=".$get['act'];
       

       if(!$menu_path) return;
        $aData = $this->getList('*',array('menu_path' => $menu_path));
        if(count($aData) == 0) return null;
        if(count($aData) > 1){
            foreach($aData as $k => $row){
                $addon = unserialize($row['addon']);
                if(!isset($addon['url_params'])){
                    $menu['menu_id'] = $row['menu_id'];
                    continue;
                }
                $flag = true;
                foreach((array)$addon['url_params'] as $field => $val){
                    if($get[$field] != $val){
                        $flag = false;
                        break;
                    }
                }    
                if($flag){
                    $menu['menu_id'] = $aData[$k]['menu_id'];
                    break;
                }
            }
            if($aData[$k]['workground']){
                $workground_data = $this->getList('menu_id',array('menu_type' => 'workground','workground' => $aData[$k]['workground']));   
            }
            else{
                $workground_data = null;
            }
            $menu['workground_id'] = $workground_data[0]['menu_id'];
            
        }else{
            $menu['menu_id'] = $aData[0]['menu_id'];
             if($aData[0]['workground']){
                $workground_data = $this->getList('menu_id',array('menu_type' => 'workground','workground' => $aData[0]['workground']));
                }
            else{
                $workground_data = null;
            }    
            $menu['workground_id'] = $workground_data[0]['menu_id'];
        }
        return $menu;
    }
   
}
