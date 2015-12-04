<?php  
class sysspfb_ctl_admin_supplys extends desktop_controller {
	 public $workground = 'sysspfb.workground.category';
	public function index()
	{
	     return $this->finder(	# code...  return $this->finder(
            'sysspfb_mdl_supplyInfo',
            array(
                'title'=>app::get('sysspfb')->_('供应信息列表'),
                'actions'=>array(
                 
                ),
                'use_view_tab' => true,
            )
        );
	}
	// public function requeir_index()
	// {
	// 	   'sysspfb_mdl_requireInfo',
 //            array(
 //                'title'=>app::get('sysspfb')->_('求购信息列表'),
 //                'actions'=>array(
 //                    array(
 //                        'label'=>app::get('sysspfb')->_('上架'),
 //                        'href'=>'?app=sysspfb&ctl=admin_props&act=addRequire',
 //                        'target'=>'dialog::{title:\''.app::get('sysspfb')->_('上架求购信息').'\',width:600,height:420}',
 //                    ),
 //                ),
 //                'use_view_tab' => true,
 //            )# code...
	// }
}
?>