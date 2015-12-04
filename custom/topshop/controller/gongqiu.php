<?php 
class topshop_ctl_gongqiu extends topshop_controller {
	//添加供应
	public function addGongying()
	{
		$pagedata['shopId'] = $this->shopId;
		$this->contentHeaderTitle = app::get('topshop')->_('添加供应信息');
        return $this->page('topshop/gongqiu/addGongying.html', $pagedata);	# code...
	}
	//供应列表
	public function gongyingList()
	{
		$gongyingList=app::get("sysgongqiu")->model("gongying")->getList("*",array("shop_id"=>$this->shopId));
		$pagedata["gongyingList"]=$gongyingList;
		$this->contentHeaderTitle = app::get('topshop')->_('供应列表');
        return $this->page('topshop/gongqiu/gongyingList.html', $pagedata);	# code...
	}
	//保存供应
	public function saveGongying()
	{
		$postdata = $_POST["gongying"];
		try {
			$postdata["create_time"]=time();
			app::get("sysgongqiu")->model("gongying")->save($postdata);
			$url=url::action('topshop_ctl_gongqiu@gongyingList');
			return $this->splash('success',$url,"添加成功",true);
		} catch (Exception $e) {
			return $this->splash('error', '', $e->getMessage(), true);
		}
		
		
	}

	//添加求购
	public function addQiugou()
	{
		$pagedata['shopId'] = $this->shopId;
		$this->contentHeaderTitle = app::get('topshop')->_('添加求购信息');
        return $this->page('topshop/gongqiu/addQiugou.html', $pagedata);	# code...
	}
	//求购列表
	public function qiugouList($value='')
	{
		$qiugouList=app::get("sysgongqiu")->model("qiugou")->getList("*",array("shop_id"=>$this->shopId));
		$pagedata["qiugouList"]=$qiugouList;
		$this->contentHeaderTitle = app::get('topshop')->_('求购列表');
        return $this->page('topshop/gongqiu/qiugouList.html', $pagedata);	# code...
	}
	//保存求购
	public function saveqiugou()
	{
			$postdata = $_POST["qiugou"];
		try {
			$postdata["create_time"]=time();
			app::get("sysgongqiu")->model("qiugou")->save($postdata);
			$url=url::action('topshop_ctl_gongqiu@qiugouList');
			return $this->splash('success',$url,"添加成功",true);
		} catch (Exception $e) {
			return $this->splash('error', '', $e->getMessage(), true);
		}
		
		
	}
}
 ?>