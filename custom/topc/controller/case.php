<?php
class topc_ctl_case extends topc_controller {

	public function index()
	{
		$this->setlayoutFlag('articlesdtl');
		$essayInfo=app::get("syscase")->model("essay")->getList("*",array('towhere'=>1));
        $pagedata["essay"]=$essayInfo;
		

        $pageNow=1;
        $pageSize=10;
        if(!empty($_GET['pageNow'])){
            $pageNow=$_GET['pageNow'];
        }

        $essaycatid=$_GET["essaycatid"];
        if($essaycatid){
        	
        	$essayAllSql="SELECT sl.* FROM syscase_essay sl WHERE essaycat_id = ".$essaycatid;
        	$essaySql="SELECT sl.* FROM syscase_essay sl WHERE essaycat_id = ".$essaycatid." LIMIT ".($pageNow-1)*$pageSize.",".$pageSize."";
        }
        else{
        	$essayAllSql="SELECT sl.* FROM syscase_essay sl";
        	$essaySql="SELECT sl.* FROM syscase_essay sl LIMIT ".($pageNow-1)*$pageSize.",".$pageSize."";
        }
        $essayAllList = app::get("base")->database()->executeQuery($essayAllSql)->fetchAll();
        $essayList = app::get("base")->database()->executeQuery($essaySql)->fetchAll();

        $rowConut=  count($essayAllList);
        $pageCount=  ceil($rowConut/$pageSize);
        $pagedata['pageCount'] = $pageCount;
        $pagedata['pageNow'] = $pageNow;     
        $pagedata["essayAllList"]=$essayAllList;
        $pagedata["essayList"]=$essayList;

return $this->page('topc/case/index.html',$pagedata);
	}

	public function essay()
	{
		$this->setlayoutFlag('articlesdtl');

        $essayid=$_GET["essayid"];
		$essayInfo=app::get("syscase")->model("essay")->getRow("*",array("essay_id"=>$essayid));

        if(!$essayInfo){
            $pagedata["isfb"]="0";//不存在文章
            return $this->page('topc/case/essay.html',$pagedata);
        }
        else{
            if($essayInfo["towhere"]=="0"){
                $pagedata["isfb"]="1";//文章下架
                return $this->page('topc/case/essay.html',$pagedata);
            }
            else{
                 $pagedata["isfb"]="2";//正常显示
            }
        }
        
		//点击文章点击量加1
		$essayInfo["click_count"]=$essayInfo["click_count"]+1;
		app::get("syscase")->model("essay")->save($essayInfo);
		$pagedata["essayRow"]=$essayInfo;

		//SQL取按文章点击量递减的文章
		/*$sql="select * from syscase_essay where towhere =1 order by click_count desc LIMIT 6 ";
        $essayInfo = app::get("base")->database()->executeQuery($sql)->fetchAll();
        $pagedata["hotessayLit"]=$essayInfo;*/

		return $this->page('topc/case/essay.html',$pagedata);
	}


}
    
?>