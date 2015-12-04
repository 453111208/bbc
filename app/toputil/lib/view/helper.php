<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class toputil_view_helper{

    public function function_pagers($params,&$smarty)
    {

        if(!$params['data']['current'])$params['data']['current'] = 1;
        if(!$params['data']['total'])$params['data']['total'] = 1;
        if($params['data']['total']<2){
            return '';
        }

        $c = $params['data']['current'];
        $t = $params['data']['total'];
        $l=$params['data']['link'];
        $p=$params['data']['token'];

        if($c > 1 ){
            $head = str_replace($p,$c-1,$l);
        }

        if($c <= 6){
            for($i = 1; $i < $c;$i++){
                $links[] =  array(
                    'link' => str_replace($p,$i,$l),
                    'num' => $i,
                );
            }
        }else{
            $links = array(
                array(
                    'link' => str_replace($p,1,$l),
                    'num' => 1,
                ),
                array(
                    'link' => str_replace($p,2,$l),
                    'num' => 2,
                ),
                array(
                    'link' => '',
                    'num' => '...',
                ),
                array(
                    'link' => str_replace($p,$c-2,$l),
                    'num' => $c-2,
                ),
                array(
                    'link' => str_replace($p,$c-1,$l),
                    'num' => $c-1,
                ),
            );
        }

        $links[] =array(
            'link' => str_replace($p,$c,$l),
            'num' => $c,
        );

        if( ($t-$c) <= 5){
            for($i=$c+1 ; $i<=$t ;$i++ ){
                $links[] = array(
                    'link' => str_replace($p,$i,$l),
                    'num' => $i,
                );
            }
        }else{
            $links[] = array(
                'link' => str_replace($p,$c+1,$l),
                'num' => $c+1,
            );
            $links[] = array(
                'link' => str_replace($p,$c+2,$l),
                'num' => $c+2,
            );
            $links[] = array(
                'link' => '',
                'num' => '...',
            );
            $links[] = array(
                'link' => str_replace($p,$t-1,$l),
                'num' => $t-1,
            );
            $links[] = array(
                'link' => str_replace($p,$t,$l),
                'num' => $t,
            );
        }

        if($c < $t){
            $tail = str_replace($p,$c+1,$l);
        }

        //迷你翻页
        if($params['type'] == "mini")
        {
            $pagedata['head'] = $head;
            $pagedata['tail'] = $tail;
            $pagedata['c'] = $c;
            $pagedata['total'] = $t;
            return view::make('toputil/smarty/mini_pagers.html', $pagedata)->render();
        }

        $pagedata['ajax'] = $params['ajax'] ? 'true': 'false';
        if( $pagedata['ajax'] )
        {
            $pagedata['load_id'] = $params['load_id'] ? $params['load_id'] : 'page-reponse';
        }
        if($params['type'] == "wap")
        {
            $pagedata['links'] = $links;
            $pagedata['head'] = $head;
            $pagedata['tail'] = $tail;
            $pagedata['c'] = $c;
            $pagedata['use_app'] = $params['data']['use_app'] ? $params['data']['use_app'] : 'topc';

            return view::make('toputil/smarty/wap_pagers.html', $pagedata)->render();
        }

        $pagedata['links'] = $links;
        $pagedata['head'] = $head;
        $pagedata['tail'] = $tail;
        $pagedata['c'] = $c;
        $pagedata['use_app'] = $params['data']['use_app'] ? $params['data']['use_app'] : 'topc';
        return view::make('toputil/smarty/pagers.html', $pagedata)->render();
    }

    public function function_im($params, &$smarty)
    {
        $pagedata['qq'] = $params['qq'] ? $params['qq'] : null;
        $pagedata['wangwang'] = $params['wangwang'] ? $params['wangwang'] : null;

        if( $params['shop_id'] )
        {
            $shopdata = app::get('toputil')->rpcCall('shop.get',array('shop_id'=>$params['shop_id']));
            $pagedata['qq'] = $shopdata['qq'];
            $pagedata['wangwang'] = $shopdata['wangwang'];
        }

        $pagedata['type'] = $params['type'] ? $params['type'] : 'large';

        $pagedata['res_full_url'] = app::get('toputil')->res_full_url;

        if( empty($pagedata['qq']) && empty($pagedata['wangwang']) )
        {
            return '';
        }

        return view::make('toputil/smarty/im.html', $pagedata)->render();
    }

    public function modifier_shopname($shopId)
    {
        if($shopId)
        {
            $shopdata = app::get('toputil')->rpcCall('shop.get',array('shop_id'=>$shopId));
            return $shopdata['shopname'];
        }
    }

    public function modifier_region($r)
    {
        list($regions,$region_id) = explode(':',$r);
        if($region_id)
        {
            return str_replace('/','-',$regions);
        }
        else
        {
            return $r;
        }
    }
}


