
<?php
class topc_ctl_mffb extends topc_controller{
	public function save()
	{
		$a=$_POST;
		$a["create_time"]=time();
		app::get("sysshoppubt")->model("mffb")->save($a);
		 return $this->splash('success',"/index.php/trading", app::get('topc')->_('提交成功'));
		
		$b=1;# code...
	}
}