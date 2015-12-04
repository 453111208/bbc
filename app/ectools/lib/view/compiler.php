<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class ectools_view_compiler{

    public function compile_modifier_cur($attrs,&$compile)
    {
        //todo 需要将货币汇率也缓存
        if(!strpos($attrs,',') || false!==strpos($attrs,',')){
            $arr_attributes = explode(',', $attrs);
            if (count($arr_attributes) <= 2)
            {
                if (count($arr_attributes) < 2)
                {
                    $attrs .= ',$_COOKIE["S"]["CUR"],app::get(\'ectools\')->getConf(\'system.money.decimals\'),app::get(\'ectools\')->getConf(\'system.money.operation.carryset\')';
                }
                else
                {
                    $attrs .= ',app::get(\'ectools\')->getConf(\'system.money.decimals\'),app::get(\'ectools\')->getConf(\'system.money.operation.carryset\')';
                }
            }
            elseif (count($arr_attributes) < 4)
            {
                $attrs .= ',app::get(\'ectools\')->getConf(\'system.money.decimals\'),app::get(\'ectools\')->getConf(\'system.money.operation.carryset\')';
            }
            else
            {
                $attrs .= ',app::get(\'ectools\')->getConf(\'system.money.decimals\'),app::get(\'ectools\')->getConf(\'system.money.operation.carryset\')';
            }

            $attrs = 'kernel::single(\'ectools_data_currency\')->showMoney('.$attrs.')';
            return $attrs ;
        }
    }

    public function compile_modifier_cur_name($attrs) {
        //todo 得到货币的cur_name
        if(!strpos($attrs,',') || false!==strpos($attrs,',')){
            return $attrs = 'kernel::single(\'ectools_data_currency\')->getCurrency(\'\','.$attrs.')';
        }
    }

    public function compile_modifier_payname($attrs) {
        //todo 需要将货币汇率也缓存
        if(!strpos($attrs,',') || false!==strpos($attrs,',')){
            return $attrs = 'app::get(\'ectools\')->model(\'payment_cfgs\')->get_app_display_name('.$attrs.')';
        }
    }

    public function compile_modifier_operactor_name($attrs) {
        if (!strpos($attrs,',') || false!==strpos($attrs,',')){
            return $attrs = 'app::get(\'pam\')->model(\'account\')->get_operactor_name('.$attrs.')';
        }
    }
}
