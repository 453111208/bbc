<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class ectools_ctl_admin_payment_notice extends desktop_controller{
    
	public function __construct($app)
	{
		parent::__construct($app);
        $this->app = $app;
		header("cache-control: no-store, no-cache, must-revalidate");
	}
	
	/**
	 * 设置提示信息
	 */
    public function index()
	{
       $this->basic();
    }
	
	private function basic()
	{
		$all_settings = array(
            app::get('ectools')->_('支付提示信息设置')=>array(
                'site.paycenter.pay_succ',
                'site.paycenter.pay_failure',
            ),
        );
		
		// set service for extension settings.
        $obj_extension_services = kernel::servicelist('ectools_custom_extension_settings');
        if ($obj_extension_services)
        {
            foreach ($obj_extension_services as $obj_ext_service)
            {
                $obj_ext_service->settings($all_settings);
            }
        }

        $html= $this->_process($all_settings);
        echo $html;
	}
	
	private function _process($all_settings)
	{
        $setting = new base_setting($this->app);
        $setlib = $setting->source();
        $typemap = array(
            SET_T_STR=>'text',
            SET_T_INT=>'number',
            SET_T_ENUM=>'select',
            SET_T_BOOL=>'bool',
            SET_T_TXT=>'html',
            SET_T_FILE=>'file',
            SET_T_IMAGE=>'image',
            SET_T_DIGITS=>'number',
        );
        $tabs = array_keys($all_settings);
        $html = view::ui()->form_start(array('tabs'=>$tabs,'method'=>'POST'));
        $input_style = false;
        $arr_js = array();
        foreach($tabs as $tab=>$tab_name){
            foreach($all_settings[$tab_name] as $set){
                $current_set = $pre_set = $this->app->getConf($set);
                if($_POST['set'] && array_key_exists($set,$_POST['set'])){
                    if($current_set!==$_POST['set'][$set]){
                        $current_set = $_POST['set'][$set];
                        $this->app->setConf($set,$_POST['set'][$set]);
                    }
                }

                $input_type = $typemap[$setlib[$set]['type']];

                $form_input = array(
                    'title'=>$setlib[$set]['desc'],
                    'type'=>$input_type,
                    'name'=>"set[".$set."]",
                    'tab'=>$tab,
                    'value'=>$current_set,
                    'options'=>$setlib[$set]['options'],
                    'required' => ($input_type=='select'?true:false)
                );

                if (isset($setlib[$set]['extends_attr']) && $setlib[$set]['extends_attr'] && is_array($setlib[$set]['extends_attr']))
                {
                    foreach ($setlib[$set]['extends_attr'] as $_key=>$extends_attr)
                    {
                        $form_input[$_key] = $extends_attr;
                    }
                }

                $arr_js[] = $setlib[$set]['javascript'];

                $html.= view::ui()->form_input($form_input);
            }
        }

        if (!$_POST)
        {
            $pagedata['_PAGE_CONTENT'] = $html .= view::ui()->form_end();

            $str_js = '';
            if (is_array($arr_js) && $arr_js)
            {
                foreach ($arr_js as $str_javascript)
                {
                    $str_js .= $str_javascript;
                }
            }
			
			if ($str_js)
			{
				$pagedata['_PAGE_CONTENT'] .= '<script type="text/javascript">window.addEvent(\'domready\',function(){';
				$pagedata['_PAGE_CONTENT'] .= $str_js . '});</script>';
			}
            return $this->page(null, $pagedata);
        }
        else
        {
            $this->begin();
            $this->adminlog('修改支付提示信息配置', 1);
            $this->end(true, app::get('ectools')->_('当前配置修改成功！'));
        }
    }
}
