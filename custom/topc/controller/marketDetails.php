
<?php
class topc_ctl_marketDetails extends topc_controller{
//行情中心-详情

	public function index()
	{
	 $this->setLayoutFlag('marketDetails');
	 return $this->page();
	}

	
}
