<?php
class sysshoppubt_ctl_mffb extends desktop_controller{
	 var $workground = 'sysshoppubt.sprodrelease.manage';
 	public function index(){
 	$filter = input::get();
 	return $this->finder('sysshoppubt_mdl_mffb',array(
            		'title' => app::get('sysshoppubt')->_('免费发布助手'), )
 		 );
 	}

 	public function update()
 	{
 		# code...
 	}
 }