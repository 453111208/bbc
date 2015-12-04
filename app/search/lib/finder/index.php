<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class search_finder_index
{

    public $column_edit = '配置';
    public $column_edit_order = '10';
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $colList[$k] = '<a href="?app=search&ctl=index&act=setting&_finder[finder_id]=' . $_GET['_finder']['finder_id'] . '&p[0]=' . $row['index_name'] . '" target="dialog::{title:\'' . app::get('search')->_('配置索引搜索参数') . '\', width:680, height:250}">' . app::get('search')->_('配置') . '</a>';
        }
    }


    public $detail_capability = '基本信息';
    public function detail_capability($row)
    {
        $policy = app::get('search')->getConf('search_server_policy');
        $obj = kernel::single($policy);

        $status = $obj->status($msg);
        if($status){
            //sphinx 配置到sphinx todo
            //$tablesInfo = $obj->get_describe($row);
            $tablesInfo = $obj->query('DESCRIBE '.$row);
            $column[$row] = $tablesInfo;
        }
        $pagedata['column'] = $column;
        return view::make('search/capability/default.html', $pagedata)->render();
    }

}//End Class
