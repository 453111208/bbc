<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_finder_tag{
    var $column_control = '编辑';
    function column_control(&$colList, $list)
    {
        foreach($list as $k => $row)
        {
            $colList[$k] = '<a target="dialog::{title:\''.app::get('desktop')->_('链接编辑').'\', width:400, height:400}" href="?app='.$_GET['app'].'&ctl='.$_GET['ctl'].'&act=tag_edit&type='.$_GET['type'].'&finder_id='.$_GET['_finder']['finder_id'].'&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&_finder_name='.$_GET['_finder']['finder_id'].'&p[0]='.$row['tag_id'].'">'.app::get('desktop')->_('编辑').'</a>';            
        }
        
       
    }

}
