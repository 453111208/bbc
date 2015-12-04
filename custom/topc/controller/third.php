<?php
class topc_ctl_third extends topc_controller{
	public function third(){
        $data = input::get('page');
        $data -= 1;
        $data *= 10;
		$third = app::get('sysshop')->model('service');
		$thirdinfo = $third->getList('*',array('ifpub'=>1),$data,10);
		$countnum = $third->count(array('ifpub'=>1));
        $countarr=array();
        if($countnum%10!=0){
        $countnum = $countnum/10;
        $countnum = intval($countnum)+1;
        }else{
        $countnum = $countnum/10;  
        }
        for ($i=0; $i < intval($countnum); $i++) { 
            $countarr[$i] = $i+1;
        }
        $pagedata['countnum'] = $countarr;
		$pagedata['thirdinfo'] = $thirdinfo;
		$this->setLayoutFlag('third');
		return $this->page('topc/third/thirdlist.html',$pagedata);
	}
	public function thirdlist(){
		$data = input::get();
		$third = app::get('sysshop')->model('service');
		$thirdinfo = $third->getRow('*',array('article_id'=>$data['article_id'],"ifput"=>true));
		$this->setLayoutFlag('third');
		if(!$thirdinfo){
            $pagedata["isfb"]="0";//不存在文章
            return $this->page('topc/third/third.html',$pagedata);
        }
        else{
            if($thirdinfo["ifpub"]==false){
                $pagedata["isfb"]="1";//文章下架
                return $this->page('topc/third/third.html',$pagedata);
            }
            else{
                 $pagedata["isfb"]="2";//正常显示
            }
        }
		$oldthird = $thirdinfo;
		$thirdinfo['count'] += 1;
		try {
			$third->update($thirdinfo,$oldthird);
		} catch (Exception $e) {
			$msg = $e->getMessage();
          	return $this->splash('error',null,$msg);
		}
		$pagedata['thirdinfo'] = $thirdinfo;
		
		return $this->page('topc/third/third.html',$pagedata);
	}
}