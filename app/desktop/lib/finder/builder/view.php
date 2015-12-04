<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_finder_builder_view extends desktop_finder_builder_prototype{
  	/**
	 * 是否显示设置标签按钮
	 *
	 * @var bool
	 */
    public $use_buildin_set_tag = false;
  	/**
	 * 是否显示标签管理按钮
	 *
	 * @var bool
	 */
    public $use_buildin_tagedit =true;
  	/**
	 * 是否显示删除按钮
	 *
	 * @var bool
	 */
    public $use_buildin_delete = true;
  	/**
	 * 是否显示导出按钮
	 *
	 * @var bool
	 */
    public $use_buildin_export = false;
  	/**
	 * 是否显示导入按钮
	 *
	 * @var bool
	 */
    public $use_buildin_import = false;
  	/**
	 * 是否显示高级筛选按钮
	 *
	 * @var bool
	 */
    public $use_buildin_filter = false;

  	/**
	 * 是否显示列配置按钮
	 *
	 * @var bool
	 */
    public $use_buildin_setcol = true;
  	/**
	 * 是否显示刷新按钮
	 *
	 * @var bool
	 */
    public $use_buildin_refresh = true;
  	/**
	 * 是否显示每条记录前的复选按钮
	 *
	 * @var bool
	 */
    public $use_buildin_selectrow =true;
  	/**
	 * 当存在条目详细下拉时, 是否显示弹出框按钮
	 *
	 * @var bool
	 */
    public $allow_detail_popup =false;
  	/**
	 * 是否支持保存搜索结果功能
	 *
	 * @var bool
	 */
    public $use_save_filter = true;
  	/**
	 * 支持最大actions数量
	 *
	 * @var int
	 */
    public $max_actions =7;
  	/**
	 * 删除时提示信息
	 *
	 * @var string
	 */
    public $delete_confirm_tip = '';
  	/**
	 * 当前页码
	 *
	 * @var string
	 */
    public $currentPage = 1;

  	/**
	 * 标准finder视图, 通用数据
	 *
	 * @var bool
	 */
    protected $commonPagedata = array();


    protected function getViewFilter()
    {
        $inputView = input::get('view');
        // 获取tab视图
        $views = $this->getViews();
        //if(count($views) && $this->use_view_tab){
        if(!empty($views) && $this->use_view_tab){
            $this->tab_view_count = 0;
            foreach((array)$views as $view){
                if($view['addon'])
                    $this->tab_view_count += $view['addon'];

            }
            if(!$_GET['view']){
                $default_view = reset($views);
                $view_filter = $default_view['filter'];
            }else{
                $view_filter = (array)$views[$_GET['view']]['filter'];
                // 如果有view_tab的情况下,则将当前view的总数记下，减少同一finder两次进行count计算，增加性能
                if($views[$_GET['view']]['addon']!='_FILTER_POINT_'){
                    $this->current_view_count = $views[$_GET['view']]['addon'];
                }
            }
        }
        $this->__view_filter = $view_filter;

        return array_merge(
            (array)$this->base_filter,
            (array)$view_filter);

    }

    public function getCommonPagedata()
    {
        if (!$this->commonPagedata)
        {
            $finder_name = $this->name;
            $pagedata['title'] = $this->title;
            $pagedata['name'] = $this->name;
            $pagedata['var_name'] = $this->var_name;
            $pagedata['url'] = $this->url;

            $pagedata['finder_name'] =  $finder_name;
            $pagedata['finder_aliasname'] =  $this->finder_aliasname;
            $this->commonPagedata = $pagedata;
        }

        return $this->commonPagedata;
    }

    public function initParams()
    {
        $this->params = array_merge(
            (array)$this->getViewFilter(),
            (array)$_POST);
        /** 用于打开的input_object object_base_filter **/
        if (isset($_GET['obj_filter'])&&$_GET['obj_filter']) $this->params = array_merge($this->params,array('obj_filter'=>$_GET['obj_filter']));

        

        // 过滤掉_finder参数
        unset($this->params['_finder']);

        // params过滤
        foreach($this->params as $k=>$v)
        {
            if(!is_array($v)&&$v!==false)
            {
                $this->params[$k] = trim($v);
            }

            if($this->params[$k]==='')
            {
                unset($this->params[$k]);
            }
        }
        $dbeav_filter = kernel::single('desktop_finder_filter');
        $dbeav_filter->createFinderFilter($this->params, $this->object);

    }


    public function setupShowColumns()
    {
        $allCols = $this->all_columns();
        // 所有列自定义长度数组
        $col_width_set = app::get('desktop')->getConf('colwith.'.$this->object_name.'_'.$this->finder_aliasname.'.'.$this->controller->user->user_id);

        foreach($this->getColumnNames() as $col)
        {
            // 检查是否自定义列是否还存在
            if(isset($allCols[$col]))
            {
                $colArray[$col] = &$allCols[$col];

                // 列width
                $colArray[$col]['width'] = $col_width_set[$col] ?: 150;
            }
        }

        $this->showColumns = $colArray;
    }

    public function setupPagerContext()
    {
        $currentPage = input::get('page', 1);
        $perPage = $this->pagelimit;
        $model = $this->object;

        // 如果有view_tab的情况下,则直接使用计算顶部view_tab的addon时记下的总数，减少同一finder两次进行count计算，增加性能
        $itemCount = $this->current_view_count ? $this->current_view_count : $model->count($this->params);

        $total = ceil($itemCount/$perPage);
        if($currentPage < 0 || ($currentPage >1 && $currentPage > $total)){
            $currentPage = 1;
        }
        // 修正当前页参数
        $this->currentPage = $currentPage;

        $from = $perPage*($currentPage-1)+1;
        $to = ($currentPage*$perPage>$itemCount) ? $itemCount : $from+$perPage-1;

        $this->currentPage = $currentPage;

        $this->pagerInfo = array(
            'current'=> $currentPage,
            'count'=>$itemCount,
            'total'=> $total?$total:1,
            'from' => $from,
            'to' => $to,
        );
    }
    
    public function getCurrentPage()
    {
        return $this->currentPage;
    }


    public function getFinderTitleHtml()
    {
        $pagedata = $this->getCommonPagedata();

        // 是否开启刷新
        $pagedata['use_buildin_refresh'] =  $this->use_buildin_refresh;
        // 是否开启列选择器
        $pagedata['use_buildin_setcol'] =  $this->use_buildin_setcol;
        // 是否开启筛选保存器
        $pagedata['use_save_filter'] = $this->use_save_filter;

        // finder列表顶部自定义显示html信息
        if($this->top_extra_view)
        {
            $pagedata['top_extra'] = "";

            foreach($this->top_extra_view as $app=>$view)
            {
                $pagedata = array_merge($this->controller->pagedata, $pagedata);
                $pagedata['top_extra'].= view::make($view, $pagedata)->render();
            }
        }

        $pagedata['pinfo'] = $this->pagerInfo;

        return view::make('desktop/finder/view/finder_title.html', $pagedata)->render();
    }

    public function getFinderActionsHtml()
    {
        $finder_name = $this->name;
        $actions = $this->actions;

        if($this->use_buildin_set_tag){
            $_tagaction =array(
                    'label'=>app::get('desktop')->_('标签'),
                    'icon'=>'label.gif',
                    'group'=>array(
    array('label'=>app::get('desktop')->_('为选中项打标签'),'submit'=>$this->url.'&action=tag','target'=>'dialog::{width:400,title:\''.app::get('desktop')->_('设置标签').'\'}')
                            )
                        );


            if($this->has_tag==true&&$this->use_buildin_tagedit){
                $_tagediturl='?app=desktop&ctl=default&act=alertpages&nobuttion=1&goto='.urlencode('?app='.$this->app->app_id.'&ctl='.$_GET['ctl'].'&act=tags&nobuttion=1&type='.$this->short_object_name);
                if (($obj = kernel::service('desktop.tags.setting')) && method_exists($obj,'gen_target_url')){
                    $obj->gen_target_url($this->short_object_name,$this->app->app_id,$_tagediturl);
                }
                array_push($_tagaction['group'],array('label'=>'_SPLIT_'),array('label'=>app::get('desktop')->_('标签设置'),'href'=>$_tagediturl,'target'=>'_blank'));

            }
            $actions[] = $_tagaction;

        }

        if($this->use_buildin_delete){
            $actions[] = array('label'=>app::get('desktop')->_('删除'),'icon'=>'del.gif','confirm'=>$this->delete_confirm_tip?$this->delete_confirm_tip:app::get('desktop')->_('确定删除选中项？删除后不可恢复'),'submit'=>$this->url.'&action=dodelete');
       }

        if($this->use_buildin_export){
            $export_url = '?app=importexport&ctl=admin_export&act=export_view&_params[app]='.$this->app->app_id.'&_params[mdl]='.$this->object_name;
            $actions[] = array('label'=>app::get('desktop')->_('导出'),'icon'=>'download.gif','submit'=>$export_url.'&action=export','target'=>'dialog::{width:400,height:170,title:\''.app::get('desktop')->_('导出').'\'}');
        }

        if($this->use_buildin_import){
            $import_url = '?app=importexport&ctl=admin_import&act=import_view&_params[app]='.$this->app->app_id.'&_params[mdl]='.$this->object_name;
            $actions[] = array('label'=>app::get('desktop')->_('导入'),'icon'=>'upload.gif','href'=>$import_url.'&action=import','target'=>'dialog::{width:400,height:150,title:\''.app::get('desktop')->_('导入').'\'}');
        }

        foreach((array)$this->service_object as $object)
        {
           $actions = array_merge((array)$actions,(array)$object->actions);
        }


        $max_action = $this->max_actions;
        $i=0;

        if (isset($actions) && $actions)
        {
            foreach($actions as $key=>$item){

            //  if(!$item['label']){continue;}

                if($item['href']){$item['href'] = $item['href'].'&_finder[finder_id]='.$finder_name.'&finder_id='.$finder_name;
                }else{
                   $item['href'] ="javascript:void(0);";
                }
                if($item['submit']){$item['submit'] = $item['submit'].'&finder_id='.$finder_name;}

                $show_actions[] = $item;
                unset($actions[$key]);
                if($i++==$max_action-1){
                    break;
                }
            }
            $other_actions = $actions;
        }
        $pagedata = $this->getCommonPagedata();

        $pagedata['show_actions'] =  $show_actions;
        $pagedata['other_actions'] =  $other_actions;


        $pagedata['use_buildin_filter'] =  $this->use_buildin_filter;



        /** 判断是否要显示归类视图 **/
        $pagedata['haspacket'] = $this->getViews() ? true : false;

        if(method_exists($this->object,'searchOptions'))
            $searchOptions =  $this->object->searchOptions();


        if( is_array($searchOptions) && $this->__view_filter ) {
            foreach( $searchOptions as $key => $val ) {
                if( isset($this->__view_filter[$key]) ) {
                    unset($searchOptions[$key]);
                }
            }
        }
        $pagedata['searchOptions'] = $searchOptions;
        $pagedata['__search_options_default_label'] = current($searchOptions);

        return view::make('desktop/finder/view/actions.html', $pagedata)->render();

    }

    public function getFinderTableHeaderHtml()
    {
        $pagedata = $this->getCommonPagedata();

        $pagedata['inputhtml'] = $this->toinput($this->params);
        $pagedata['subheader'] = $this->getFinderTableSubHeaderHtml();

        $query = $_GET;
        unset($query['page']);
        $query = http_build_query($query);
        $pagedata['query'] = $query;

        return view::make('desktop/finder/view/header.html', $pagedata)->render();
    }

    public function getFinderTableSubHeaderHtml()
    {
        $colArray = $this->getShowColumns();
        $pagedata = $this->getCommonPagedata();
        // todo 临时策略
        $pagedata['detail_url'] = $this->detail_url;
        // todo 临时策略, 目的替换 column_col_html
        $pagedata['showColumnsDefines'] = $colArray;

        $pagedata['orderBy'] = $this->orderBy;
        $pagedata['orderType'] = $this->orderType;

        $pagedata['pinfo'] = $this->pagerInfo;
        $pagedata['body'] = $body;

        $pagedata['filter_td_html'] = $filter_td_html;
        $pagedata['use_buildin_selectrow'] =  $this->use_buildin_selectrow;

        return view::make('desktop/finder/view/subheader.html', $pagedata)->render();
    }


    public function getFinderPagerHtml()
    {
        $pre_btn_addon = $this->pagerInfo['current']>1?'':'disabled="disabled"';
        $next_btn_addon = $this->pagerInfo['current']<$this->pagerInfo['total']?'':'disabled="disabled"';

        $from = $this->pagerInfo['from'];
        $to = $this->pagerInfo['to'];

        $pager = view::ui()->desktoppager(array(
            'current'=>$this->pagerInfo['current'],
            'total'=>$this->pagerInfo['total'],
            'link'=>'javascript:'.$this->var_name.'.page(%d);'
            ));

        $pagedata = $this->getCommonPagedata();

        $pagedata['perPageSettingList'] = $this->plimit_in_sel;

        $pagedata['plimit'] = $this->pagelimit;
        $pagedata['from'] = $from;
        $pagedata['to'] = $to;

        $pagedata['pre_btn_addon'] = $pre_btn_addon;
        $pagedata['next_btn_addon'] = $next_btn_addon;
        $pagedata['pager'] = $pager;

        $pagedata['pinfo'] = $this->pagerInfo;

        return view::make('desktop/finder/view/pager.html', $pagedata)->render();
    }

    function getFinderScriptHtml()
    {

        $finderOptions = array(
            'selectName'=>$this->dbschema['idColumn'].'[]',
            'object_name'=>$this->object_name,
            'finder_aliasname'=>$this->finder_aliasname,
            'packet'=>$this->getViews() ? true : false,
        );

        $finderOptions = ecos_cactus('desktop','finder_builder_view_script_gen_finderoptions',$this->getViews(),$this->options,$finderOptions);

        return <<<EOF
<script>
Ex_Loader('finder',function(){
 finderDestory();
 var finderOption={$finderOptions};
 {$this->var_name} = new Finder("{$this->name}",finderOption);
 });
</script>
EOF;

    }

    public function main()
    {
        $this->html_script = '';
        $this->html_header = '';
        $this->html_body   = '';
        $this->html_footer = '';
        $this->html_pager  = '';

        // 生成筛选参数
        $this->initParams();
        // 初始化排序
        $this->orderBy = $this->getOrderByColumn();
        $this->orderType = $this->getOrderByType();
        $this->pagelimit = $this->getPageLimit();
        $this->var_name = 'window.finderGroup[\''.$this->name.'\']';

        // 如果存在注册的finder detail tab. 则做相应处理
        if($this->detail_pages)
        {
            $this->detail_url = $this->url.'&action=detail&finder_id='.$this->name;
        }

        $this->setupPagerContext();
        $this->setupShowColumns();

        if(!$_POST['_finder']['in_pager']){
            $output = $output = $this->controller->sidePanel().
                  $this->getFinderScriptHtml().
                  '<!-----.mainHead-----'.
                  $this->getFinderTitleHtml().
                  $this->getFinderActionsHtml().
                  $this->getFinderTableHeaderHtml().
                  '-----.mainHead----->'.
                  $this->getFinderTableRowsHtml().
                  '<!-----.mainFoot-----'.
            //html_footer.
                  $this->getFinderFooterHtml().
                  '-----.mainFoot----->';

        }
        else
        {
            $output = '<!-----.pager-----'.$this->getFinderPagerHtml().'-----.pager----->'.
                    $this->getFinderTableRowsHtml().
                '<!-----.innerheader-----'.$this->getFinderTableSubHeaderHtml().'-----.innerheader----->';
        }

       return $output;

    }

    public function getFinderFooterHtml()
    {
        $pagedata = $this->getCommonPagedata();
        $pagedata['pager'] = $this->getFinderPagerHtml();
        return view::make('desktop/finder/view/footer.html', $pagedata)->render();
    }


    function toinput($params){
        $html = null;
        $this->_toinput($params['from'],$ret,$params['name']);
        foreach((array)$ret as $k=>$v){
            $html.='<input type="hidden" name="'.$k.'" value="'.$v."\" />\n";
        }
        return $html;
    }

    function _toinput($data,&$ret,$path=null){
        foreach((array)$data as $k=>$v){
            $d = $path?$path.'['.$k.']':$k;
            if(is_array($v)){
                $this->_toinput($v,$ret,$d);
            }else{
                $ret[$d]=$v;
            }
        }
    }

    public function getShowColumns()
    {
        if (!$this->showColumns) $this->setupShowColumns();
        return $this->showColumns;
    }

    public function getFinderTableRowsHtml()
    {
        $colArray = $this->getShowColumns();

        $list = $this->prepareFinderTableRows();

        $body = $this->item_list_body($list);
        

        $pagedata = $this->getCommonPagedata();

        $pagedata['detail_url'] = $this->detail_url;
        $pagedata['showColumnsDefines'] = $colArray;

        $pagedata['orderBy'] = $this->orderBy;
        $pagedata['orderType'] = $this->orderType;

        $pagedata['pinfo'] = $this->pagerInfo;
        $pagedata['body'] = $body;

        $pagedata['filter_td_html'] = $filter_td_html;
        $pagedata['use_buildin_selectrow'] =  $this->use_buildin_selectrow;

        return view::make('desktop/finder/view/body.html', $pagedata)->render();
    }


    public function prepareFinderTableRows()
    {
        $colArray = $this->getShowColumns();
        $modifier_object = new modifiers;


        $order = $this->orderBy?$this->orderBy.' '.$this->orderType:'';

        $origList = $finalList = $midList = $this->object->getlist('*',$this->params,($this->getCurrentPage()-1)*$this->pagelimit,$this->pagelimit,$order);
        foreach($origList as $k => $row)
        {
            // 对原始列进行加工, 主要是为了自定义列
            $midList[$k]['idColumn'] = $this->dbschema['idColumn'];
            $midList[$k]['app_id'] = $row['app_id']?$row['app_id']:$this->app->app_id;
            $midList[$k]['tag_type'] = $row['tag_type']?$row['tag_type']:$this->short_object_name;
            // 为了在任何情况下可以取到行pkey值
            $finalList[$k]['_id_'] = $row[$this->dbschema['idColumn']];

            foreach ($this->getColumnNames() as $columnName)
            {
                // 设置列存储表
                $colLists[$columnName][$k] = $row[$columnName];
                $finalList[$k][$columnName] = &$colLists[$columnName][$k];
            }
        }

        

        foreach($colArray as $columnName => $columnDefine)
        {
            // service 注册
            if($columnDefine['type'] == 'func')
            {
                $this->modifyRowsWithFunc($columnDefine['ref'], $colLists[$columnName], $midList);
            }
            // model内定义处理
            elseif(method_exists($this->object,'modifier_'.$columnName))
            {
                $this->modifyRowsWithModifier($columnName, $colLists[$columnName]);
            }
            // dbschema 中type为数组, 代表enum
            elseif(is_array($columnDefine['type']))
            {
                $this->modifyRowsForEnumType($columnDefine['type'], $colLists[$columnName]);
            }
            // dbschema 中type为外表关联字段
            elseif(substr($columnDefine['type'], 0, 6)=='table:')
            {
                $this->modifyRowsWithRelatedKey($columnName, $columnDefine['type'], $colLists[$columnName]);
            }
            // dbschema 中type为预定义函数
            elseif(method_exists($modifier_object,$columnDefine['type']))
            {
                $this->modifyRowsWithBuildin($colLists[$columnName], $columnDefine['type'], $modifier_object);

            }

        }
        return $finalList;
    }

    public function modifyRowsWithFunc($closure, &$colList, $list)
    {
        $colList = call_user_func($closure, $colList, $list);

    }

    public function modifyRowsWithModifier($columnName, &$colList)
    {
        $func = 'modifier_'.$columnName;
        $model = $this->object;
        $colList = call_user_func([$model, $func], $colList);
    }

    public function modifyRowsWithRelatedKey($columnName, $type, &$colList)
    {
        
        list(,$relatedModelName,$foreignKey) = explode(':',$type);
        list($relatedModelName, $appId) = explode('@', $relatedModelName);
        $appId = $appId ?: $this->object->app->app_id;

        $model = $this->object;
        
        $relatedModel = app::get($appId)->model($relatedModelName);

        $foreignKey = $foreignKey ?: $relatedModel->textColumn;
        // 这个看似多余的步骤是为了阻断$colList数组的引用传染.
        foreach($colList as $k=>$v) $copyColList[$k] = $v;
        
        $relatedRows = $relatedModel->getList($relatedModel->idColumn.','.$foreignKey, [$relatedModel->idColumn=>array_unique($copyColList)]);
        $relatedRows = utils::array_change_key($relatedRows, $relatedModel->idColumn);
        foreach ($colList as $k => $v)
        {
            $colList[$k] = $relatedRows[$v][$foreignKey];
        }
    }

    Public function modifyRowsForEnumType($enum, &$colList)
    {
        foreach($colList as $k => $field)
        {
            $colList[$k] = $enum[$field];
        }

    }

    public function modifyRowsWithBuildin(&$colList, $type, $modifier_object)
    {
        call_user_func(array($modifier_object, $type), $colList);
    }

    public function item_list_body($list)
    {
        if(!$list) return '';
        $showColumnsDefines = $this->getShowColumns();
        // 主键键值
        $idKey = $this->dbschema['idColumn'];

        // 获取star列列表
        $favstar_rows = app::get('desktop')->getConf('favstar.'.$this->object_name.'_'.$this->finder_aliasname.'.'.$this->controller->user->user_id);

        // 确认行下拉, 默认tab
        if(is_array($this->detail_pages))
        {
            $default_detail = '&finderview='.key($this->detail_pages);
        }
        // 行下拉展开url地址
        $detail_url = $this->detail_url;

        // 确认选择列式 radio模式还是checkbox
        $singleselect = $_GET['singleselect'] ? 'radio' : 'checkbox';

        $use_buildin_selectrow = $this->use_buildin_selectrow;
        $allow_detail_popup = $this->allow_detail_popup;
        $pinfo = $this->pagerInfo;

        $pagedata = compact('idKey','list', 'showColumnsDefines', 'default_detail', 'favstar_rows',
                            'use_buildin_selectrow', 'singleselect', 'detail_url', 'allow_detail_popup',
                            'pinfo');

        return view::make('desktop/finder/view/list.html', $pagedata)->render();
    }
}
