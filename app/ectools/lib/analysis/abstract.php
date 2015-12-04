<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * 这个类实现报表的数据统计和显示的抽象类
 * @abstract implements ectools_analysis_interface
 * @auther shopex ecstore dev dev@shopex.cn
 * @version 0.1
 * @package ectools.lib.analysis
 */
abstract class ectools_analysis_abstract 
{
	/**
	 * @var protected service object
	 */
    protected $_serivce = null;
    /**
	 * @var protected params array
	 */
    protected $_params = null;
    /**
	 * @array protected pagedata 
	 */
    public $pagedata = array();
    /**
	 * @var protected extra view
	 */
    protected $_extra_view = null;
    /**
	 * @var protected title string
	 */
    protected $_title = null;
	/**
	 * @var public layout type
	 */
    public $layout = 1;
    /**
	 * @var public report type string
	 */
    public $report_type = false;
    /**
	 * @var public log options array
	 */
    public $logs_options = array();
    /**
	 * @var public type options array
	 */
    public $type_options = array();
    /**
	 * @var publ;ic detail options array
	 */
    public $detail_options = array(
        'hidden' => false,
        'force_ext' => false,
    );
    /**
	 * @var public graph options array
	 */
    public $graph_options = array(
        'hidden' => false,
        'iframe_height' => 180,
    );
    /**
	 * @var public rank options array
	 */
    public $rank_options = array(
    
    );
    /**
	 * @var public finder options array
	 */
    public $finder_options = array(
        'hidden' => false,    
    );
	
    /**
     * 构造方法
     * @param object app
     * @return null
     */
    function __construct(&$app) 
    {
        $this->app = $app;
        if(substr(PHP_SAPI_NAME(),0,3) !== 'cli' && base_rpc_service::$is_start != true) {
            if(isset($this->analysis_config)){
                 $this->pagedata['time_shortcut'] = $this->analysis_config['setting']['time_shortcut'];
             }
        }
        $this->_params = array();
        $this->_service = get_class($this);
        $this->_extra_view = array('ectools' => 'ectools/analysis/extra_view.html');
        $this->analysis_config = app::get('ectools')->getConf('analysis_config');
    }//End Function
	
    /**
     * 得到报表日志-各种报表各自实现
     * @param string time
     * @return array 日志信息
     */
    public function get_logs($time) 
    {
        //todo:各自实现
    }//End Function
	
    /**
     * 设置报表统计的参数
     * @param array 需要设置的参数
     * @return object 本类对象
     */
    public function set_params($params) 
    {
        $this->_params = $params;

        if(isset($this->analysis_config)){
            $time_from = date("Y-m-d", time()-(date('w')?date('w')-$this->analysis_config['setting']['week']:7-$this->analysis_config['setting']['week'])*86400);
        }else{
            $time_from = date("Y-m-d", time()-(date('w')?date('w')-1:6)*86400);
        }
        $time_to = date("Y-m-d", strtotime($time_from)+86400*7-1);

        $this->_params['time_from'] = ($this->_params['time_from']) ? $this->_params['time_from'] : $time_from;
        $this->_params['time_to'] = ($this->_params['time_to']) ? $this->_params['time_to'] : $time_to;
        $this->_params['order_status'] = $this->analysis_config['filter']['order_status'];
        return $this;
    }//End Function
	
    /**
     * 设置extra视图
     * @param array view视图数组
     * @return object 本类对象
     */
    public function set_extra_view($array) 
    {
        $this->_extra_view = $array;
        return $this;
    }//End Function
	
    /**
     * 设置service
     * @param object service
     * @return object 本类对象
     */
    public function set_service($service){
        $this->_service = $service;
        return $this;
    }
	
    /**
     * 设置图像方法，设置页面参数
     * @param null
     * @return boolean 成功与否
     */
    public function graph() 
    {
        if($this->graph_options['hidden'] == true){
            $this->pagedata['graph_hidden'] = 1;
            return false;
        }
        foreach($this->logs_options AS $key=>$val){
            $this->pagedata['graph'][$key]['name'] = $this->app->_($val['name']);
        }
        $this->pagedata['target'] = ($this->pagedata['target']) ? $this->pagedata['target'] : 1;
        $this->pagedata['ext_url'] .= '&type='.$this->_params['type'];
        $this->pagedata['ext_url'] .= '&time_from='.$this->_params['time_from'];
        $this->pagedata['ext_url'] .= '&time_to='.$this->_params['time_to'];
        $this->pagedata['ext_url'] .= '&service='.$this->_service;
        $this->pagedata['ext_url'] .= '&callback='.$this->graph_options['callback'];
        $this->pagedata['ext_url'] .= '&report='.$this->_params['report'];
        $this->pagedata['iframe_height'] = $this->graph_options['iframe_height'];
        return true;
    }//End Function
	
