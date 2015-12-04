<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_enterprise(&$setting){
        $enterprise = app::get('sysshop')->model('enterprise');
        $shop = app::get('sysshop')->model('shop');
        $cat = app::get('syscategory')->model('cat');
        $sprodrelease = app::get('sysshoppubt')->model('sprodrelease');
        $standard_item = app::get('sysshoppubt')->model('standard_item');
        $tender = app::get('sysshoppubt')->model('tender');
        $biddings = app::get('sysshoppubt')->model('biddings');
        $tradeorder = app::get('sysshoppubt')->model('tradeorder');
        $item = app::get('sysitem')->model('item');
        $seller = app::get('sysshop')->model('seller');
        $shopcat = app::get('sysshop')->model('shop_rel_lv1cat');

        $catids = $shopcat->getList('distinct cat_id',array('rel_id'=>$setting['node_select']));
        foreach ($catids as $key => $value) {
            $catids[$key] = $value['cat_id'];
        }
        $_return['catname'] = $cat->getList('cat_name,cat_id',array('cat_id'=>$catids));

        /*$catid = $cat->getList('cat_id',array('cat_id'=>$catids));
        foreach ($catid as $key => $value) {
            $catid[$key] = $value['cat_id'];
        }*/
        $countget = count($catids);
        for ($i=0; $i < $countget; $i++) { 
            $shopid[$i] = $shopcat->getList('shop_id,cat_id',array('cat_id'=>$catids[$i]));
        }
        foreach ($shopid as $key => $value) {
            foreach ($value as $key1 => $value1) {
                /*$counts = $sprodrelease->count(array('shop_id'=>$value1['shop_id']));
                $countt = $tender->count(array('shop_id'=>$value1['shop_id']));
                $countb = $biddings->count(array('shop_id'=>$value1['shop_id']));
                $countall = $counts + $countt + $countb;*/
                $countall = $tradeorder->count(array('shop_id'=>$value1['shop_id'],'state'=>1));
                
                $shopname = $shop->getRow('shop_name,shop_descript,shop_id,shop_logo',array('shop_id'=>$value1['shop_id']));
                $onecatid = $shopcat->getList('cat_id',array('shop_id'=>$shopname['shop_id']));

                foreach ($onecatid as $key2 => $value2) {
                    $onecatid[$key2] = $value2['cat_id'];
                }
                $types = $seller->getRow('seller_type',array('shop_id'=>$value1['shop_id']));
                $onecatname = $cat->getList('cat_name',array('cat_id'=>$onecatid));
                if($types['seller_type'] == 2){
                $rabish[$key][$key1]['cat_name'] = $onecatname;
                $rabish[$key][$key1]['cat_id'] = $value1['cat_id'];
                $rabish[$key][$key1]['shop_logo'] = $shopname['shop_logo'];
                $rabish[$key][$key1]['shopname'] = $shopname['shop_name'];
                $rabish[$key][$key1]['shop_descript'] = $shopname['shop_descript'];
                $rabish[$key][$key1]['count'] = $countall;
                }elseif($types['seller_type'] == 1){
                $resycle[$key][$key1]['cat_name'] = $onecatname;
                $resycle[$key][$key1]['cat_id'] = $value1['cat_id'];
                $resycle[$key][$key1]['shop_logo'] = $shopname['shop_logo'];
                $resycle[$key][$key1]['shopname'] = $shopname['shop_name'];
                $resycle[$key][$key1]['shop_descript'] = $shopname['shop_descript'];
                $resycle[$key][$key1]['count'] = $countall;
               
                }
            }
        }
        $j=0;$i=0;$m=0;$n=0;
        foreach ($rabish as $key2 => $value2) {
            foreach ($value2 as $key => $value) {
                $j=0;
            foreach ($value2 as $key1 => $value1) {
                if($value['green']<$value1['green']){$j++;}
            }
            while ($newresyclepd[$key2][$j]) {
                $j++;
            }
            $newresyclepd[$key2][$j] = $value;
            }

            for ($m=0; $m < count($newresyclepd[$key2]); $m++) { 
                foreach ($newresyclepd[$key2] as $key => $value) {
                    if($key==$m){
                    $resyclepdall[$key2][$m] = $value;break;
                    }
                }
                if($m==10)break;
            }
        }


        foreach ($resycle as $key2 => $value2) {
            foreach ($value2 as $key => $value) {
                $i=0;
            foreach ($value2 as $key1 => $value1) {
                if($value['green']<$value1['green']){$i++;}
            }
            while ($newrabishpd[$key2][$i]) {
                $i++;
            }
            $newrabishpd[$key2][$i] = $value;
        }
        for ($n=0; $n < count($newrabishpd[$key2]); $n++) { 
            foreach ($newrabishpd[$key2] as $key => $value) {
                if($key==$n){
                $rabishpdall[$key2][$n] = $value;break;
                }
            }
            if($n==10)break;
        }
        }
        $_return['rabish'] = $rabishpdall;
        $_return['resycle'] = $resyclepdall;
    return $_return;
}
?>