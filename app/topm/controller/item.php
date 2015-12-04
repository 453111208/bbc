<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topm_ctl_item extends topm_controller {

    public function __construct($app)
    {
        parent::__construct();
        $this->setLayoutFlag('product');
    }

    private function __setting()
    {
        $setting['image_default_id']= app::get('image')->getConf('image.set');
        return $setting;
    }

    public function index()
    {
        $itemId = intval(input::get('item_id'));
        if( empty($itemId) )
        {
            return redirect::action('topm_ctl_default@index');
        }

        if( userAuth::check() )
        {
            $pagedata['nologin'] = 1;
        }

        $pagedata['user_id'] = userAuth::id();

        $pagedata['image_default_id'] = $this->__setting();

        $params['item_id'] = $itemId;
        $params['use_platform'] = 1;
        $params['fields'] = "*,item_desc.wap_desc,item_count,item_store,item_status,sku,item_nature,spec_index";
        $detailData = app::get('topm')->rpcCall('item.get',$params);

        if(!$detailData)
        {
            $pagedata['error'] = "商品过期不存在";
            return $this->page('topm/items/error.html', $pagedata);
        }

        if(count($detailData['sku']) == 1)
        {
            $detailData['default_sku_id'] = array_keys($detailData['sku'])[0];
        }

        $detailData['valid'] = $this->__checkItemValid($detailData);

        if($detailData['use_platform'] != 2 && $detailData['use_platform'] != 0)
        {
            redirect::action('topm_ctl_item@index',array('item_id'=>$itemId))->send();exit;
        }
        //相册图片
        if( $detailData['list_image'] )
        {
            $detailData['list_image'] = explode(',',$detailData['list_image']);
        }

        //获取商品的促销信息
        $promotionDetail = app::get('topm')->rpcCall('item.promotiontag.get', array('item_id'=>$itemId));
        if($promotionDetail)
        {
            $promotionIds = explode(',', $promotionDetail['promotion_ids']);
            foreach($promotionIds as $promotionId)
            {
                $basicPromotionInfo = app::get('topm')->rpcCall('promotion.promotion.get', array('promotion_id'=>$promotionId, 'platform'=>'wap'));
                if($basicPromotionInfo['valid']===true)
                {
                    $pagedata['promotionDetail'][$promotionId] = $basicPromotionInfo;
                }
            }
        }
        $pagedata['promotion_count'] = count($pagedata['promotionDetail']);

        // 活动促销(如名字叫团购)
        $activityDetail = app::get('topm')->rpcCall('promotion.activity.item.info',array('item_id'=>$itemId,'valid'=>1),'buyer');
        if($activityDetail)
        {
            $pagedata['activityDetail'] = $activityDetail;
        }

        $detailData['spec'] = $this->__getSpec($detailData['spec_desc'], $detailData['sku']);

        $pagedata['item'] = $detailData;

        $pagedata['shopCat'] = app::get('topm')->rpcCall('shop.cat.get',array('shop_id'=>$pagedata['item']['shop_id']));

        $pagedata['shop'] = app::get('topm')->rpcCall('shop.get',array('shop_id'=>$pagedata['item']['shop_id']));
        $pagedata['next_page'] = url::action("topm_ctl_item@index",array('item_id'=>$itemId));

        if(empty($pagedata['item']['item_id']))
        {
            return $this->page('topm/items/goodsEmpty.html');
        }

        //设置此页面的seo
        $brand = app::get('topm')->rpcCall('category.brand.get.info',array('brand_id'=>$detailData['brand_id']));
        $cat = app::get('topm')->rpcCall('category.cat.get.info',array('cat_id'=>$detailData['cat_id']));
        $seoData = array(
            'item_title' => $detailData['title'],
            'shop_name' =>$pagedata['shop']['shop_name'],
            'item_bn' => $detailData['bn'],
            'item_brand' => $brand['brand_name'],
            'item_cat' =>$cat[$detailData['cat_id']]['cat_name'],
            'sub_title' =>$detailData['sub_title'],
        );
        seo::set('topm.item.detail',$seoData);
//echo '<pre>';print_r($pagedata);exit();
        return $this->page('topm/items/index.html', $pagedata);
    }

    private function __checkItemValid($itemsInfo)
    {
        if( empty($itemsInfo) ) return false;

        //违规商品
        if( $itemsInfo['violation'] == 1 ) return false;

        //未启商品
        if( $itemsInfo['disabled'] == 1 ) return false;

        //未上架商品
        if($itemsInfo['approve_status'] == 'instock' ) return false;

        //库存小于或者等于0的时候，为无效商品
        //if($itemsInfo['realStore'] <= 0 ) return false;

        return true;
    }


    private function __getSpec($spec, $sku)
    {
        if( empty($spec) ) return array();

        foreach( $sku as $row )
        {
            $key = implode('_',$row['spec_desc']['spec_value_id']);

            if( $key )
            {
                $result['specSku'][$key]['sku_id'] = $row['sku_id'];
                $result['specSku'][$key]['item_id'] = $row['item_id'];
                $result['specSku'][$key]['price'] = $row['price'];
                $result['specSku'][$key]['store'] = $row['realStore'];
                if( $row['status'] == 'delete')
                {
                    $result['specSku'][$key]['valid'] = false;
                }
                else
                {
                    $result['specSku'][$key]['valid'] = true;
                }

                $specIds = array_flip($row['spec_desc']['spec_value_id']);
                $specInfo = explode('、',$row['spec_info']);
                foreach( $specInfo  as $info)
                {
                    $id = each($specIds)['value'];
                    $result['specName'][$id] = explode('：',$info)[0];
                }
            }
        }
        return $result;
    }
    //商品照片
    public function itemPic()
    {
        $itemId = intval(input::get('item_id'));
        if( empty($itemId) )
        {
            return redirect::action('topm_ctl_default@index');
        }

        $pagedata['image_default_id'] = $this->__setting();
        $params['item_id'] = $itemId;
        $params['fields'] = "*,item_desc.wap_desc,item_count,item_store,item_status,sku,item_nature,spec_index";
        $detailData = app::get('topm')->rpcCall('item.get',$params);
        $pagedata['title'] = "商品描述";

        $pagedata['itemPic'] = $detailData;
        return $this->page('topm/items/itempic.html', $pagedata);
    }
    //商品参数
    public function itemParams()
    {
        $itemId = intval(input::get('item_id'));
        if( empty($itemId) )
        {
            return redirect::action('topm_ctl_default@index');
        }

        $pagedata['image_default_id'] = $this->__setting();
        $params['item_id'] = $itemId;
        $params['fields'] = "*,item_desc.wap_desc,item_count,item_store,item_status,sku,item_nature,spec_index";
        $detailData = app::get('topm')->rpcCall('item.get',$params);

        $pagedata['itemParams'] = $detailData['params'];
        $pagedata['title'] = "商品参数";
        return $this->page('topm/items/itemparams.html', $pagedata);
    }

    public function getItemRate()
    {
        $itemId = input::get('item_id');
        if( empty($itemId) ) return '';

        $pagedata =  $this->__searchRate($itemId);
        $pagedata['item_id'] = $itemId;

        $pagedata['title'] = '产品评价';
        return $this->page('topm/items/rate/index.html', $pagedata);
    }

    public function getItemRateList()
    {
        $itemId = input::get('item_id');

        $pagedata =  $this->__searchRate($itemId);

        if( input::get('json') )
        {
            $data['html'] = view::make('topm/items/rate/list.html',$pagedata)->render();
            $data['pagers'] = $pagedata['pagers'];
            $data['success'] = true;
            return response::json($data);exit;
        }

        return view::make('topm/items/rate/list.html',$pagedata);
    }

    private function __searchRate($itemId)
    {
        $current = input::get('pages',1);
        $limit = 10;
        $params = ['item_id'=>$itemId,'page_no'=>$current,'page_size'=>$limit,'fields'=>'*'];

        if( input::get('query_type') == 'content'  )
        {
            $params['is_content'] = true;
        }
        elseif( input::get('query_type') == 'pic' )
        {
            $params['is_pic'] = true;
        }

        $data = app::get('topm')->rpcCall('rate.list.get', $params);
        foreach($data['trade_rates'] as $k=>$row )
        {
            if($row['rate_pic'])
            {
                $data['trade_rates'][$k]['rate_pic'] = explode(",",$row['rate_pic']);
            }

            $userId[] = $row['user_id'];
        }

        $pagedata['rate']= $data['trade_rates'];
        if( $userId )
        {
            $pagedata['userName'] = app::get('topm')->rpcCall('user.get.account.name',array('user_id'=>$userId),'buyer');
        }

        //处理翻页数据
        $filter = input::get();
        $pagedata['filter'] = $filter;
        $filter['pages'] = time();
        if($data['total_results']>0) $total = ceil($data['total_results']/$limit);
        $current = $total < $current ? $total : $current;
        $pagedata['pagers'] = array(
            //'link'=>url::action('topm_ctl_item@getItemRateList',$filter),
            'current'=>$current,
            'total'=>$total,
        );

        return $pagedata;
    }
}

