<?php
class topc_ctl_enterprise extends topc_controller{
	//企业库
	public function enterprise(){
		$enterprise = app::get('sysshop')->model('enterprise');
		$shop = app::get('sysshop')->model('shop');
		$cat = app::get('syscategory')->model('cat');
		$shopcat = app::get('sysshop')->model('shop_rel_lv1cat');
		$resycle = $enterprise->getList('*');

		foreach ($resycle as $key => $value) {
			$shopinfo = $shop->getRow('shop_name,shop_descript,shop_logo',array('shop_id'=>$value['shop_id']));
			$shopcats = $shopcat->getList('cat_id',array('shop_id'=>$value['shop_id']));
			foreach ($shopcats as $key1 => $value1) {
				$shopcatinfo[$key1] = $cat->getRow('cat_name',array('cat_id'=>$value1['cat_id']));
			}
			$resycle[$key]['cat_name'] = $shopcatinfo;
			$resycle[$key]['shop_name'] = $shopinfo['shop_name'];
			$resycle[$key]['shop_descript'] = $shopinfo['shop_descript'];
			$resycle[$key]['shop_logo'] = $shopinfo['shop_logo'];
		}
		foreach ($resycle as $key => $value) {
			if($value['seller_type'] == 1){
				$resyclepd[$key] = $value;
			}
			if($value['seller_type'] == 2){
				$rabishpd[$key] = $value;
			}
		}
		$i=0;$j=0;
		foreach ($resyclepd as $key => $value) {
				$j=0;
			foreach ($resyclepd as $key1 => $value1) {
				if($value['green']<$value1['green']){$j++;}
			}
			while ($newresyclepd[$j]) {
				$j++;
			}
			$newresyclepd[$j] = $value;
		}
		for ($m=0; $m < count($newresyclepd); $m++) { 
			foreach ($newresyclepd as $key => $value) {
				if($key==$m){
				$resyclepdall[$m] = $value;break;
				}
			}
			if($m==10)break;
		}
		foreach ($rabishpd as $key => $value) {
				$i=0;
			foreach ($rabishpd as $key1 => $value1) {
				if($value['green']<$value1['green']){$i++;}
			}
			while ($newrabishpd[$i]) {
				$i++;
			}
			$newrabishpd[$i] = $value;
		}
		for ($n=0; $n < count($newrabishpd); $n++) { 
			foreach ($newrabishpd as $key => $value) {
				if($key==$n){
				$rabishpdall[$n] = $value;break;
				}
			}
			if($n==10)break;
		}
		$pagedata['resyclepd'] = $resyclepdall;
		$pagedata['rabishpd'] = $rabishpdall;
		$this->setLayoutFlag('enterprise');
	 	return $this->page('topc/enterprise/enterprise.html',$pagedata);
	}
}