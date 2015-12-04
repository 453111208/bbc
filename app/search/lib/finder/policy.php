<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class search_finder_policy
{
    public $column_service_type = '服务项目';

    public $column_description = '描述';
    public $column_description_width = '300';

    public $column_status_width = '80';
    public $column_status = '状态';

    public $column_used = '使用';
    public $column_used_width = '80';

    public $column_reindex_width = '80';

    public function column_status(&$colList, $list)
    {
        if (!$list) return;
        foreach($list as $k=>$row)
        {
            $service = kernel::single($row['app_id']);
            $status = $service->status($msg);
            $colList[$k] = $msg;
        }
    }

    public function column_used(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            if( app::get('search')->getConf('search_server_policy') == $row['app_id'] )
            {
                $colList[$k] = '<a href="javascript:;" onClick="javascript:W.page(\'?app=search&ctl=policy&act=setDefault&method=shut&name='.$row["app_id"].'\')" >'.app::get('search')->_('停用').'</a>';
            }
            else
            {
                $colList[$k] = '<a href="javascript:;" onClick="javascript:W.page(\'?app=search&ctl=policy&act=setDefault&method=open&&name='.$row["app_id"].'\')" >'.app::get('search')->_('启用').'</a>';
            }
        }
    }//End Function

    public function column_service_type(&$colList, $list)
    {
        if (!$list) return;
        foreach($list as $k=>$row)
        {
            $service = kernel::single($row['app_id']);
            $des = $service->name;
            $colList[$k] = $des;
        }
    }//End Function

    public function column_description(&$colList, $list)
    {
        if (!$list) return;
        foreach($list as $k=>$row)
        {
            $service = kernel::single($row['app_id']);
            $des = $service->description;
            $colList[$k] = $des;
        }

    }//End Function

}//End Class
