<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_ctl_data extends desktop_controller{

    function index(){
        ini_set("max_exection_time", 0);
        
        $oDSB = kernel::single("desktop_system_backup");
        $arr = $oDSB->getList();
        if( $arr ) {
            foreach( (array)$arr as $row ) {   //去除重复数据 
                $key = md5($row['name']);
                if( !$arr_list[$key] )
                    $arr_list[$key] = $row;
                
                $last_backup_time = ($last_backup_time?time():$row['time']);
                $last_backup_time = $last_backup_time>$row['time'] ? $row['time'] : $last_backup_time;
            }
            $pagedata['last_backup_time'] = date('Y-m-d H:i:s',$last_backup_time);
        }
        if( !$arr_list ) $pagedata['last_backup_time'] = null;
        $pagedata['archive'] = $arr_list;

        return $this->page('desktop/system/data_run.html', $pagedata);
    }


}
