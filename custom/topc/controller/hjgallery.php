
<?php
class topc_ctl_hjgallery extends topc_controller{
//幻境列表页

	public function index()
	{
	 $this->setLayoutFlag('hjgallery');
	 return $this->page();
	}


	
}
