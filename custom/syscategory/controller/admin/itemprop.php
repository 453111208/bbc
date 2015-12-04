<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class syscategory_ctl_admin_itemprop extends desktop_controller {

    public $workground = 'syscategory.workground.category';

    public function index()
    {
   return $this->finder(
            'syscategory_mdl_item_prop',
            array(
                'title'=>app::get('syscategory')->_('商品属性列表'),
                'actions'=>array(
                    array(
                        'label'=>app::get('syscategory')->_('添加商品属性'),
                        'href'=>'?app=syscategory&ctl=admin_itemprop&act=create','target'=>'dialog::{title:\''.app::get('syscategory')->_('添加商品属性').'\',width:600,height:420}'
                    ),
                 
                ),
                'use_view_tab' => true,
            )
        );
    }

   public function create($itempropId)
    {
        if( $itempropId )
        {
            $propInfo = app::get('syscategory')->model('item_prop')->getRow("*",array("item_prop_id"=>$itempropId));
            $pagedata['propInfo'] = $propInfo;
        }

        return $this->page('syscategory/admin/itemprop/itemProp.html', $pagedata);
    }

    public function save()
    {
        # code...
        $this->begin();
        $data = $_POST;
        $data["modified_time"] = time();
        $itempropMdl = app::get('syscategory')->model('item_prop');
        $itempropMdl->save($data);
        $this->end(true, app::get('syscategory')->_('保存成功'));

    }

}