    /**
     * 生成页面详细区域信息
     * @param null
     * @return boolean 成功与否
     */
    public function detail() 
    {
        if($this->detail_options['hidden'] == true){
            $this->pagedata['detail_hidden'] = 1;
            return false;
        }
        $detail = array();
        if($this->detail_options['force_ext'] == false){
            $qb = app::get('ectools')->database()->createQueryBuilder();
            if ($analysis_id = $qb->select('id')->from('ectools_analysis')->where('service='.$qb->createPositionalParameter($this->_service))->execute()->fetchColumn())
            {
                $qb = app::get('ectools')->database()->createQueryBuilder();
                $qb->select('target, sum(value) as value')
                   ->from('ectools_analysis_logs')
                   ->where('analysis_id = '.$qb->createPositionalParameter($analysis_id))
                   ->andWhere('flag = 0')
                   ->groupBy('target');
                if(isset($this->_params['type'])) $qb->andWhere('type = '.$qb->createPositionalParameter($this->_params['type']));
                if(isset($this->_params['target'])) $qb->andWhere('target = '.$qb->createPositionalParameter($this->_params['target']));
                if(isset($this->_params['time_from'])) $qb->andWhere('time = '. $qb->createPositionalParameter(strtotime(sprintf('%s 00:00:00', $this->_params['time_from']))));
                if(isset($this->_params['time_to'])) $qb->andWhere('time = '. $qb->createPositionalParameter(strtotime(sprintf('%s 23:59:59', $this->_params['time_to']))));
                $rows = $qb->execute()->fetchAll();
                foreach($rows AS $row)
                {
                    $tmp[$row['target']] = $row['value'];
                }
            }
            
            foreach($this->logs_options AS $target=>$option)
            {
                $detail[$option['name']]['value'] = ($tmp[$target]) ? $tmp[$target] : 0;
                $detail[$option['name']]['memo'] = $this->logs_options[$target]['memo'];
                $detail[$option['name']]['icon'] = $this->logs_options[$target]['icon'];
            }
        }
        if(method_exists($this, 'ext_detail')){
            $this->ext_detail($detail);
        }
        foreach($detail AS $key=>$val){
            $name = $this->app->_($key);
            $data[$name]['value'] = $val['value'];
            $data[$name]['memo'] = $this->app->_($val['memo']);
            $data[$name]['icon'] = $val['icon'];
        }
        $this->pagedata['detail'] = $data;
        return true;
    }//End Function
	
    /**
     * 统计的类型-内容
     * @param null
     * @return string 类型值
     */
    public function get_type() 
    {
        //todo:各自实现
    }//End Function
	
    /**
     * 统计频率
     * @param null
     * @return string 频率值
     */
    public function rank() 
    {
        //todo:各自实现
    }//End Function
	
    /**
     * 生成各自统计内容的finder
     * @param null
     * @return array - finder统一格式的数组
     */
    public function finder() 
    {
        //todo:各自实现
    }//End Function
	
    /**
     * 生成头部信息，统计图表的头部
     * @param null
     * @return null
     */
    public function headers() 
    {
        $this->pagedata['title'] = $this->_title;
        $this->pagedata['time_from'] = $this->_params['time_from'];
        $this->pagedata['time_to'] = $this->_params['time_to'];
        $this->pagedata['today'] = date("Y-m-d");
        $this->pagedata['yesterday'] = date("Y-m-d", time()-86400);
        if(isset($this->analysis_config)){
            $this->pagedata['this_week_from'] = date("Y-m-d", time()-(date('w')?date('w')-$this->analysis_config['setting']['week']:7-$this->analysis_config['setting']['week'])*86400);
        }else{
            $this->pagedata['this_week_from'] = date("Y-m-d", time()-(date('w')?date('w')-1:6)*86400);
        }
        $this->pagedata['this_week_to'] = date("Y-m-d", strtotime($this->pagedata['this_week_from'])+86400*7-1);
        $this->pagedata['last_week_from'] = date("Y-m-d", strtotime($this->pagedata['this_week_from'])-7*86400);
        $this->pagedata['last_week_to'] = date("Y-m-d", strtotime($this->pagedata['last_week_from'])+86400*7-1);
        $this_month_t = date('t');
        $this->pagedata['this_month_from'] = date("Y-m-" . 01);
        $this->pagedata['this_month_to'] = date("Y-m-" . $this_month_t);
        $last_month_t = date('t', strtotime("last month"));
        $this->pagedata['last_month_from'] = date("Y-m-" . 01, strtotime("last month"));
        $this->pagedata['last_month_to'] = date("Y-m-" . $last_month_t, strtotime("last month"));
        $this->pagedata['layout'] = $this->layout;

        if($this->report_type){
            $this->pagedata['report'] = $this->_params['report'];
            $this->pagedata['report_type'] = $this->report_type;
            $this->pagedata['month'] = array(1,2,3,4,5,6,7,8,9,10,11,12);
            for($i = 2000;$i<=date("Y",time());$i++){
                $year[] = $i;
            }
            $this->pagedata['year'] = $year;
            $this->pagedata['from_selected'] = explode('-',$this->_params['time_from']);
            $this->pagedata['to_selected'] = explode('-',$this->_params['time_to']);
        }

        if($this->type_options['display'] == true){
            $this->pagedata['type_display'] = true;
            $this->pagedata['typeData'] = $this->get_type();
            $this->pagedata['type_selected'] = $this->_params['type_id'];
        }
    }//End Function
	
   /**
    * 展示页面内容的方法
    * @param boolean true - 提出内容，相当于fetch，false echo内容
    * @return string html结果内容
    */
    public function display($fetch=false) 
    {
        $this->headers();
        $this->detail();
        $this->graph();
        $this->rank();

        if($this->finder_options['hidden']){
            foreach($this->_extra_view AS $app_id=>$view){
                $content = view::make($view, $this->pagedata)->render();
                break;
            }
        }else{
            $controller = kernel::single('desktop_controller');
            $controller->pagedata = $this->pagedata;
            $finder = $this->finder();
            $finder['params']['base_filter'] = $this->_params;
            $finder['params']['top_extra_view'] = $this->_extra_view;
            ob_start();
            echo $controller->finder($finder['model'], $finder['params']);
            $content = ob_get_contents();
            ob_end_clean();
        }

        if($fetch){
            
            return $content;
        }else{
            echo $content;
        }
    }//End Function
	
    /**
     * fetch 页面的html
     * @param null
     * @return string html页面nei'ron
     */
    public function fetch() 
    {
        return $this->display(true);
    }//End Function

}//End Function
