<?php
class topc_ctl_trading extends topc_controller{
//��������

	public function index()
	{
	 $this->setLayoutFlag('trading');
	 return $this->page();
	}
	
	
}
