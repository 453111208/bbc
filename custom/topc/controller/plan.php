<?php
class topc_ctl_plan extends topc_controller {

	public function index()
	{
		$this->setlayoutFlag('articlesdtl');
		$literaryInfo=app::get("sysplan")->model("literary")->getList("*",array("towhere"=>1));
        $pagedata["literary"]=$literaryInfo;
		

        $pageNow=1;
        $pageSize=10;
        if(!empty($_GET['pageNow'])){
            $pageNow=$_GET['pageNow'];
        }


        $literarycatid=$_GET["literarycatid"];
        if($literarycatid){
        	
        	$literaryAllSql="SELECT sl.* FROM sysplan_literary sl WHERE literarycat_id = ".$literarycatid;
        	$literarySql="SELECT sl.* FROM sysplan_literary sl WHERE literarycat_id = ".$literarycatid." LIMIT ".($pageNow-1)*$pageSize.",".$pageSize."";
        }
        else{
        	$literaryAllSql="SELECT sl.* FROM sysplan_literary sl";
        	$literarySql="SELECT sl.* FROM sysplan_literary sl LIMIT ".($pageNow-1)*$pageSize.",".$pageSize."";
        }
        $literaryAllList = app::get("base")->database()->executeQuery($literaryAllSql)->fetchAll();
        $literaryList = app::get("base")->database()->executeQuery($literarySql)->fetchAll();

        $rowConut=  count($literaryAllList);
        $pageCount=  ceil($rowConut/$pageSize);
        $pagedata['pageCount'] = $pageCount;
        $pagedata['pageNow'] = $pageNow;     
        $pagedata["literaryAllList"]=$literaryAllList;
        $pagedata["literaryList"]=$literaryList;

return $this->page('topc/plan/index.html',$pagedata);
	}

	public function literary()
	{
		$this->setlayoutFlag('articlesdtl');

        $literaryid=$_GET["literaryid"];
		$literaryInfo=app::get("sysplan")->model("literary")->getRow("*",array("literary_id"=>$literaryid));

        if(!$literaryInfo){
            $pagedata["isfb"]="0";//不存在文章
            return $this->page('topc/plan/literaryDetile.html',$pagedata);
        }
        else{
            if($literaryInfo["towhere"]=="0"){
                $pagedata["isfb"]="1";//文章下架
                return $this->page('topc/plan/literaryDetile.html',$pagedata);
            }
            else{
                 $pagedata["isfb"]="2";//正常显示
            }
        }
        
		//点击文章点击量加1
		$literaryInfo["click_count"]=$literaryInfo["click_count"]+1;
		app::get("sysplan")->model("literary")->save($literaryInfo);
		$pagedata["literaryRow"]=$literaryInfo;

        $literarycatInfo=app::get("sysplan")->model("literarycat")->getList("*");
        $pagedata["literarycatList"]=$literarycatInfo;
        $literaryclassInfo=app::get("sysplan")->model("literaryclass")->getList("*");
        $pagedata["literaryclassList"]=$literaryclassInfo;
        $literarytargetInfo=app::get("sysplan")->model("literarytarget")->getList("*");
        $pagedata["literarytargetList"]=$literarytargetInfo;
        // var_dump($pagedata["literarycatList"]);

		//SQL取按文章点击量递减的文章
		/*$sql="select * from sysplan_literary where towhere =1 order by click_count desc LIMIT 6 ";
        $literaryInfo = app::get("base")->database()->executeQuery($sql)->fetchAll();
        $pagedata["hotliteraryLit"]=$literaryInfo;*/

		return $this->page('topc/plan/literaryDetile.html',$pagedata);
	}


}
    
?>