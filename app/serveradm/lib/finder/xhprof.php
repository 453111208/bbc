<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class serveradm_finder_xhprof{
    var $detail_basic = '基本信息';
    var $column_control = '操作';
    
    function column_control(&$colList, $list){
        foreach($list as $k=>$row)
        {
            $colList[$k] = '<a href="?app=serveradm&ctl=admin_xhprof&act=show&p[0]='.$row['run_id'].'"  target="blank">'.__("分析报告").'</a>';
        }
        
    }
    
    function detail_basic($id){
        $oXHProf = app::get('serveradm')->model('xhprof');
        $aData=$oXHProf->dump($id);
        $pagedata['data'] = $aData;
        return view::make('serveradm/admin/xhprof_detail.html', $pagedata)->render();
    }
}
