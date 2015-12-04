<?php
class topc_ctl_expert extends topc_controller {
    public function index()
	{
		$this->setlayoutFlag('expert');
		//对$pagedata赋值expert表
	    $expertInfo=app::get("sysexpert")->model("expert")->getList("*");
        $pagedata["expertList"]=$expertInfo;
        //对$pagedata赋值literarycat表
	    $literarycatInfo=app::get("sysexpert")->model("literarycat")->getList("*");
        $pagedata["literarycatList"]=$literarycatInfo;
        $literaryInfo=app::get("sysexpert")->model("literary")->getList("*",array("towhere"=>1));
        $pagedata["literary"]=$literaryInfo;
        //对$pagedata赋值literary表
        
       

		/*$literarycatid=$_GET["literarycatid"];
		$literarycatInfo=app::get("sysexpert")->model("literary")->getList("*",array("literarycat_id_id"=>$literarycatid));
		$pagedata["literaryList"]=$literarycatInfo;*/

		//SQL取按文章点击量递减的文章
        $sql="select * from sysexpert_literary where towhere =1 and ishot =1 order by click_count desc LIMIT 8 ";
        $literaryInfo = app::get("base")->database()->executeQuery($sql)->fetchAll();
        $pagedata["hotliteraryLit"]=$literaryInfo;

        $pageNow=1;
        $pageSize=12;
        if(!empty($_GET['pageNow'])){
            $pageNow=$_GET['pageNow'];
        }


        $literarycatid=$_GET["literarycatid"];
        if($literarycatid>0){
        	//$literaryInfo=app::get("sysexpert")->model("literary")->getList("*",array("literarycat_id"=>$literarycatid,"towhere"=>1));
        	$literaryAllSql="SELECT sl.* FROM sysexpert_literary sl WHERE literarycat_id = ".$literarycatid;
        	$literarySql="SELECT sl.* FROM sysexpert_literary sl WHERE literarycat_id = ".$literarycatid." LIMIT ".($pageNow-1)*$pageSize.",".$pageSize."";
        }
        else{
        	//$literaryInfo=app::get("sysexpert")->model("literary")->getList("*",array("towhere"=>1));
        	$literaryAllSql="SELECT sl.* FROM sysexpert_literary sl";
        	$literarySql="SELECT sl.* FROM sysexpert_literary sl LIMIT ".($pageNow-1)*$pageSize.",".$pageSize."";
        }
        $literaryAllList = app::get("base")->database()->executeQuery($literaryAllSql)->fetchAll();
        $literaryList = app::get("base")->database()->executeQuery($literarySql)->fetchAll();

        $rowConut=  count($literaryAllList);
        $pageCount=  ceil($rowConut/$pageSize);
        $pagedata['pageCount'] = $pageCount;
        $pagedata['pageNow'] = $pageNow;     
        $pagedata["literaryAllList"]=$literaryAllList;
        $pagedata["literaryList"]=$literaryList;
		//$literarycatInfo=app::get("sysexpert")->model("literarycat")->getRow("*",array("literarycat_id"=>$literarycatid));
		//$pagedata["literarycatRow"]=$literarycatInfo;
		// $literaryInfo=app::get("sysexpert")->model("literary")->getList("*",array("literarycat_id_id"=>$literarycatid));
		// $pagedata["literaryRow"]=$literaryInfo;
		return $this->page('topc/expert/index.html',$pagedata);

	}

