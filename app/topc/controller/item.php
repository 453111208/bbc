<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
use Endroid\QrCode\QrCode;
class topc_ctl_item extends topc_controller {

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
            return redirect::action('topc_ctl_default@index');
        }

        if( userAuth::check() )
        {
            $pagedata['nologin'] = 1;
        }

        $pagedata['user_id'] = userAuth::id();

        $pagedata['image_default_id'] = $this->__setting();

        $params['item_id'] = $itemId;
        $params['fields'] = "*,item_desc.pc_desc,item_count,item_store,item_status,sku,item_nature,spec_index";
        $detailData = app::get('topc')->rpcCall('item.get',$params);
        if(!$detailData)
        {
            $pagedata['error'] = "很抱歉，您查看的宝贝不存在，可能已下架或者被转移";
            return $this->page('topc/items/error.html', $pagedata);
        }
        if(count($detailData['sku']) == 1)
        {
            $detailData['default_sku_id'] = array_keys($detailData['sku'])[0];
        }

        $detailData['valid'] = $this->__checkItemValid($detailData);

        //判断此商品发布的平台，如果是wap端，跳转至wap链接
        if($detailData['use_platform'] == 2 )
        {
            redirect::action('topm_ctl_item@index',array('item_id'=>$itemId))->send();exit;
        }

        //相册图片
        if( $detailData['list_image'] )
        {
            $detailData['list_image'] = explode(',',$detailData['list_image']);
        }
        //获取商品的促销信息
        $promotionDetail = app::get('topc')->rpcCall('item.promotiontag.get', array('item_id'=>$itemId));
        if($promotionDetail)
        {
            $promotionIds = explode(',', $promotionDetail['promotion_ids']);
            foreach($promotionIds as $promotionId)
            {
                $basicPromotionInfo = app::get('topc')->rpcCall('promotion.promotion.get', array('promotion_id'=>$promotionId, 'platform'=>'pc'));
                if($basicPromotionInfo['valid']===true)
                {
                    $pagedata['promotionDetail'][$promotionId] = $basicPromotionInfo;
                }
            }
        }
        $pagedata['promotion_count'] = count($pagedata['promotionDetail']);
        // 活动促销(如名字叫团购)
        $activityDetail = app::get('topc')->rpcCall('promotion.activity.item.info',array('item_id'=>$itemId,'valid'=>1),'buyer`');
        if($activityDetail)
        {
            $pagedata['activityDetail'] = $activityDetail;
        }
        $detailData['spec'] = $this->__getSpec($detailData['spec_desc'], $detailData['sku']);
        $detailData['qrCodeData'] = $this->__qrCode($itemId);
        $pagedata['item'] = $detailData;

        //获取商品详情页左侧店铺分类信息
        $pagedata['shopCat'] = app::get('topc')->rpcCall('shop.cat.get',array('shop_id'=>$pagedata['item']['shop_id']));

        //获取该商品的店铺信息
        $pagedata['shop'] = app::get('topc')->rpcCall('shop.get',array('shop_id'=>$pagedata['item']['shop_id']));
        //获取该商品店铺的DSR信息
        $pagedata['shopDsrData'] = $this->__getShopDsr($pagedata['item']['shop_id']);

        $pagedata['next_page'] = url::action("topc_ctl_item@index",array('item_id'=>$itemId));

        if( $pagedata['user_id'] )
        {
            //获取该用户的最近购买记录
            $pagedata['buyerList'] = app::get('topc')->rpcCall('trade.user.buyerList',array('user_id'=>$pagedata['user_id']));
        }

        //设置此页面的seo
        $brand = app::get('topc')->rpcCall('category.brand.get.info',array('brand_id'=>$detailData['brand_id']));
        $cat = app::get('topc')->rpcCall('category.cat.get.info',array('cat_id'=>$detailData['cat_id']));
        $seoData = array(
            'item_title' => $detailData['title'],
            'shop_name' =>$pagedata['shop']['shop_name'],
            'item_brand' => $brand['brand_name'],
            'item_bn' => $detailData['bn'],
            'item_cat' =>$cat[$detailData['cat_id']]['cat_name'],
            'sub_title' =>$detailData['sub_title'],
        );
        seo::set('topc.item.detail',$seoData);
        //echo '<pre>';print_r($pagedata);exit();
        return $this->page('topc/items/index.html', $pagedata);
    }

    private function __qrCode($itemId)
    {
        $url = url::action("topm_ctl_item@index",array('item_id'=>$itemId));
        $qrCode = new QrCode();
        return $qrCode
            ->setText($url)
            ->setSize(80)
            ->setPadding(10)
            ->setErrorCorrection(1)
            ->setForegroundColor(array('r' => 227, 'g' => 135, 'b' => 44, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16)
            ->getDataUri('png');
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

        return true;
    }

    private function __getShopDsr($shopId)
    {
        $params['shop_id'] = $shopId;
        $params['catDsrDiff'] = true;
        $dsrData = app::get('topc')->rpcCall('rate.dsr.get', $params);
        if( !$dsrData )
        {
            $countDsr['tally_dsr'] = sprintf('%.1f',5.0);
            $countDsr['attitude_dsr'] = sprintf('%.1f',5.0);
            $countDsr['delivery_speed_dsr'] = sprintf('%.1f',5.0);
        }
        else
        {
            $countDsr['tally_dsr'] = sprintf('%.1f',$dsrData['tally_dsr']);
            $countDsr['attitude_dsr'] = sprintf('%.1f',$dsrData['attitude_dsr']);
            $countDsr['delivery_speed_dsr'] = sprintf('%.1f',$dsrData['delivery_speed_dsr']);
        }
        $shopDsrData['countDsr'] = $countDsr;
        $shopDsrData['catDsrDiff'] = $dsrData['catDsrDiff'];
        return $shopDsrData;
    }

    private function __getRateResultCount($itemId)
    {
        $countRateData = app::get('topc')->rpcCall('item.get.count',array('item_id'=>$itemId,'fields'=>'item_id,rate_count,rate_good_count,rate_neutral_count,rate_bad_count'));
        if( !$countRateData[$itemId]['rate_count'] )
        {
            $countRate['good']['num'] = 0;
            $countRate['good']['percentage'] = '0%';
            $countRate['neutral']['num'] = 0;
            $countRate['neutral']['percentage'] = '0%';
            $countRate['bad']['num'] = 0;
            $countRate['bad']['percentage'] = '0%';
            return $countRate;
        }
        $countRate['good']['num'] = $countRateData[$itemId]['rate_good_count'];
        $countRate['good']['percentage'] = sprintf('%.2f',$countRateData[$itemId]['rate_good_count']/$countRateData[$itemId]['rate_count'])*100 .'%';
        $countRate['neutral']['num'] = $countRateData[$itemId]['rate_neutral_count'];
        $countRate['neutral']['percentage'] = sprintf('%.2f',$countRateData[$itemId]['rate_neutral_count']/$countRateData[$itemId]['rate_count'])*100 .'%';
        $countRate['bad']['num'] = $countRateData[$itemId]['rate_bad_count'];
        $countRate['bad']['percentage'] = sprintf('%.2f',$countRateData[$itemId]['rate_bad_count']/$countRateData[$itemId]['rate_count'])*100 .'%';
        return $countRate;
    }

    public function getItemRate()
    {
        $itemId = input::get('item_id');
        if( empty($itemId) ) return '';

        $pagedata =  $this->__searchRate($itemId);
        $pagedata['countRate'] = $this->__getRateResultCount($itemId);
        $pagedata['item_id'] = $itemId;

        return view::make('topc/items/rate.html', $pagedata);
    }

    public function getItemRateList()
    {
        $itemId = input::get('item_id');

        $pagedata =  $this->__searchRate($itemId);

        return view::make('topc/items/rate/list.html',$pagedata);
    }

    private function __searchRate($itemId)
    {
        $current = input::get('pages',1);
        $params = ['item_id'=>$itemId,'page_no'=>$current,'page_size'=>10,'fields'=>'*'];

        if( in_array(input::get('result'), ['good','bad', 'neutral']) )
        {
            $params['result'] = input::get('result');
            $pagedata['result'] = $params['result'];
        }
        else
        {
            $pagedata['result'] = 'all';
        }
        if( input::get('content') )
        {
            $params['is_content'] = true;
        }
        if( input::get('picture') )
        {
            $params['is_pic'] = true;
        }

        $data = app::get('topc')->rpcCall('rate.list.get', $params);
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
            $pagedata['userName'] = app::get('topc')->rpcCall('user.get.account.name',array('user_id'=>$userId));
        }

        //处理翻页数据
        $filter = input::get();
        $filter['pages'] = time();
        if($data['total_results']>0) $total = ceil($data['total_results']/10);
        $current = $total < $current ? $total : $current;
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_item@getItemRateList',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );

        return $pagedata;
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

    //以下为商品咨询
    public function getItemConsultation()
    {
        $itemId = input::get('item_id');
        if( empty($itemId) ) return '';

        $pagedata =  $this->__searchConsultation($itemId);
        $pagedata['item_id'] = $itemId;
        $pagedata['user_id'] = userAuth::id();

        return view::make('topc/items/consultation.html', $pagedata);
    }

    public function getItemConsultationList()
    {
        $itemId = input::get('item_id');
        $pagedata =  $this->__searchConsultation($itemId);
        return view::make('topc/items/consultation/list.html',$pagedata);
    }

    private function __searchConsultation($itemId)
    {
        $current = input::get('pages',1);
        $params = ['item_id'=>$itemId,'user_id'=>userAuth::id(),'page_no'=>$current,'page_size'=>10,'fields'=>'*'];

        if( in_array(input::get('result'), ['item','store_delivery', 'payment','invoice']) )
        {
            $params['type'] = input::get('result');
            $pagedata['result'] = 'all';
        }
        else
        {
            $pagedata['result'] = 'all';
        }

        $data = app::get('topc')->rpcCall('rate.gask.list', $params);

        $pagedata['gask']= $data['lists'];
        $pagedata['count'] = app::get('topc')->rpcCall('rate.gask.count', $params);

        //处理翻页数据
        $filter = input::get();
        $pagedata['filter'] = $filter;
        $filter['pages'] = time();
        if($data['total_results']>0) $total = ceil($data['total_results']/10);
        $current = $total < $current ? $total : $current;
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_item@getItemConsultationList',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );
        return $pagedata;
    }

    /**
     * @brief 商品咨询提交
     *
     * @return
     */
    public function commitConsultation()
    {
        $post = input::get('gask');
        $params['item_id'] = $post['item_id'];
        $params['content'] = $post['content'];
        $params['type'] = $post['type'];
        $params['is_anonymity'] = $post['is_anonymity'] ? $post['is_anonymity'] : 0;

       if(userAuth::id())
        {
            $params['user_name'] = userAuth::getLoginName();
            $params['user_id'] = userAuth::id();
        }
        else
        {
            if(!$post['contack'])
            {
                return $this->splash('error',$url,"由于您没有登录，咨询请填写联系方式",true);
            }
            $params['contack'] = $post['contack'];
            $params['user_name'] = '游客';
            $params['user_id'] = "0";
        }

        try{
            if($params['contack'])
            {
                $type = kernel::single('pam_tools')->checkLoginNameType($params['contack']);
                if($type == "login_account")
                {
                    throw new \LogicException('请填写正确的联系方式(手机号或邮箱)');
                }
            }

            $params = utils::_filter_input($params);
            $result = app::get('topc')->rpcCall('rate.gask.create',$params);
            $msg = '咨询提交失败';
        }
        catch(\Exception $e)
        {
            $result = false;
            $msg = $e->getMessage();
        }

        if( !$result )
        {
            return $this->splash('error',$url,$msg,true);
        }

        $url = url::action('topc_ctl_item@index',array('item_id'=>$postdata['item_id']));

        $msg = '咨询提交成功,请耐心等待商家审核、回复';
        return $this->splash('success',$url,$msg,true);
    }
}


