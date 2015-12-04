<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

class site_finder_explorers
{
    public $column_tools='操作';
    public $column_tools_width='80';
    public function column_tools(&$colList, $list){
        foreach($list as $k=>$row)
        {
            $colList[$k] = '<a href="?app=site&ctl=admin_explorer_app&act=directory&app_id='.$row['app'].'&content_path='.str_replace('/', '-', $row['path']).'">'.app::get('site')->_('进入目录').'</a>';            
        }

    }
}//End Class