	public function literary()
	{
		$this->setlayoutFlag('expert');
		
        //对$pagedata赋值expert表
	    // $expertInfo=app::get("sysexpert")->model("expert")->getList("*");
     //    $pagedata["expertList"]=$expertInfo;
     //    //对$pagedata赋值literary表
	    // $literarycatInfo=app::get("sysexpert")->model("literarycat")->getList("*");
     //    $pagedata["literarycatList"]=$literarycatInfo;
     //    //对$pagedata赋值literarycat表
     //    $literaryInfo=app::get("sysexpert")->model("literary")->getList("*",array("towhere"=>1));
     //    $pagedata["literaryList"]=$literaryInfo;

        //取到点击的文章ID
		$literaryid=$_GET["literaryid"];
		$literaryInfo=app::get("sysexpert")->model("literary")->getRow("*",array("literary_id"=>$literaryid));
        if(!$literaryInfo){
            $pagedata["isfb"]="0";//不存在文章
            return $this->page('topc/expert/literaryDetile.html',$pagedata);
        }
        else{
            if($literaryInfo["towhere"]=="0"){
                $pagedata["isfb"]="1";//文章下架
                return $this->page('topc/expert/literaryDetile.html',$pagedata);
            }
            else{
                 $pagedata["isfb"]="2";//正常显示
            }
        }

		//点击 文章点击量加1
		$literaryInfo["click_count"]=$literaryInfo["click_count"]+1;
		app::get("sysexpert")->model("literary")->save($literaryInfo);
		$pagedata["literaryRow"]=$literaryInfo;
        //取到点击的文章的名人专家
		$expertid=$literaryInfo["expert_id"];
		$expertInfo=app::get("sysexpert")->model("expert")->getRow("*",array("expert_id"=>$expertid));
		$pagedata["expertRow"]=$expertInfo;
        //取到点击的文章的类型
		$literarycatid=$literaryInfo["literarycat_id"];
		$literarycatInfo=app::get("sysexpert")->model("literarycat")->getRow("*",array("literarycat_id"=>$literarycatid));
		$pagedata["literarycatRow"]=$literarycatInfo;
		//SQL取按文章点击量递减的文章
		$sql="select * from sysexpert_literary where towhere =1 and ishot=1 order by click_count desc LIMIT 6 ";
        $literaryInfo = app::get("base")->database()->executeQuery($sql)->fetchAll();
        $pagedata["hotliteraryLit"]=$literaryInfo;


		return $this->page('topc/expert/literaryDetile.html',$pagedata);
	}
    
	public function expert()
	{
		$this->setlayoutFlag('expert');

        //对$pagedata赋值expert表
	    $expertInfo=app::get("sysexpert")->model("expert")->getList("*");
        $pagedata["expertList"]=$expertInfo;
        //对$pagedata赋值literary表
	    $literarycatInfo=app::get("sysexpert")->model("literarycat")->getList("*");
        $pagedata["literarycatList"]=$literarycatInfo;
        //对$pagedata赋值literarycat表
        // $literaryInfo=app::get("sysexpert")->model("literary")->getList("*",array("towhere"=>1));
        // $pagedata["literaryList"]=$literaryInfo;
        
        //取到点击的专家ID
		$expertid=$_GET["expertid"];
		$expertInfo=app::get("sysexpert")->model("expert")->getRow("*",array("expert_id"=>$expertid));
		$pagedata["expertRow"]=$expertInfo;
        //取到点击的专家的文章表
		$literaryInfo=app::get("sysexpert")->model("literary")->getList("*",array("expert_id"=>$expertid,"towhere"=>1));
		$pagedata["literaryList"]=$literaryInfo;

		return $this->page('topc/expert/expertDetile.html',$pagedata);
	}


	   public function expertList()
	{
		$this->setlayoutFlag('expert');
		//对$pagedata赋值expert表
	    $expertInfo=app::get("sysexpert")->model("expert")->getList("*");
        $pagedata["expertList"]=$expertInfo;
        $literarycatInfo=app::get("sysexpert")->model("literarycat")->getList("*");
        $pagedata["literarycatList"]=$literarycatInfo;


        $pageNow=1;
        $pageSize=9;//九宫格
        if(!empty($_GET['pageNow'])){
            $pageNow=$_GET['pageNow'];
        }

        $expertAllSql="SELECT sl.* FROM sysexpert_expert sl";
        $expertSql="SELECT sl.* FROM sysexpert_expert sl LIMIT ".($pageNow-1)*$pageSize.",".$pageSize."";
        
        $expertAllList = app::get("base")->database()->executeQuery($expertAllSql)->fetchAll();
        $expertList = app::get("base")->database()->executeQuery($expertSql)->fetchAll();

        $rowConut=  count($expertAllList);
        $pageCount=  ceil($rowConut/$pageSize);
        $pagedata['pageCount'] = $pageCount;
        $pagedata['pageNow'] = $pageNow;     
        $pagedata["expertAllList"]=$expertAllList;
        $pagedata["expertList"]=$expertList;

		return $this->page('topc/expert/expertList.html',$pagedata);

	}

}
    
?>