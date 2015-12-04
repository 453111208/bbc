<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class ectools_mdl_payment_cfgs extends dbeav_model
{

    function __construct(&$app){
        $this->app = $app;
        $this->columns = array(
            'app_display_name'=>array('label'=>app::get('ectools')->_('支付方式'),'width'=>200,'is_title'=>true),
            'app_staus'=>array('label'=>app::get('ectools')->_('状态'),'width'=>120),
            'app_version'=>array('label'=>app::get('ectools')->_('应用程序版本'),'width'=>120),
            'app_order_by'=>array('label'=>app::get('ectools')->_('排序'),'width'=>120),
            'is_frontend'=>array('type' => 'bool','label'=>app::get('ectools')->_('是否应用与后台'),'width'=>120),
            'platform'=>array(
                'type' =>array (
                    'ispc'     => app::get('ectools')->_('标准版'),
                    'iswap' => app::get('ectools')->_('触屏版'),
                    'iscommon' => app::get('ectools')->_('通用版'),
                ),
                'default' => 'ispc',
                'label'=>app::get('ectools')->_('支持平台'),
                'width'=>120,
            ),
        );

        $this->schema = array(
            'default_in_list'=>array_keys($this->columns),
            'in_list'=>array_keys($this->columns),
            'idColumn'=>'app_id',
            'textColumn'=>'app_display_name',
            'columns'=>$this->columns
        );
    }

    /**
     * suffix of model
     * @params null
     * @return string table name
     */
    public function table_name()
    {
        return 'payment_cfgs';
    }

    function get_schema(){
        return $this->schema;
    }

    function count($filter=''){
        $arrServicelist = kernel::servicelist('ectools_payment.ectools_mdl_payment_cfgs');
        foreach($arrServicelist as $class_name => $object){
            $i++;
        }
        return $i;
    }

    /**
     * 取到服务列表 - 1条或者多条
     * @params string - 特殊的列名
     * @params array - 限制条件
     * @params 偏移量起始值
     * @params 偏移位移值
     * @params 排序条件
     */
    public function getList($cols='*', $filter=array('status' => 'false'), $offset=0, $limit=-1, $orderby=null){
        //todo fitler;
        $arrServicelist = kernel::servicelist('ectools_payment.ectools_mdl_payment_cfgs');
        $start_index = 0;
        foreach($arrServicelist as $class_name => $object){
            if ($offset >= 0 && $limit > 0)
            {
                if ($start_index >= ($offset+$limit) || $start_index < $offset)
                {
                    $start_index++;
                    continue;
                }
            }
            $strPaymnet = $this->app->getConf($class_name);
            $arrPaymnet = unserialize($strPaymnet);

            $payName = $arrPaymnet['setting']['pay_name'] ? $arrPaymnet['setting']['pay_name'] : $object->name;
            $row['app_name'] = $object->name;
            $row['app_staus'] = (($arrPaymnet['status']===true||$arrPaymnet['status']==='true') ? app::get('ectools')->_('开启') : app::get('ectools')->_('关闭'));
            $row['app_version'] = $object->ver;
            $row['app_id'] = $object->app_key;
            $row['app_rpc_id'] = (isset($object->app_rpc_key) && $object->app_rpc_key) ? $object->app_rpc_key : $object->app_key;
            $row['app_class'] = $class_name;
            $row['app_des'] = isset($arrPaymnet['setting']['pay_desc']) ? $arrPaymnet['setting']['pay_desc'] : "";
            $row['app_pay_type'] = $arrPaymnet['pay_type'];
            $row['app_display_name'] = $payName;
            $row['app_pay_brief'] = $arrPaymnet['setting']['pay_brief'];
            $row['app_order_by'] = $arrPaymnet['setting']['order_by'] ? $arrPaymnet['setting']['order_by'] : 1;
            $row['app_info'] = $object->intro();
            $row['support_cur'] = $arrPaymnet['setting']['support_cur'];
            $row['pay_fee'] = $arrPaymnet['setting']['pay_fee'];
            $row['supportCurrency'] = isset($object->supportCurrency) ? $object->supportCurrency : array();
            if(isset($arrPaymnet['setting']['real_method'])){
                $row['real_method']= $arrPaymnet['setting']['real_method'];
            }

            $row['platform'] = $object->platform;


            if($filter['app_id']){
                $app_id = is_array($filter['app_id'])?$filter['app_id'][0]:$filter['app_id'];
                return array($this->getPaymentInfo($app_id));
            }

            if (isset($filter) && $filter)
            {
                if (isset($filter['is_frontend']) && !$filter['is_frontend'])
                {
                    if(is_array($filter['platform']))
                    {
                        foreach ($filter['platform'] as $key => $value)
                        {
                            if($object->platform == $value)
                            {
                                $data[] = $row;
                            }
                        }
                    }
                    else
                    {
                        if($filter['platform'])
                        {
                            if($filter['platform'] == $object->platform)
                            {
                                $data[] = $row;
                            }
                        }
                        else
                        {
                            $data[] = $row;
                        }
                    }
                }
                else
                {
                    if (isset($arrPaymnet['status']) && $arrPaymnet['status'] === 'true')
                    {
                        if(is_array($filter['platform']))
                        {
                            foreach ($filter['platform'] as $key => $value)
                            {
                                if($object->platform == $value)
                                {
                                    $data[] = $row;
                                }
                            }
                        }
                        else
                        {
                            if($filter['platform'])
                            {
                                if($filter['platform'] == $object->platform)
                                {
                                    $data[] = $row;
                                }
                            }
                            else
                            {
                                $data[] = $row;
                            }
                        }
                    }
                }
            }
            else
            {
                $data[] = $row;
            }

            $start_index++;
        }

        return $data;
    }


    /**
     * 取到特定的支付方式
     * @params string - 字符方式的名称
     * @return array - 支付方式的结果数组
     */
    public function getPaymentInfo($pay_app_id='alipay', $app='ectools')
    {
        if (!$pay_app_id)
            return array(
                'app_name'=>app::get('ectools')->_('无支付方式'),
                'app_staus' => app::get('ectools')->_('关闭'),
                'app_version' => '1.0',
                'app_id' => app::get('ectools')->_('无支付方式'),
                'app_class' => 'No Class',
                'app_des' => '',
                'app_pay_type' => 'online',
                'app_display_name' => app::get('ectools')->_('无支付方式'),
                'app_info' => '',
                'support_cur' => '',
                'pay_fee' => '',
            );

        if ($pay_app_id != app::get('ectools')->_('货到付款') && $pay_app_id != '-1')
        {
            //$class_name = "ectools_payment_plugin_" . $pay_app_id;
            $class_name = "";
            $obj_app_plugins = kernel::servicelist("ectools_payment.ectools_mdl_payment_cfgs");
            foreach ($obj_app_plugins as $obj_app)
            {
                $app_class_name = get_class($obj_app);
                $arr_class_name = explode('_', $app_class_name);
                if (isset($arr_class_name[count($arr_class_name)-1]) && $arr_class_name[count($arr_class_name)-1])
                {
                    if ($arr_class_name[count($arr_class_name)-1] == $pay_app_id)
                    {
                        $pay_app_ins = $obj_app;
                        $class_name = $app_class_name;
                    }
                }
                else
                {
                    if ($app_class_name == $sdf['pay_app_id'])
                    {
                        $pay_app_ins = $obj_app;
                        $class_name = $app_class_name;
                    }
                }

                if ($class_name && !class_exists($class_name))
                    return array(
                        'app_name'=>$pay_app_id,
                        'app_staus' => app::get('ectools')->_('开启'),
                        'app_version' => '1.0',
                        'app_id' => $pay_app_id,
                        'app_rpc_id' => $pay_app_id,
                        'app_class' => $class_name,
                        'app_des' => '',
                        'app_pay_type' => 'online',
                        'app_display_name' => $pay_app_id,
                        'app_info' => '',
                        'support_cur' => '',
                        'pay_fee' => '',
                    );
            }
            $strPayment = $this->app->getConf($class_name);
            $arrPaymnet = unserialize($strPayment);
            if (class_exists($class_name))
                $objPayment = kernel::single($class_name);
            else
            {
                return array(
                        'app_name'=>$pay_app_id,
                        'app_staus' => app::get('ectools')->_('开启'),
                        'app_version' => '1.0',
                        'app_id' => $pay_app_id,
                        'app_rpc_id' => $pay_app_id,
                        'app_class' => '',
                        'app_des' => '',
                        'app_pay_type' => 'online',
                        'app_display_name' => $pay_app_id,
                        'app_info' => '',
                        'support_cur' => '',
                        'pay_fee' => '',
                    );
            }

            $row = array(
                'app_name' => $objPayment->name,
                'app_staus' => (($arrPaymnet['status']===true||$arrPaymnet['status']==='true') ? app::get('ectools')->_('开启') : app::get('ectools')->_('关闭')),
                'app_version' => $objPayment->ver,
                'app_id' => $objPayment->app_key,
                'app_rpc_id' => (isset($objPayment->app_rpc_key) && $objPayment->app_rpc_key) ? $objPayment->app_rpc_key : $objPayment->app_key,
                'app_class' => $class_name,
                'app_des' => $arrPaymnet['setting']['pay_desc'],
                'app_pay_type' => $arrPaymnet['pay_type'],
                'app_display_name' => $arrPaymnet['setting']['pay_name'],
                'app_info' => $objPayment->intro(),
                'support_cur' => $arrPaymnet['setting']['support_cur'],
                'pay_fee' => $arrPaymnet['setting']['pay_fee'],
                'platform' => $objPayment->platform,
            );
        }
        else
        {
            $row = array(
                'app_name' => 'COD',
                'app_staus' => app::get('ectools')->_('开启'),
                'app_version' => '1.0',
                'app_id' => 'COD',
                'app_rpc_id' => '-1',
                'app_class' => 'COD',
                'app_des' => app::get('ectools')->_('货到付款'),
                'app_pay_type' => 'offline',
                'app_display_name' => app::get('ectools')->_('货到付款'),
                'app_info' => app::get('ectools')->_('货到付款'),
                'support_cur' => '1',
            );
        }

        return $row;
    }

    /**
     * 得到app_display_name
     * @param string pay_app_id
     * @return string
     */
    public function get_app_display_name($pay_app_id='alipay')
    {
        $arr_payment_cfgs = $this->getPaymentInfo($pay_app_id);

        return $arr_payment_cfgs['app_display_name'];
    }

    /**
     * 得到指定货币对应的所有支付方式,获取pc端的支付方式
     * @param string 结算货币对应的cur_code
     * @return array 所有的支付方式
     */
    public function getListByCode($cur_code='CNY',$platform=array('ispc','iscommon'))
    {
        $cur_code = $cur_code ? $cur_code : "CNY";
        $arr_payments = $this->getList('*', array('status' => 'true','platform'=>$platform, 'is_frontend' => true));
        $arrDefCurrency = "";

        if(!$arr_payments) return "";
        foreach ($arr_payments as $key=>$paymentinfo)
        {
            switch ($paymentinfo['support_cur'])
            {
                case '1':
                    if ($cur_code != 'CNY')
                    {
                        unset($arr_payments[$key]);
                    }
                    break;
                case '2':
                    if ($cur_code == 'CNY')
                    {
                        unset($arr_payments[$key]);
                    }
                    break;
                case '3':
                    if ($arrDefCurrency['cur_code'] != $cur_code)
                    {
                        unset($arr_payments[$key]);
                    }
                    break;
                case '4':
                    break;
                default:
                    break;
            }
        }

        return $arr_payments;
    }

    /**
     * 判断指定支付方式是否开启
     *
     */

    function check_payment_open($pay_app_id){
        $arr_payments = $this->getList('*', array('app_id'=>$pay_app_id));
        if($arr_payments[0]['app_staus'] == '关闭'){
            return false;
        }
        return true;
    }
}
