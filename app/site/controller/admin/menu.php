<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

/*
 * @package site
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license 
 */
class site_ctl_admin_menu extends site_admin_controller 
{
    
    /*
     * workground
     * @var string
     */
    var $workground = 'site.wrokground.theme';

    /*
     * 列表
     * @public
     */
    public function index() 
    {
        return $this->finder('site_mdl_menus', array(
            'title' => app::get('site')->_('导航菜单'),
            'base_filter' => array(),
            'actions'=>array(
                array(
                    'label' => app::get('site')->_('添加菜单'), 
                    'href' => '?app=site&ctl=admin_menu&act=add', 
                    'target' => 'dialog::{frameable:true, title:\''. app::get('site')->_('添加菜单').'\', width:400, height:400}',
                ),
            ),
        ));
    }//End Function
    
    /*
     * 添加菜单
     * @public
     */
    public function add() 
    {
        $step = input::get('step');
        switch($step)
        {
            case 3:
                $module = input::get('module');
                if(empty($module))  $this->_error();
                $args = explode(':', $module);
                if(count($args) != 3)   $this->_error();
                $args = array_combine(array('app', 'ctl', 'act'), $args);
                $obj = kernel::service('site_menu.' . sprintf('%s_%s_%s', $args['app'], $args['ctl'], $args['act']));
                if($obj instanceof site_interface_menu){
                    foreach($obj->inputs() as $title=>$input){
                        $tmp['title'] = $title;
                        $tmp['input'] = view::ui()->input($input);
                        $html[] = $tmp;
                    }
                    $pagedata['html'] = $html;
                    $pagedata['menu'] = $args;
                    return view::make('site/admin/menu/edit_app_module.html', $pagedata);
                }else{
                    $pagedata['menu'] = $args;
                    return view::make('site/admin/menu/edit_module.html', $pagedata);
                }                
            break;
            case 2:
                $type = input::get('type');
                if($type == 'module'){
                    $pagedata['menus'] = $this->get_module_menus();
                    return view::make('site/admin/menu/add_module.html', $pagedata);
                }else{
                    return view::make('site/admin/menu/edit_url.html', $pagedata);
                }
            break;
            default:
                $pagedata['pre'] = input::get('pre');
                return view::make('site/admin/menu/add_step_1.html', $pagedata);
        }//End Switch
    }//End Function

    /*
     * 保存APP模块
     * @public
     */
    public function saveappmodule() 
    {
        $this->begin('?app=site&ctl=admin_menu&act=index');
        $get_menu = input::get('menu');
        $id = $get_menu['id'];
        $app = $get_menu['app'];
        $ctl = $get_menu['ctl'];
        $act = $get_menu['act'];

        $obj = kernel::service('site_menu.' . sprintf('%s_%s_%s', $app, $ctl, $act));
        if($obj instanceof site_interface_menu){
            $menu = input::get('menu');
            if(empty($menu['title']))   $this->_error();
            $obj->handle(input::get());
            $params = $obj->get_params();
            $config = $obj->get_config();
            $data = array(
                'title' => $menu['title'],
                'app' => $app,
                'ctl' => $ctl,
                'act' => $act,
                'display_order' => ((is_numeric($menu['display_order']) && $menu['display_order'] > 0) ? $menu['display_order'] : 0),
                'hidden' => $menu['hidden'],
                'target_blank' => $menu['target_blank'],
                'params' => $params,
                'config' => $config
            );
            if($id > 0){
                if(app::get('site')->model('menus')->update($data, array('id'=>$id))){
                    $this->end(true, app::get('site')->_('保存成功'));
                }else{
                    $this->end(false, app::get('site')->_('保存失败'));
                }
            }else{
                if(app::get('site')->model('menus')->insert($data)){
                    $this->end(true, app::get('site')->_('添加成功'));
                }else{
                    $this->end(false, app::get('site')->_('添加失败'));
                }
            }
        }else{
            $this->_error();
        }
    }//End Function

