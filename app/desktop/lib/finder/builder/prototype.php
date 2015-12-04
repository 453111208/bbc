<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
define('COLUMN_IN_HEAD','HEAD');
define('COLUMN_IN_TAIL','TAIL');
class desktop_finder_builder_prototype{

    public $plimit_in_sel = array(100,50,20,10);
    public $has_tag = 1;
    public $title = '列表';
    public $object_method = array(
            'count'=>'count',   //获取数量的方法名
            'getlist'=>'getlist',   //获取列表的方法名
        );

	/**
	 * 扩展列
	 *
	 * @var bool
	 */
    public $addon_columns = array();

	/**
	 * detail处理closure
	 *
	 * @var array
	 */
    public $detail_pages = array();
    public $addon_actions = array();
    public $finder_aliasname = 'default';
	/**
	 * 扩展列
	 *
	 * @var 自定义默认显示列
	 */
    public $finder_cols = '';
    public $alertpage_finder = false;

	/**
	 * 所有列的列头
	 *
	 * @var array
	 */
    private $columns = null;

	/**
	 * 全局视图
	 *
	 * @var array
	 */
    private $views = null;
    
    
	/**
	 * 是否开启tab标签 
	 *
	 * @var bool
	 */
    public $use_view_tab = true;

    function __construct($controller){
        $this->controller = $controller;

        $this->name = ecos_cactus('desktop','finder_find_id',$_REQUEST['_finder']['finder_id']);
    }

	/**
	 * 初始化基准url
	 *
	 * @param  \Closure  $callback
	 * @return string
	 */
    protected function initBasicUrl()
    {
        $url = '?';
        ecos_cactus('desktop','finder_make_get',$this->name);
        $query = http_build_query($_GET);
        $this->url = $url.$query;        
    }

    /**
     * 初始化扩展列和detail
     *
     * @return void
     */
    protected function initExtendsColumnAndDetail()
    {
        $service_list = array();

        foreach(kernel::servicelist('desktop_finder.'.$this->object_name) as $name=>$object)
        {
            $service_list[$name] = $object;
        }
        
        foreach(kernel::servicelist('desktop_finder.'.$this->object_name.'.'.$this->finder_aliasname) as $name=>$object)
        {
            $service_list[$name] = $object;
        }

        foreach($service_list as $name=>$object){
            foreach(get_class_methods($object) as $method){
                switch(substr($method,0,7)){
                    case 'column_':
                        $this->addon_columns[] = array($object,$method);
                        break;

                    case 'detail_':
                        if(!$this->alertpage_finder)//如果是弹出页finder, 则去掉详细查看按钮
                            $this->detail_pages[$method] = array($object,$method);
                        break;
                }
            }

            $this->service_object[] = $object;
        }        
    }

    protected function initExtends()
    {
        $this->initExtendsColumnAndDetail();
    }

    public function work($full_object_name)
    {
        $this->object_name = $full_object_name;
        list($object_app, $short_object_name) = ecos_cactus('desktop','finder_split_model',$full_object_name);
        $this->short_object_name = $short_object_name;
        $this->object = app::get($object_app)->model($this->short_object_name);
        $this->has_tag = $this->object->has_tag;
        $this->dbschema = $this->object->schema;

        $this->initBasicUrl();
        $this->initExtends();
        
        return $this->main();
    }
    
	/**
	 * 获取所有finders上当前应该显示的字段名列表
	 *
	 * @return array
	 */
    public function getColumnNames(){
        if(!$this->columns){
            // 自定义选择显示哪些字段 
            $cols = $this->app->getConf('view.'.$this->object_name.'.'.$this->finder_aliasname.'.'.$this->controller->user->user_id);
            // finder_cols 可以在finder options中定义;
            $cols = $cols ? $cols : $this->finder_cols;
            if ($cols)
            {
                $this->columns = explode(',',$cols);
            }
            else
            {
                $this->columns =  $this->getDefaultColumnNames();
            }
        }
        return $this->columns;
    }

    protected function getDefaultColumnNames()
    {
        // dbschema中配置默认显示字段
        $defaultInListColumnNames = (array)$this->dbschema['default_in_list'];
        // 函数自定义字段
        $funcColumnNames = $this->getFuncColumnNames();
        // 生成最终需要显示的所有字段
        $allColumnNames = array_merge($funcColumnNames, $defaultInListColumnNames);
        $collection = collect($allColumnNames);
        // 增加默认order100
        $collection->map(function ($columnDefine, $key) {
            if (!$columnDefine['order']) $columnDefine['order'] = 100;
            return $item;
        });
        // 按照order值进行排序
        $collection->sortBy(function ($columnDefine, $key) {
            return $columnDefine['order'];
        });

        return $collection->values()->all();
    }

    protected function getFuncColumnNames()
    {
        return array_keys($this->func_columns());
    }

    protected function getSchemaColumnNames()
    {
        return array_keys($this->all_columns());
    }

    function processOrderBy(){
        if(isset($_POST['_finder']['orderBy'])||isset($_GET['_finder']['orderBy'])){
            $this->orderBy = $_POST['_finder']['orderBy']?$_POST['_finder']['orderBy']:$_GET['_finder']['orderBy'];
            $this->orderType = $_POST['_finder']['orderType']?$_POST['_finder']['orderType']:$_GET['_finder']['orderType'];
        }
    }

