<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

/*
 * @package content
 * @subpackage article
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license 
 */

class sysinfo_ctl_admin_storesOffer extends desktop_controller
{
    var $workground = 'sysinfo.wrokground.theme';
    public function index(){
        return $this->finder('sysinfo_mdl_offer', array(
            'title'=>app::get('sysinfo')->_('商家报价数据'),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                    array(
                        'label'=>app::get('sysinfo')->_('添加报价'),
                        'href'=>'?app=sysinfo&ctl=admin_storesOffer&act=add','target'=>'dialog::{title:\''.app::get('sysinfo')->_('添加报价').'\',width:800,height:500}'
                    ),
                )
            ));
    }
    public function add(){
        return $this->page('sysinfo/admin/offer/editor.html',$pagedata);
    }
    public function save(){
        $this->begin("?app=sysinfo&ctl=admin_storesOffer&act=index");
        $article = app::get('sysinfo')->model('offer');
        $article->save($_POST);
        $this->end(true);
    }
}
