<?php
class topc_ctl_activity extends topc_controller{

    public $limit = 40;
    public function index()
    {
        $post = input::get();
        $params = array(
            'release_time' => "sthan",
            'end_time' => "bthan",
            'order_by' => 'mainpush desc',
            'fields' => 'activity_name,activity_id,mainpush,slide_images',
        );
        $activity = app::get('topc')->rpcCall('promotion.activity.list',$params);
        if(!$activity['data'])
        {
            return $this->page("topc/promotion/empty.html",$pagedata);
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
        $pagedata['catlist'] = $this->__getCatLv1Lv3($pagedata['group_item'],$post['id']);
        return $this->page("topc/promotion/activity.html",$pagedata);
    }

    public function search()
    {
        $post = input::get();
        $pagedata = $this->__getPagedata($post);
        $pagedata['catlist'] = $this->__getCatLv1Lv3($pagedata['group_item'],$post['id']);
        return view::make('topc/promotion/itemlist.html',$pagedata);
    }

    public function itemlist()
    {
        $post = input::get();
        if(isset($post['lv3']) && $post['lv3'])
        {
            $post['cat_id'] = intval($post['lv3']);
            unset($post['lv3']);
        }
        elseif(isset($post['lv1']) && $post['lv1'])
        {
            $post['cat_id'] = intval($post['lv3']);
            unset($post['lv1']);
        }
        $pagedata = $this->__getPagedata($post);
        return view::make('topc/promotion/list.html',$pagedata);
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
        $groupItem = app::get('topc')->rpcCall('promotion.activity.item.info',$params);
        if($groupItem['activity_info']['release_time'] > time())
        {
            redirect::action('topc_ctl_item@index',array('item_id'=>$params['item_id']))->send();exit;
        }
        $pagedata['group_item'] = $groupItem;
        $pagedata['activity'] = $pagedata['group_item']['activity_info'];
        $pagedata['item'] = app::get('topc')->rpcCall('item.get',array('item_id'=>$params['item_id'],'fields'=>'item_id,item_count.sold_quantity,item_count.item_id,item_desc.pc_desc'));
        $pagedata['shop'] = app::get('topc')->rpcCall('shop.get',array('shop_id'=>$pagedata['group_item']['shop_id'],'fields'=>'shop_name,shop_id'));
        $pagedata['shopDsrData'] = $this->__getShopDsr($pagedata['shop']['shop_id']);
        $pagedata['now_time'] = time();
        //echo '<pre>';print_r($pagedata);exit();
        return $this->page("topc/promotion/activity_detail.html",$pagedata);
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
            'fields' => 'title,item_default_image,price,item_id,activity_id,sales_count,activity_price,cat_id',
        );
        if($post['id'])
        {
            $params['activity_id'] = $post['id'];
        }

        if($post['cat_id'])
        {
            if(is_array($post['cat_id']))
            {
                $params['cat_id'] = implode(',',$post['cat_id']);
            }
            else
            {
                $params['cat_id'] = $post['cat_id'];
            }
        }
        $item = app::get('topc')->rpcCall('promotion.activity.item.list',$params);
        $pagedata['group_item'] = $item['list'];
        $pagedata['activity'] = app::get('topc')->rpcCall('promotion.activity.info',array('activity_id' => $params['activity_id'],'fields'=>'activity_id,activity_name,activity_tag,start_time,end_time,release_time'));;
        $pagedata['total'] = $item['count'];
        if( $pagedata['total'] > 0 ) $totalPage = ceil($pagedata['total']/$this->limit);
        $post['pages'] = time();
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_activity@index',$post),
            'current'=>$page,
            'total'=>$totalPage,
            'token'=>time(),
        );
        $pagedata['now_time'] = time();
        return $pagedata;
    }

    private function __getCatLv1Lv3($activityItem,$id)
    {
        $activityData = app::get('topc')->rpcCall('promotion.activity.info',array('activity_id'=>intval($id),'fields'=>'limit_cat'));
        $lv1List = $activityData['limit_cat'];

        $catIds = implode(',',array_column($activityItem,'cat_id'));
        if($catIds)
        {
            $catLv3 = app::get('topc')->rpcCall('category.cat.get.info',array('cat_id'=>$catIds,'level'=>'3','fields'=>'cat_path,cat_name,cat_id'));
        }
        foreach($lv1List as $id=>$name)
        {
            $cat[$id]['cat_id']=  $id;
            $cat[$id]['cat_name']=  $name;
            if($catLv3)
            {
                foreach($catLv3 as $k=>$val)
                {
                    $catPath = explode(',',$val['cat_path']);
                    if($id == $catPath[1])
                    {
                        $cat[$id]['lv3'][] = $val;
                    }
                }
            }
        }
        return $cat;
    }
}
