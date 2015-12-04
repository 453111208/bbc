<?php

/**
 * @brief 商品列表
 */
class sysshoppubt_ctl_biddingsitems extends desktop_controller{
	public $workground = 'sysshoppubt.sprodrelease.manage';
	public function index()
	{
	return $this->finder('sysshoppubt_mdl_biddingsitems', array(
                'title'=>app::get('sysshoppubt')->_('出价信息列表'),
                'actions'=>array(
                
                ),
                'use_view_tab' => true,
            ));# code...
	}
	}