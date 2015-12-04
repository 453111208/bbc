<?php

class site_finder_seo
{
    var $detail_basic = '查看';
    function column_control($row){
        $colList[$k] = '<a href="index.php?app=site&ctl=admin_seo&act=seoset&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['id'].'"  target="dialog::{frameable:true, title:\''.app::get('site')->_('添加菜单').'\', width:400, height:280}">'.app::get('site')->_('编辑').'</a>';
    }

    function detail_basic($id){
        $seo = app::get('site')->model('seo')->getRow('*',array('id'=>$id));
        if(is_string($seo['param'])){
            $seo['param'] = unserialize($seo['param']);
        }
        if(is_string($seo['config'])){
            $seo['config'] = unserialize($seo['config']);
        }
        if(!isset($seo['param']['seo_nofollow']) || !$seo['param']['seo_nofollow'] ||$seo['param']['seo_nofollow'] == "false" || $seo['param']['seo_nofollow'] == "否")
        {
            $seo['param']['seo_nofollow'] = 0;
        }
        if(!isset($seo['param']['seo_noindex']) || !$seo['param']['seo_noindex'] ||$seo['param']['seo_noindex'] == "false" || $seo['param']['seo_noindex'] == "否")
        {
            $seo['param']['seo_noindex'] = 0;
        }

        $pagedata['id'] = $id;
        $pagedata['param'] = $seo['param'];
        $pagedata['config'] = $seo['config'];
        return view::make('site/admin/seo/base.html', $pagedata)->render();
    }
}//End Class
