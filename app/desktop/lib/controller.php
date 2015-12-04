<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_controller {

    var $defaultwg;
    public function __construct($app)
    {
        $this->app = $app;

        $this->defaultwg = $this->defaultWorkground;
        kernel::single('base_session')->start();

        pamAccount::setAuthType('desktop');

        if($_COOKIE['autologin'] > 0){
            kernel::single('base_session')->set_sess_expires($_COOKIE['autologin']);
        }//如果有自动登录，设置session过期时间，单位：分

        if(get_class($this)!='desktop_ctl_passport' && !pamAccount::check()){
            if(get_class($this) != 'desktop_ctl_default')
            {
                $url = url::route('shopadmin', $_GET);
            }
            else
            {
                $url = url::route('shopadmin');
            }
            $url = base64_encode($url);
            $arr_get = $_GET;
            foreach ($arr_get as &$str_get)
            {
                $str_get = urldecode($str_get);
            }
            $params = urlencode(json_encode($arr_get));
            // 直接跳转, 所以直接send
            $goto = url::route('shopadmin', array('ctl' => 'passport', 'url' => $url, 'params' => $params));
            echo "<script>location ='$goto'</script>";exit;
        }

        $this->user = kernel::single('desktop_user');
        if($_GET['ctl']!="passport"&&$_GET['ctl']!=""){
            $this->status = $this->user->get_status();
            if(!$this->status&&$this->status==0){
                unset($_SESSION['account']);//如果验证错误，则把此次的session值清掉
                $url = url::route('shopadmin');
                $url = base64_encode($url);
                $pagedata['link_url'] = '?ctl=passport&url='.$url;
                view::make('desktop/auth_error.html', $pagedata)->send();
            }
        }
        ###如果不是超级管理员就查询操作权限
        if(!$this->user->is_super()){
            if(!$this->user->chkground($this->workground)){
                header('Content-Type:text/html; charset=utf-8');
                return app::get('desktop')->_("您无权操作");
            }
        }
        $obj_model = app::get('desktop')->model('menus');
        //检查链接是否可用
        $obj_model->permissionId($_GET);
        //end

        $this->url = url::route('shopadmin', array('app' => $this->app->app_id, 'ctl' => request::get('ctl')));
    }

    function begin($url_params=null){
        set_exception_handler(array($this, 'exceptionHandler'));
        if($this->transaction_start) trigger_error('The transaction has been started',E_USER_ERROR);
        db::connection()->beginTransaction();

        $this->transaction_start = true;
        if(is_array($url_params)){
            $this->_action_url = url::route('shopadmin', $url_params);
        }else{
            $this->_action_url = $url_params;
        }
    }

    public function exceptionHandler($exception)
    {
        //如果不是逻辑错误, 则抛出让ecos处理. 如果是逻辑错误那么跳转页面
        if (!$exception instanceof LogicException) throw $exception;
        $msg = $exception->getMessage();

        $this->splash('error',$this->_action_url, $msg);
        return url::to($this->_action_url);
    }


    function endonly($result=true){
        if(!$this->transaction_start) trigger_error('The transaction has not started yet',E_USER_ERROR);
        $this->transaction_start = false;
        restore_exception_handler();
        if($result){
            //$db->commit($this->transaction_status);
            db::connection()->commit();

        }else{
            //$db->rollback();
            db::connection()->rollback();
        }
    }

    function end($result=true,$message=null,$url_params=null,$params=array()){
        if(!$this->transaction_start) trigger_error('The transaction has not started yet',E_USER_ERROR);
        $this->transaction_start = false;
        restore_error_handler();
        if(is_null($url_params)){
            $url = $this->_action_url;
        }elseif(is_array($url_params)){
            //$url = $this->app->router()->gen_url($url_params);
            $url = url::route('shopadmin', $url_params);
        }else{
            $url = $url_params;
        }
        if($result){
            //$db->commit($this->transaction_status);
            db::connection()->commit();
            $status = 'success';
            $message = ($message=='' ? app::get('desktop')->_('操作成功！') : app::get('desktop')->_('成功：').$message);
        }else{
            //$db->rollback();
            db::connection()->rollback();
            $status = 'error';
            $message = $message?$message:app::get('desktop')->_("操作失败: 对不起,无法执行您要求的操作");
        }
        $this->_end_message = $message;
        $this->_end_status = $status;
        return $this->splash($status,$url,$message,'redirect',$params);
    }

    function redirect($url){
        $arr_url = parse_url($url);
        if($arr_url['scheme'] && $arr_url['host']){
            header('Location: '.$url);
        }else{
            header('Location: '.url::route('shopadmin').'/'.$url);
        }
    }

    function location_to(){
        //echo request::url(). '#'.$_SERVER['QUERY_STRING'];
        if(request::ajax()!=true){
            header('Location: '. request::url(). '#'.$_SERVER['QUERY_STRING']);
        }
    }
    function finder($object_name,$params=array()){
        if($_GET['action']!='to_export'&&$_GET['action']!='to_import'&&$_GET['singlepage']!='true')
        {
            $this->location_to();
        }

        $_GET['action'] = $_GET['action']?$_GET['action']:'view';
        $finder = kernel::single('desktop_finder_builder_'.$_GET['action'],$this);

        foreach($params as $k=>$v){
            $finder->$k = $v;
        }
        $app_id = substr($object_name,0,strpos($object_name,'_'));
        $app = app::get($app_id);
        $finder->app = $app;
        return $finder->work($object_name);
    }

    function singlepage($view, $pagedata=array()){

        $service = kernel::service(sprintf('desktop_controller_display.%s.%s.%s', $_GET['app'],$_GET['ctl'],$_GET['act']));
        if($service){
            if(method_exists($service, 'get_file'))  $view = $service->get_file();
            if(method_exists($service, 'get_app_id'))   $app_id = $service->get_app_id();
        }

        $page = view::make($view, $pagedata)->render();

        ini_set('pcre.backtrack_limit', 10000000);
        $re = '/<script([^>]*)>(.*?)<\/script>/is';
        $this->__scripts = '';
        $page = preg_replace_callback($re,array(&$this,'_singlepage_prepare'),$page)
            .'<script type="text/plain" id="__eval_scripts__" >'.$this->__scripts.'</script>';

        //后台singlepage页面增加自定义css引入到head标签内的操作--@lujy-start
        $recss = '/<link([^>]*)>/is';
        $this->__link_css = '';
        $page = preg_replace_callback($recss,array(&$this,'_singlepage_link_prepare'),$page);
        $pagedata['singleappcss'] = $this->__link_css;
        //--end

        $pagedata['statusId'] = $this->app->getConf('b2c.wss.enable');
        $pagedata['session_id'] = kernel::single('base_session')->sess_id();
        $pagedata['desktop_path'] = app::get('desktop')->res_url;
        $pagedata['shopadmin_dir'] = dirname($_SERVER['PHP_SELF']).'/';
        $pagedata['shop_base'] = url::route('topc');
        $pagedata['desktopresurl'] = app::get('desktop')->res_url;
        $pagedata['desktopresfullurl'] = app::get('desktop')->res_full_url;
        $pagedata['_PAGE_'] = &$page;
        return view::make('desktop/singlepage.html', $pagedata);
    }

    function _singlepage_prepare($match){
        if($match[2] && !strpos($match[1],'src') && !strpos($match[1],'hold')){
            $this->__scripts.="\n".$match[2];
            return '';
        }else{
            return $match[0];
        }
    }

    //处理singlepage页面的css的preg_replace_callback的回调替换函数--@lujy-start
    function _singlepage_link_prepare($matches){
        $this->__link_css .= $matches[0];
        return '';
    }
    //--end

    function _outSplitBegin($key){
       return "<!-----$key-----";
    }

    function _outSplitEnd($key){
       return "-----$key----->";
    }





    function url_frame($url){
        $this->sidePanel();
        echo '<iframe width="100%" scrolling="auto" allowtransparency="true" frameborder="0" height="100%" src="'.$url.'" ></iframe>';
    }

    function page($view=null, $pagedata = array())
    {
        $this->location_to();
        $_SESSION['message'] = '';


        $service = kernel::service(sprintf('desktop_controller_display.%s.%s.%s', $_GET['app'],$_GET['ctl'],$_GET['act']));
        if($service){
            if(method_exists($service, 'get_file'))  $view = $service->get_file();
            if(method_exists($service, 'get_app_id'))   $app_id = $service->get_app_id();
        }

        if(!$view){
            $view = 'desktop/common/default.html';
        }

        $output = view::make($view, $pagedata)->render();
        $output=$this->sidePanel().$output;
        return $this->output($output);
    }



    function sidePanel(){
         $menuObj = app::get('desktop')->model('menus');
         $bcdata = $menuObj->get_allid($_GET);
         $output = '';
         if(!$this->workground){
            $this->workground = get_class($this);
         }
         $output.="<script>window.BREADCRUMBS ='".($bcdata['workground_id']?$bcdata['workground_id']:0)
                                                .":"
                                                .($bcdata['menu_id']?$bcdata['menu_id']:0)
                                                ."';</script>";

         if('desktop_ctl_dashboard'==$this->workground){

             $output .="<script>fixSideLeft('add');</script>";
             return $output;
         }else{

             $output .="<script>fixSideLeft('remove');</script>";
         }

        if($_SERVER['HTTP_WORKGROUND'] == $this->workground){
            return $output;
        }


        $output.= $this->_outSplitBegin('.side-content');
        $output .= $this->get_sidepanel($menuObj);
        $output .= $this->_outSplitEnd('.side-content');

        $output .= '<script>window.currentWorkground=\''.$this->workground.'\';</script>';
        return $output;
    }

    public function output(&$output)
    {
       echo $output;
    }//End Function

   function splash($status='success',$url=null,$msg=null,$method='redirect',$params=array()){
        $default = array(
                $status=>$msg?$msg:app::get('desktop')->_('操作成功'),
                $method=>$url,
            );
        $arr = array_merge($default, $params ,array('splash'=>true));
        response::json($arr)->send();exit;
    }

    function has_permission($perm_id)
    {
        $user = kernel::single('desktop_user');
        return $user->has_permission($perm_id);
    }

   function get_sidepanel($menuObj)
   {
        $obj = $menuObj;
        $workground_menus = ($obj->menu($_GET,$this->defaultwg));
        if($workground_menus['nogroup'])
        {
            $nogroup = $workground_menus['nogroup'];
            unset($workground_menus['nogroup']);

        }

        if(!$workground_menus)
        {
            $dashboard_menu = new desktop_sidepanel_dashboard(app::get('desktop'));
            return $dashboard_menu->get_output();

        }
        $workground = array();
        if($_GET['app']&&$_GET['ctl'])
        {
            $workground = $obj->get_current_workground($_GET);
            $pagedata['workground'] = $workground;
        }
        $data_id = $obj->get_allid($_GET);
        $pagedata['side'] = "leftpanel";
        $pagedata['menus_data'] = $workground_menus;
        $pagedata['nogroup'] = $nogroup;
        return view::make('desktop/sidepanel.html', $pagedata)->render();
    }

    function tags()
    {
        $ex_p = '&wg='.urlencode($_GET['wg']).'&type='.urlencode($_GET['type']);
        $params = array(
            'title'=>app::get('desktop')->_('标签管理'),
            'actions'=>array(
                array('label'=>app::get('desktop')->_('新建普通标签'),'icon'=>'add.gif','href'=>$this->url.'&act=new_mormal_tag'.$ex_p,'target'=>'dialog::{title:\''.app::get('desktop')->_('新建普通标签').'\'}'),
               // array('label'=>'新建条件标签','href'=>$this->url.'&act=new_filter_tag'.$ex_p,'target'=>'dialog::{title:\'新建条件标签\'}'),
            ),
            'base_filter'=>array(
                'tag_type'=>$_GET['type'],
                'app_id'=>$_GET['app'],
            ),'use_buildin_set_tag'=>false,'use_buildin_export'=>false);
        return $this->finder('desktop_mdl_tag',$params);
    }

    function new_mormal_tag(){
        $ex_p = '&wg='.urlencode($_GET['wg']).'&type='.urlencode($_GET['type']);
       if($_POST){
            $this->begin();
            $tagmgr = app::get('desktop')->model('tag');
            $data = array(
                    'tag_name'=>$_POST['tag_name'],
                    'tag_abbr'=>$_POST['tag_abbr'],
                    'tag_type'=>$_REQUEST['type'],
                    'app_id'=>$this->app->app_id,
                    'tag_mode'=>'normal',
                    'tag_bgcolor'=>$_POST['tag_bgcolor'],
                    //'tag_fgcolor'=>$_POST['tag_fgcolor'],
                );
            if($_POST['tag_id']){
                $data['tag_id'] = $_POST['tag_id'];
            }
            $tagmgr->save($data);
            $this->end();
        }else{
            $html = view::ui()->form_start(array(
                'action'=>$this->url.'&act=new_mormal_tag'.$ex_p,
                'id'=>'form_settag',
                'method' => 'post',
                ));
            $html .= view::ui()->form_input(array('title'=>app::get('desktop')->_('标签名'),'vtype'=>'required','name'=>'tag_name'));
            $html .= view::ui()->form_input(array('title'=>app::get('desktop')->_('标签备注'),'name'=>'tag_abbr'));
            $html .= view::ui()->form_input(array('title'=>app::get('desktop')->_('标签颜色'),'type'=>'color','name'=>'tag_bgcolor'));
            //$html .= view::ui()->form_input(array('title'=>app::get('desktop')->_('标签字体景色'),'type'=>'color','name'=>'tag_fgcolor'));
            $html.=view::ui()->form_end();
            $___infomation=app::get('desktop')->_('如果新建的标签已经存在，则此操作变为编辑原标签');

echo <<<EOF
<div style="margin: 5px;" class="notice">
$___infomation
</div>
{$html}
<script>

   \$('form_settag').store('target',{


        onComplete:function(){

            if(window.finderGroup['{$_GET['finder_id']}'])
            window.finderGroup['{$_GET['finder_id']}'].refresh();

            $('form_settag').getParent('.dialog').retrieve('instance').close();

        }

   });

</script>
EOF;
        }
    }

    function tag_edit($id){
        $this->url = '?app='.$_GET['app'].'&ctl='.$_GET['ctl'];
        $mdl_tag = app::get('desktop')->model('tag');
        $tag = $mdl_tag->dump($id,'*');
        $html = view::ui()->form_start(array(
                        'action'=>$this->url.'&act=new_mormal_tag'.$ex_p,
                        'id'=>'tag_form_add',
                        'method' => 'post',
                        ));
            $html .= view::ui()->form_input(array('title'=>app::get('desktop')->_('标签名'),'name'=>'tag_name','value'=>$tag['tag_name']));
            $html .= view::ui()->form_input(array('title'=>app::get('desktop')->_('标签备注'),'name'=>'tag_abbr','value'=>$tag['tag_abbr']));
            $html .= view::ui()->form_input(array('title'=>app::get('desktop')->_('标签颜色'),'type'=>'color','name'=>'tag_bgcolor','value'=>$tag['tag_bgcolor']));
            //$html .= view::ui()->form_input(array('title'=>app::get('desktop')->_('标签字体色'),'type'=>'color','name'=>'tag_fgcolor','value'=>$tag['tag_fgcolor']));
            $html .= '<input type="hidden" name="tag_id" value="'.$id.'"/>';
            $html .= '<input type="hidden" name="app_id" value="'.$tag['app_id'].'"/>';
            $html .= '<input type="hidden" name="type" value="'.$tag['tag_type'].'"/>';
            $html.=view::ui()->form_end();
            echo $html;
echo <<<EOF
<script>
window.addEvent('domready', function(){
    $('tag_form_add').store('target',{
        onComplete:function(){

           if(window.finderGroup['{$_GET['finder_id']}'])
            window.finderGroup['{$_GET['finder_id']}'].refresh();

            if($('tag_form_add').getParent('.dialog'))
            $('tag_form_add').getParent('.dialog').retrieve('instance').close();
        }
    });
});
</script>
EOF;
        exit;
    }

    public function get_view_filter($controller,$model)
    {
        $controller = kernel::single($controller);
        $object_name = $model;
        if(!isset($_POST['view'])) return array();
        list($app_id,$model) = explode('_mdl_',$object_name);
        if($app_id!=$controller->app->app_id){
            return array();
        }
        if(method_exists($controller,'_views')){
            $views = $controller->_views();
         }
        if(isset($views[$_POST['view']])){
            return $views[$_POST['view']]['filter'];
        }

        //自定义筛选器
        $filter = app::get('desktop')->model('filter');
        $_filter = array(
                'model'=>$object_name,
                'app'  =>$_POST['app'],
                'ctl'  =>$_POST['ctl'],
                'act'  =>$_POST['act'],
                'user_id'  => $this->user->user_id,
            );
        $rows = $filter->getList('*',$_filter,0,-1,'create_time asc');
        if($views){
            end($views);
            $view_id = $_POST['view'] - key($views) -1;
        }else{
            $view_id = $_POST['view'] - 1;
        }
        if($rows[$view_id]){
            parse_str($rows[$view_id]['filter_query'],$filter_query);
        }
        return $filter_query;

    }

    /**
     * 记录平台操作日志
     *
     * @param $lang 日志语言包
     * @param $status 成功失败状态
     * @param $admin_name
     * @param $admin_id
     */
    protected final function adminlog($memo = '', $status = 1)
    {
        // 开启了才记录操作日志
        if ( ADMIN_OPERATOR_LOG !== true ) return;

        $queue_params = array(
            'admin_userid'   => $this->user->get_id(),
            'admin_username' => $this->user->get_login_name(),
            'created_time'   => time(),
            'memo'           => $memo,
            'status'         => ($status ? 1 : 0),
            'router'         => request::fullurl(),
            'ip'             => request::getClientIp(),
        );
        return system_queue::instance()->publish('system_tasks_adminlog', 'system_tasks_adminlog', $queue_params);
    }

}
