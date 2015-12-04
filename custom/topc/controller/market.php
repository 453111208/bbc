<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topc_ctl_market extends topc_controller {
    //获取价格行情详细信息
    public function details()
    {
        $this->setLayoutFlag('marketDetails');
        $data_id = input::get("dataId");
        $artList = app::get("sysinfo")->model("marketdata")->getList("*",array('data_id'=>$data_id));
        $thirdSort=$artList[0]['third_sort'];
        $secondSort=$artList[0]['second_sort'];
        $date=$artList[0]['date'];
        $dataList = app::get("sysinfo")->model("marketdata")->getList("*",array('third_sort'=>$thirdSort,'second_sort'=>$secondSort,'date'=>$date));
        $pagedata['dataList'] = $dataList;
        $pagedata['artList'] = $artList[0];
        $date=explode("年",$dataList[0]['date']);
        $pagedata['date'] = $date[1];
        return $this->page('topc/market/details.html', $pagedata);
    }
    //获取行情文章详细页  
    public function getInfo()
    {
        $this->setLayoutFlag('marketDetails');
        $article_id = input::get("articleId");
        $artList = app::get("sysinfo")->model("marketArticle")->getList("*",array('article_id'=>$article_id));
        $node_id=$artList[0]['node_id'];
        $clickRate=$artList[0]['click_rate']+1;
        $sql="update sysinfo_marketArticle set click_rate = '".$clickRate."' where article_id = '".$article_id."'";
        app::get('sysinfo')->database()->executeUpdate($sql);
        $nodeList = app::get("sysinfo")->model("marketNode")->getList("*",array('node_id'=>$node_id));
        $parent_id=$nodeList[0]['parent_id'];
        $parentList = app::get("sysinfo")->model("marketNode")->getList("*",array('node_id'=>$parent_id));
        $pagedata['parentList'] = $parentList[0];
        $pagedata['nodeList'] = $nodeList[0];
        $pagedata['artList'] = $artList[0];
        return $this->page('topc/market/marketInfo.html', $pagedata);
    }
     //获取行情栏目列表页  
    public function getNode()
    {
        $this->setLayoutFlag('marketDetails');
        $pageNow=1;
        $pageSize=10;
        $nodeName = input::get("nodeName");
        $nodeId = input::get("nodeId");
        if(!empty($_GET['pageNow'])){
            $pageNow=$_GET['pageNow'];
        }
        $sql="SELECT * FROM sysinfo_marketArticle where node_name='".$nodeName."' and node_id='".$nodeId."' limit ".($pageNow-1)*$pageSize.",".$pageSize."";
        $List=app::get("base")->database()->executeQuery($sql)->fetchAll();
        foreach($List as $key=>$value){
            $date=date("Y-m-d ",$value['pubtime']); 
            $List[$key]['pubtime'] = $date;
        }
        $pagedata['List']= $List;
        $artList = app::get("sysinfo")->model("marketArticle")->getList("*",array('node_name'=>$nodeName,'node_id'=>$nodeId));
        $parentId=$artList[0]['parent_id'];
        $secondList = app::get("sysinfo")->model("marketNode")->getList("*",array('node_id'=>$parentId));
        $pagedata['node_name'] = $secondList[0]['node_name'];
        $pagedata['nodeId'] = $artList[0]['node_id'];
        $pagedata['artList'] = $artList;
        $rowConut=  count($artList);
        $pageCount=  ceil($rowConut/$pageSize);
        $pagedata['pageCount'] = $pageCount;
        $pagedata['pageNow'] = $pageNow;
        $pagedata['nodeName'] = $nodeName;
        return $this->page('topc/market/marketNode.html', $pagedata);
    }
    //获取废电子电器商家报价列表页  
    public function getOffer()
    {
        $this->setLayoutFlag('marketDetails');
        $sql="SELECT * FROM sysinfo_offer order by offer_id desc limit 20";
        $offerList=app::get("base")->database()->executeQuery($sql)->fetchAll();
        $pagedata['offerList'] = $offerList;
        return $this->page('topc/market/marketOffer.html', $pagedata);
    }
    //获取行情列表页  
    public function getList()
    {
        $this->setLayoutFlag('marketDetails');
        $second_sort = input::get("secondSort");
        $pageNow=1;
        $pageSize=10;
        $sql="select third_sort FROM sysinfo_marketdata where second_sort='".$second_sort."' group by third_sort order by data_id asc limit 20";
        $sortList=app::get("base")->database()->executeQuery($sql)->fetchAll();
        if(empty($_GET['thirdSort'])){
            if(!empty($_GET['pageNow'])){
                $pageNow=$_GET['pageNow'];
            }
            $all="SELECT * FROM sysinfo_marketdata where second_sort='".$second_sort."' order by date desc";
            $allList=app::get("base")->database()->executeQuery($all)->fetchAll();
            $defaultList="SELECT * FROM sysinfo_marketdata where second_sort='".$second_sort."' order by date desc limit ".($pageNow-1)*$pageSize.",".$pageSize."";
            $List=app::get("base")->database()->executeQuery($defaultList)->fetchAll();
            foreach ($List as $key => $value) {
                    $List[$key]['adate']= substr($value['date'],7,10);
                }
            $rowConut=  count($allList);
            $pageCount=  ceil($rowConut/$pageSize);
            $pagedata['pageCount']= $pageCount;
            $pagedata['pageNow'] = $pageNow;
            $pagedata['List'] = $List;
            $pagedata['allList'] = $allList;
        }else{
            $third_sort=$_GET['thirdSort'];
            if(!empty($_GET['pageNow'])){
                $pageNow=$_GET['pageNow'];
            }
            $all="SELECT * FROM sysinfo_marketdata where second_sort='".$second_sort."' and third_sort='".$third_sort."' order by date desc";
            $allList=app::get("base")->database()->executeQuery($all)->fetchAll();
            $defaultList="SELECT * FROM sysinfo_marketdata where second_sort='".$second_sort."' and third_sort='".$third_sort."' order by date desc limit ".($pageNow-1)*$pageSize.",".$pageSize."";
            $List=app::get("base")->database()->executeQuery($defaultList)->fetchAll();
            foreach ($List as $key => $value) {
                    $List[$key]['adate']= substr($value['date'],7,10);
                }
            $rowConut=  count($allList);
            $pageCount=  ceil($rowConut/$pageSize);
            $pagedata['pageCount']= $pageCount;
            $pagedata['pageNow'] = $pageNow;
            $pagedata['List'] = $List;
            $pagedata['allList'] = $allList;
            $pagedata['third_sort'] = $third_sort;
        }
        $pagedata['sortList'] = $sortList;
        $pagedata['secondSort'] = $second_sort;
        return $this->page('topc/market/marketList.html', $pagedata);
    }
    //获取价格趋势图  
    public function marketTrend()
    {
        $this->setLayoutFlag('marketDetails');
        $thirdSort = input::get("third_sort");
        $title = input::get("title");
        $dateNow = input::get("date");
        $PriceRun = input::get("price_run");
        $dataList = app::get("sysinfo")->model("marketdata")->getList("*",array('third_sort'=>$thirdSort,'title'=>$title,'date'=>$dateNow,'price_run'=>$PriceRun));
        $date=explode("年",$dateNow);
        $pagedata['date'] = $date[1];
        $pagedata['area'] = $dataList[0]['area'];
        $pagedata['third_sort'] = $dataList[0]['third_sort'];
        if($dataList[0]['price_run'] == "走势图" || $dataList[0]['price_run'] == "折线图"){
               $pagedata['run_type'] = 1;
        } elseif ($dataList[0]['price_run'] == "柱状图") {
               $pagedata['run_type'] = 2;
        }
        $secondSort= $dataList[0]['second_sort'];
        $pagedata['second_sort'] = $secondSort;
        $pagedata['realdate'] = $dateNow;
        $List = app::get("sysinfo")->model("marketdata")->getList("*",array('third_sort'=>$thirdSort,'title'=>$title,'second_sort'=>$secondSort));
        foreach ($List as $key => $value) {
            $pagedata['data'][$key]['price'] = $value['price'];
            $pagedata['data'][$key]['dates']= substr($value['date'],7,10);
        }
        return $this->page('topc/market/marketTrend.html', $pagedata);
    }

}