    public function getOrderByColumn()
    {
        return input::get('_finder.orderBy');
    }

    public function getOrderByType()
    {
        return input::get('_finder.orderType');
    }

    //页码处理
    public function getPageLimit(){
        $user_id = $this->controller->user->user_id;
        if(isset($_POST['plimit']) && $_POST['plimit']){
            $this->app->setConf('lister.pagelimit.'.$user_id,$_POST['plimit']);
            return $_POST['plimit'];
        }else{
            $plimit = $this->app->getConf('lister.pagelimit.'.$user_id);
            return $plimit?$plimit:20;
        }
    }

    function &all_columns(){
        if(!$this->alertpage_finder)
            $func_columns = $this->func_columns();
        return ecos_cactus('desktop','finder_all_columns',$this->dbschema['in_list'], $func_columns, $this->dbschema['columns']);
    }

    function &func_columns(){
        if(!isset($this->func_list)){
            $default_with = app::get('desktop')->getConf('finder.thead.default.width');
            $return = array();
            $this->func_list = &$return;
            //标签列
            if($this->has_tag)
                $this->addon_columns[] = array(kernel::single('desktop_finder_tagcols'),'column_tag');

            foreach($this->addon_columns as $k=>$function){
                $func['type'] = 'func';
                $func['width'] = $function[0]->{$function[1].'_width'}?$function[0]->{$function[1].'_width'}:$default_with;
                $func['label'] = $function[0]->{$function[1]};
                if($function[0]->{$function[1].'_order'}==COLUMN_IN_TAIL){
                    $func['order'] = 100;
                }elseif($function[0]->{$function[1].'_order'}==COLUMN_IN_HEAD){
                    $func['order'] = 1;
                }else{
                    $func['order'] = $function[0]->{$function[1].'_order'};
                }

                $func['ref'] = $function;
                $func['sql'] = '1';
                $func['alias_name'] = $function[1];
                if($func['label']){ //只有有名称，才能被显示
                    $return[$function[1]] = $func;
                    //$return[$function[1]] = $func;
                }
            }
        }
        return $this->func_list;
    }
    
    public function getViews()
    {
        if($this->views === null)
        {
        
            if(!$this->use_view_tab) return array();
            list($app_id,$model) = explode('_mdl_',$this->object_name);
            if($app_id!=$this->controller->app->app_id){
                return array();
            }

            if(method_exists($this->controller,'_views')){
                $views = $this->controller->_views();
                $views_temp = ecos_cactus('desktop','finder_builder_prototype_get_view_modifier',$views, $this->finder_aliasname, $views_temp);
            }
            //            echo '<pre>';
            //            var_dump($views_temp);exit;

            //自定义筛选器
            $filter = app::get('desktop')->model('filter');
            $_filter = array(
                'model'=>$this->object_name,
                'app'  =>$_GET['app'],
                'ctl'  =>$_GET['ctl'],
                'act'  =>$_GET['act'],
                'user_id'  => $this->controller->user->user_id,
            );
            $rows = $filter->getList('*',$_filter,0,-1,'create_time asc');
            if(!$views_temp&&$rows[0]){
                $object = app::get($app_id)->model($model);
                $views_temp = array(
                    0=>array('label'=>app::get('desktop')->_('全部'),'optional'=>false,'filter'=>"",'addon'=>$object->count()),
                );
            }
            $extends = $this->_get_args();

            krsort($views_temp);
            $view = array_slice($views_temp,0,1,true);
            $view = array_keys($view);
            $view = $view[0] + 1;
            ksort($views_temp);
            //$view = count($views_temp);
            foreach($rows as $row){
                $_url_array = array('app'=>$_filter['app'],'act'=>$_filter['act'],'ctl'=>$_filter['ctl'],'view'=>$view);
                $view++;
                $_url_array = ecos_cactus('desktop','finder_builder_prototype_get_view_url_array',$extends, $_url_array);
                $url = url::route('shopadmin', $_url_array);
                unset( $_url_array );
                $views_temp[] = ecos_cactus('desktop','finder_builder_prototype_get_views',$row, $url);
                
            }
            $this->views = (array)$views_temp;
        }
        
        return $this->views;
    }

    protected function _get_args() {
        return ecos_cactus('desktop','finder_get_args',$_GET);
    }

    public function finder_get_view_filter(){
        if(!isset($_POST['view'])) return array();
        list($app_id,$model) = explode('_mdl_',$this->object_name);
        if($app_id!=$this->controller->app->app_id){
            return array();
        }
        if(method_exists($this->controller,'_views')){
            $views = $this->controller->_views();
        }
        if(isset($views[$_POST['view']])){
            return $views[$_POST['view']]['filter'];
        }

        //自定义筛选器
        $filter = app::get('desktop')->model('filter');
        $_filter = array(
                'model'=>$this->object_name,
                'app'  =>$_GET['app'],
                'ctl'  =>$_GET['ctl'],
                'act'  =>$_GET['act'],
                'user_id'  => $this->controller->user->user_id,
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
}
