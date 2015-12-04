<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topshop_ctl_export extends topshop_controller{

    public function view()
    {
        //导出方式 直接导出还是通过队列导出
        $pagedata['check_policy'] = 'download';

        $filetype = array(
            'csv'=>'.csv',
            'xls'=>'.xls',
        );

        $pagedata['model'] = input::get('model');
        $pagedata['app'] = input::get('app');

        $supportType = input::get('supportType');
        //支持导出类型
        if( $supportType && $filetype[$supportType] )
        {
            $pagedata['export_type'] = array($supportType=>$filetype[$supportType]);
        }
        else
        {
            $pagedata['export_type'] = $filetype;
        }
        return view::make('topshop/export/export.html', $pagedata);
    }

    public function export()
    {
        //导出
        if( input::get('filter') )
        {
            $filter = json_decode(input::get('filter'),true);
        }

        $permission = [
            'systrade' =>['trade','order'],
            'sysclearing' =>['settlement','settlement_detail'],
        ];

        $app = input::get('app',false);
        $model = input::get('model',false);

        if( input::get('name') && $app && $model && $permission[$app] && in_array($model,$permission[$app]) )
        {
            $model = $app.'_mdl_'.$model;
            $filter['shop_id'] = shopAuth::getShopId();
            kernel::single('importexport_export')->fileDownload(input::get('filetype'), $model, input::get('name'), $filter);
        }
        else
        {
            echo '导出参数错误';
        }
    }
}

