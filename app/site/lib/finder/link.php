<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class site_finder_link
{
    
    public $column_tools='操作';
    public $column_tools_width='80';
    public function column_tools(&$colList, $list){
        foreach($list as $k=>$row)
        {
            $colList[$k] = '<a target="dialog::{title:\''.app::get('site')->_('链接编辑').'\', width:600, height:400}" href="?app=site&ctl=admin_link&act=edit&link_id='.$row['link_id'].'">'.app::get('site')->_('编辑').'</a>';
        }
    }
}//End Class

