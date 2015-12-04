<?php
class topc_ctl_trading extends topc_controller{
//交易中心

	public function index()
	{
	 $this->setLayoutFlag('trading');
	 return $this->page();
	}
	
	
}