    /*
     * 保存普通模块
     * @public
     */
    public function savemodule() 
    {   
        $this->begin('?app=site&ctl=admin_menu&act=index');
        $menu = input::get('menu');
        if(empty($menu) || empty($menu['app']) || empty($menu['ctl']) || empty($menu['act']) || empty($menu['title']))    $this->_error();
        $data = array(
            'title' => $menu['title'],
            'app' => $menu['app'],
            'ctl' => $menu['ctl'],
            'act' => $menu['act'],
            'display_order' => ((is_numeric($menu['display_order']) && $menu['display_order'] > 0) ? $menu['display_order'] : 0),
            'hidden' => $menu['hidden'],
            'target_blank' => $menu['target_blank'],
        );
        if($menu['id'] > 0){
            if(app::get('site')->model('menus')->update($data, array('id'=>intval($menu['id'])))){
                $this->end(true, app::get('site')->_('保存成功'));
            }else{
                $this->end(false, app::get('site')->_('保存失败'));
            }
        }else{
            if(app::get('site')->model('menus')->insert($data)){
                $this->end(true, app::get('site')->_('添加成功'));
            }else{
                $this->end(false,  app::get('site')->_('添加失败'));
            }            
        }
    }//End Function

    /*
     * 保存自定义url
     * @public
     */
    public function saveurl() 
    {
        $this->begin('?app=site&ctl=admin_menu&act=index');
        $menu = input::get('menu');
        if(empty($menu) || empty($menu['title']) || empty($menu['custom_url']))    $this->_error();
        $data = array(
            'title' => $menu['title'],
            'custom_url' => $menu['custom_url'],
            'display_order' => ((is_numeric($menu['display_order']) && $menu['display_order'] > 0) ? $menu['display_order'] : 0),
            'hidden' => $menu['hidden'],
            'target_blank' => $menu['target_blank'],
        );
        if($menu['id'] > 0){
            if(app::get('site')->model('menus')->update($data, array('id'=>intval($menu['id'])))){
                $this->end(true, app::get('site')->_('保存成功'));
            }else{
                $this->end(false, app::get('site')->_('保存失败'));
            }
        }else{
            if(app::get('site')->model('menus')->insert($data)){
                $this->end(true, app::get('site')->_('添加成功'));
            }else{
                $this->end(false, app::get('site')->_('添加失败'));
            }            
        }
    }//End Function


    /*
     * 取得模块菜单信息
     * @private
     */
    private function get_module_menus() 
    {
        $menus = array();
        $app_module = app::get('site')->model('modules')->getList('*');
        if(is_array($app_module)){
            foreach($app_module AS $module){
                $tmp = array();
                if(empty($module['allow_menus']))   continue;
                $tmp['title'] = $module['title'];
                $tmp['app'] = $module['app'];
                $tmp['ctl'] = $module['ctl'];
                $allows = explode('|', $module['allow_menus']);
                foreach($allows AS $allow){
                    $tmp['allow'][] = array('act'=>substr($allow, 0, strpos($allow, ':')), 'title'=>substr($allow, strpos($allow, ':')+1));
                }
                $menus[] = $tmp;
            }
        }
        return $menus;
    }//End Function

    public function detail_edit($id){

        $qb = app::get('site')->database()->createQueryBuilder();
        $menu = $qb->select('*')->from('site_menus')->where('id='.$qb->createPositionalParameter($id))->execute()->fetch();
        if( !$menu ) return '';

        if(empty($menu['app'])){
            $pagedata['menu'] = $menu;
            return view::make('site/admin/menu/edit_url.html', $pagedata);
        }else{
            $obj = kernel::service('site_menu.' . sprintf('%s_%s_%s', $menu['app'], $menu['ctl'], $menu['act']));
            if($obj){
                $config = $menu['config'];
                foreach($obj->inputs($config) as $title=>$input){
                    $tmp['title'] = $title;
                    $tmp['input'] = view::ui()->input($input);
                    $html[] = $tmp;
                }
                $pagedata['menu'] = $menu;
                $pagedata['html'] = $html;
                return view::make('site/admin/menu/edit_app_module.html', $pagedata);
            }else{
                $pagedata['menu'] = $menu;
                return view::make('site/admin/menu/edit_module.html', $pagedata);
            }
        }
    }
}//End Class
