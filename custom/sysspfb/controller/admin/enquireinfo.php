<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysspfb_ctl_admin_enquireinfo extends desktop_controller {
	public $workground = 'sysspfb.workground.category';

    /**
     * 询价信息列表
     */
    public function index()
    {
        return $this->finder(	# code...  return $this->finder(
            'sysspfb_mdl_enquireinfo',
            array(
                'title'=>app::get('sysspfb')->_('询价信息列表'),
                'actions'=>array(
                
                ),
                'use_view_tab' => true,
            )
        );
    }

    public function _views()
    {
        $mdl_all = app::get('sysspfb')->model('enquireinfo');
        $require = array('ifrequire' => 1);
        $supply = array('ifrequire' => 2);
        $requirecou =  $mdl_all->count($require);
        $supplycou = $mdl_all->count($supply);
        $subMenu = array(
            0=>array(
                'label'=>app::get('sysshoppubt')->_("供应 ( $requirecou )"),
                'optional'=>false,
                'filter'=>array(
                    'ifrequire'=>1,
                ),
            ),
            1=>array(
                'label'=>app::get('sysshoppubt')->_("求购 ( $supplycou )"),
                'optional'=>false,
                'filter'=>array(
                    'ifrequire'=>2,
                ),
            ),

        );
        return $subMenu;
    }
}