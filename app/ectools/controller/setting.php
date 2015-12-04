<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class ectools_ctl_setting extends desktop_controller{

    var $require_super_op = true;

    function __construct($app){
        parent::__construct($app);
        $this->app = $app;
		header("cache-control: no-store, no-cache, must-revalidate");
    }

    function index(){
        $this->basic();
    }

    public function basic(){
        $all_settings = array(

            app::get('ectools')->_('价格计算精度设置')=>array(
                'site.decimal_digit.count',
                'site.decimal_type.count',
            ),
            app::get('ectools')->_('价格显示精度设置')=>array(
                'system.money.decimals',
                'system.money.operation.carryset'
            ),
            app::get('ectools')->_('货币设置')=>array(
                'system.currency.default',
            ),
        );
        //echo '<h5 class="head-title">系统设置</h5>';
        $pagedata['_PAGE_CONTENT'] = $this->_process($all_settings);
        return $this->page(null, $pagedata);
    }

    function _process($all_settings){
        $setting = new base_setting($this->app);
        $setlib = $setting->source();
        $typemap = array(
            SET_T_STR=>'text',
            SET_T_INT=>'number',
            SET_T_ENUM=>'select',
            SET_T_BOOL=>'bool',
            SET_T_TXT=>'text',
            SET_T_FILE=>'file',
            SET_T_IMAGE=>'image',
            SET_T_DIGITS=>'number',
        );

        $tabs = array_keys($all_settings);
        $html = view::ui()->form_start(array('tabs'=>$tabs, 'method'=>'POST'));
        foreach($tabs as $tab=>$tab_name){
            foreach($all_settings[$tab_name] as $set){
                $current_set = $pre_set = $this->app->getConf($set);
                if($_POST['set'] && array_key_exists($set,$_POST['set'])){
                    if($current_set!=$_POST['set'][$set]){
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
                );
                if ($input_type=='select')
                    $form_input['required'] = true;

                if($input_type=='image'){

                   $form_input = array_merge($form_input,array(

                      'width'=>$setlib[$set]['width'],
                      'height'=>$setlib[$set]['height']

                   ));

                }

                $html.= view::ui()->form_input($form_input);
            }
        }
        return $html .= view::ui()->form_end(1, app::get('ectools')->_('保存设置'));
    }

    function licence(){
        $this->sidePanel();
        echo '<iframe width="100%" height="100%" src="'.constant('URL_VIEW_LICENCE').'" ></iframe>';
    }
}

