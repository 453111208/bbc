
<?php
class topc_ctl_marketList extends topc_controller{
//行情列表

	public function index()
	{
	 $this->setLayoutFlag('marketList');
	 return $this->page();
	}
	

	
}
