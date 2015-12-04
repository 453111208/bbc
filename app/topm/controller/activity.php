<?php
class topm_ctl_activity extends topm_controller{
    public $limit = 20;
    public function index()
    {
        $pagedata['title'] = "活动列表";
        $post = input::get();
        $params = array(
            'release_time' => "sthan",
            'end_time' => "bthan",
            'order_by' => 'mainpush desc',
            'fields' => 'activity_name,activity_id,mainpush,slide_images',
        );
        $activity = app::get('topm')->rpcCall('promotion.activity.list',$params);
        if(!$activity['data'])
        {
            return $this->page("topm/shop/promotion/empty.html",$pagedata);
        }

        foreach($activity['data'] as $k=>$val)
        {
            if(isset($post['id']) && $val['activity_id'] == $post['id'])
            {
                $default = $val;
                unset($activity['data'][$k]);
            }
            elseif($val['mainpush'] || $val['mainpush'] == 1)
            {
                $default = $val;
                unset($activity['data'][$k]);
            }
            if($val['slide_images'])
            {
                $slideImg[$val['activity_id']] = $val['slide_images'];
            }
        }
        if($default)
        {
            array_unshift($activity['data'],$default);
        }
        $post['id'] = $activity['data'][0]['activity_id'];

        $pagedata = $this->__getPagedata($post);
        $pagedata['activity_list'] = $activity['data'];
        $pagedata['slide'] = $slideImg;
        $cativityData = app::get('topm')->rpcCall('promotion.activity.info',array('activity_id'=>$post['id'],'fields'=>'limit_cat'));
        $pagedata['catlist']  = $cativityData['limit_cat'];
        return $this->page("topm/shop/promotion/activity.html",$pagedata);
    }

    public function search()
    {
        $post = input::get();
        $pagedata = $this->__getPagedata($post);
        $cativityData = app::get('topm')->rpcCall('promotion.activity.info',array('activity_id'=>$post['id'],'fields'=>'limit_cat'));
        $pagedata['catlist']  = $cativityData['limit_cat'];
        return view::make('topm/shop/promotion/itemlist.html',$pagedata);
    }

    public function detail()
    {
        if( userAuth::check() )
        {
            $pagedata['nologin'] = 1;
        }
        $this->setLayoutFlag('product');
        $post = input::get();
        $params['fields'] = "*";
        $params['activity_id'] = $post['a'];
        $params['item_id'] = $post['g'];
        $groupItem = app::get('topm')->rpcCall('promotion.activity.item.info',$params);
        if($groupItem['activity_info']['release_time'] > time())
        {
            redirect::action('topm_ctl_item@index',array('item_id'=>$params['item_id']))->send();exit;
        }
        $pagedata['group_item'] = $groupItem;
        $pagedata['item'] = app::get('topm')->rpcCall('item.get',array('item_id'=>$params['item_id'],'fields'=>'item_count.sold_quantity,item_count.item_id'));
        $pagedata['shop'] = app::get('topm')->rpcCall('shop.get',array('shop_id'=>$pagedata['group_item']['shop_id'],'fields'=>'shop_name,shop_id'));
        $pagedata['now_time'] = time();
        $pagedata['shopDsrData'] = $this->__getShopDsr($pagedata['shop']['shop_id']);
        return $this->page("topm/shop/promotion/activity_detail.html",$pagedata);
    }

    private function __getShopDsr($shopId)
    {
        $params['shop_id'] = $shopId;
        $params['catDsrDiff'] = true;
        $dsrData = app::get('topm')->rpcCall('rate.dsr.get', $params);
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



    public function datalist()
    {
        $post = input::get();
        $pagedata = $this->__getPagedata($post);
        return view::make('topm/shop/promotion/list.html',$pagedata);
    }

    private function __getPagedata($post)
    {
        $pagedata['filter'] = $post;
        $page = $post['pages'] ? $post['pages'] : 1;
        $pageSize = $this->limit;
        $orderBy = $post['orderBy'];
        $params = array(
            'status' => 'agree',
            'page_no' => intval($page),
            'page_size' => $pageSize,
            'order_by' => $orderBy,
            'fields' => 'title,item_default_image,price,item_id,activity_id,sales_count,activity_price',
        );
        if($post['id'])
        {
            $params['activity_id'] = intval($post['id']);
        }

        if($post['cat_id'])
        {
            $params['cat_id'] = intval($post['cat_id']);
        }

        $pagedata['activity'] = app::get('topm')->rpcCall('promotion.activity.info',array('activity_id' => $params['activity_id'],'fields'=>'activity_id,activity_name,activity_tag,start_time,end_time,release_time'));;
        $item = app::get('topm')->rpcCall('promotion.activity.item.list',$params);
        $pagedata['group_item'] = $item['list'];
        $total = $item['count'];
        if( $total > 0 ) $totalPage = ceil($total/$this->limit);
        $pagedata['pagers'] = array(
            'link'=>url::action('topm_ctl_activity@datalist',$post),
            'current'=>$page,
            'total'=>$totalPage,
        );
        $pagedata['now_time'] = time();
        return $pagedata;
    }

    public function ajaxItemShow()
    {
        $post = input::get();
        $pagedata = $this->__getPagedata($post);
        $data['html'] = view::make('topm/shop/promotion/list.html',$pagedata)->render();
        $data['pagers'] = $pagedata['pagers'];
        $data['success'] = true;
        return response::json($data);exit;
    }

    public function getCatLv3()
    {
        $id = input::get('catid');
        $catLv3 = app::get('topm')->rpcCall('category.cat.get.info',array('cat_path'=>intval($id),'level'=>'3','fields'=>'cat_name,cat_id'));

        $params['activity_id'] = intval(input::get('id'));
        $params['fields'] ='cat_id';
        $item = app::get('topm')->rpcCall('promotion.activity.item.list',$params);
        $catIds = array_column($item['list'],'cat_id');
        foreach($catIds as $id)
        {
            if($catLv3[$id])
            {
                $cat[] = $catLv3[$id];
            }
        }
        return  response::json($cat);exit;
    }
}
