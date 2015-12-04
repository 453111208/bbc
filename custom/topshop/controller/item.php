<?php

/**
 * @brief 企业商品管理
 */
class topshop_ctl_item extends topshop_controller {

    public $limit = 10;

    public function add()
    {

        //$pagedata['return_to_url'] = request::server('HTTP_REFERER');
        $pagedata['shopCatList'] = app::get('topshop')->rpcCall('shop.cat.get',array('shop_id'=>$this->shopId,'fields'=>'cat_id,cat_name,is_leaf,parent_id,level'));
        
        $pagedata['shopId'] = $this->shopId;
        $pagedata["type"]=$_GET["type"];
        $this->contentHeaderTitle = app::get('topshop')->_('添加商品');
        return $this->page('topshop/item/edit.html', $pagedata);
    }
    #增加商品 begin By :Litong
    public function addItem()
    {
        $postdata = $_POST["item"];

        $item_prop_id = $_POST["item_prop_id"];
        $item_prop_name = $_POST["item_prop_name"];
        $cat_id = $_POST["cat_id"];
        $cat_name = $_POST["cat_name"];
        $item_prop_value = $_POST["item_prop_value"];

        $postdata["shop_id"]= $this->shopId;
        unset($postdata['sku']);
        // $postdata['item_numberro'] = $_POST["item_numberro"];
        $imageString = "";
        foreach ($_POST["listimages"] as $key => $value) {
           $imageString=$imageString.$value.",";
             # code...
         } 
         $postdata["list_image"] =$imageString;
        $postdata["image_default_id"] = $_POST["listimages"][0];
       // unset($postdata['spec']);
        $prop_id =$postdata["nature_props"]["id"];
        $prop_name=$postdata["nature_props"]["name"];
        $prop_value=$postdata["nature_props"]["value"];
        unset($postdata["nature_props"]);
        foreach ($prop_id as $key => $value) {
            $propdata["item_prop_value"] = array(
                'item_prop_name'=>$prop_name[$key],
                'item_prop_id' =>$prop_id[$key],
                'cat_id'=>$postdata["cat_id"],
                );
            # code...
        }
        $postdata['state']=false;
        $postdata['otherstate'] = false;
        $postdata['modified_time'] = time();

        $cat=app::get("syscategory")->model("cat")->getRow("*",array("cat_id"=>$cat_id));
        if($cat["is_bz"]=="1"){
            $postdata["type"]="0";
        }else if($cat["is_bz"]=="2"){
            $postdata["type"]="1";
        }
try{
      $itemMdl = app::get("sysitem")->model("item");
        $itemMdl->save($postdata);
        $itemid=$postdata["item_id"];

         foreach ($item_prop_id as $key => $value) {
            $propDatas = array(
                'item_prop_id'=>$item_prop_id[$key],
                'item_prop_value'=>$item_prop_value[$key],
                'cat_id'=>$cat_id[$key],
                'item_prop_name'=>$item_prop_name[$key],
                'cat_name'=>$cat_name[$key],
                'item_id'=>$itemid,
                'user_id'=>$_POST["shop_id"],
                 );
        $propMdl = app::get("syscategory")->model("item_prop_value");
        $propMdl->save($propDatas);   
    }

         //$url = url::action('topshop_ctl_item@itemList');
        $msg = app::get('topshop')->_('保存成功');
        return $this->splash('success', "/index.php/shop/item/add.html?type=1", $msg, true);
    } catch (Exception $e)
        {
            return $this->splash('error', '', $e->getMessage(), true);
        }
      


    }

    public function updateItem()
    {
        $a=$_POST;
        //$sql="UPDATE sysitem_item set is_shop_show=".$_POST['center'].",type=".$_POST['bz'].",product_type=".$_POST['fl']." where item_id=".$_POST['itemid'].";";
        $itemid=$_POST["itemid"];
        $iteminfo=app::get("sysitem")->model("item")->getRow("*",array("item_id"=>$itemid));
        $iteminfo["is_shop_show"]=$_POST['center'];
        $iteminfo["type"]=$_POST['bz'];
        // $iteminfo["product_type"]=$_POST['fl'];
        try{
           // var_dump($iteminfo);
             app::get('sysitem')->model("item")->save($iteminfo);
            $ajaxdata['isok']="success";
        }catch(Exception $e){
            $ajaxdata['isok']="error";
        }
       return response::json($ajaxdata);
        
    }
    #增加商品 End
    public function edit()
    {
        //$pagedata['return_to_url'] = request::server('HTTP_REFERER');
        $itemId = input::get('item_id');
        $pagedata['shopId'] = $this->shopId;

        // 企业关联的商品品牌列表
        // 商品详细信息
        $params['item_id'] = $itemId;
        $params['shop_id'] = $this->shopId;
        $params['fields'] = "*";
        $pagedata['item'] = app::get('topshop')->rpcCall('item.get',$params);

        // 企业分类及此商品关联的分类标示selected
      
        $pagedata["type"]="1";
        $this->contentHeaderTitle = app::get('topshop')->_('添加商品');
        return $this->page('topshop/item/edit.html', $pagedata);
    }

    public function itemList()
    {
        
        $pagedata['image_default_id'] = app::get('image')->getConf('image.set');
        $pagedata["type"]=1;
        $status = input::get('status',false);
        $pages =  input::get('pages',1);
        $pagedata['status'] = $status;
        //企业ID变成会员ID
        $shop_id= $this->shopId;
        $shopList = app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shop_id));
        $seller_id = $shopList["seller_id"];
       $filter = array(
            'shop_id' => $shop_id,
            'page_no' =>$pages,
            'page_size' => $this->limit,
        );
        $shopCatId = input::get('shop_cat_id',false);
        if( $shopCatId )
        {
            $filter['shop_cat_id'] = $shopCatId;
        }

        $filter['fields'] = 'item_id,list_time,modified_time,title,image_default_id,state,otherstate,is_shop_show,type';
        $itemsList = app::get('topshop')->rpcCall('item.search',$filter);

        $pagedata['item_list'] = $itemsList['list'];
        $pagedata['total'] = $itemsList['total_found'];


        $totalPage = ceil($itemsList['total_found']/$this->limit);
        $pagersFilter['pages'] = time();
        $pagersFilter['status'] = $status;
        $pagers = array(
            'link'=>url::action('topshop_ctl_item@itemList',$pagersFilter),
            'current'=>$pages,
            'use_app' => 'topshop',
            'total'=>$totalPage,
            'token'=>time(),
        );
        $pagedata['pagers'] = $pagers;

        $this->contentHeaderTitle = app::get('topshop')->_('商品列表');
        return $this->page('topshop/item/list.html', $pagedata);
    }
    //商品搜所
    public function searchItem()
    {
        $filter = input::get();
        if($filter['min_price']&&$filter['max_price'])
        {
            if($filter['min_price']>$filter['max_price'])
            {
                $msg = app::get('topshop')->_('最大值不能小于最小值！');
                return $this->splash('error', null, $msg, true);
            }
        }
        $pages =  input::get('pages',1);
        $params = array(
            'shop_id' => $this->shopId,
            'search_keywords' => $filter['item_title'],
            'use_platform' => $filter['use_platform'],
            'min_price' => $filter['min_price'],
            'max_price' => $filter['max_price'],
            'page_no' =>$pages,
            'page_size' => $this->limit,
        );
        $params['fields'] = 'item_id,list_time,modified_time,title,image_default_id,state,otherstate,is_shop_show,type,product_type';
        $itemsList = app::get('topshop')->rpcCall('item.search',$params);

        $pagedata['item_list'] = $itemsList['list'];
        $pagedata['total'] = $itemsList['total_found'];


        $totalPage = ceil($itemsList['total_found']/$this->limit);
        $pagersFilter['pages'] = time();
        $pagers = array(
            'link'=>url::action('topshop_ctl_item@itemList',$pagersFilter),
            'current'=>$pages,
            'use_app' => 'topshop',
            'total'=>$totalPage,
            'token'=>time(),
        );
        $pagedata['pagers'] = $pagers;
        $pagedata["type"]=1;
        $this->contentHeaderTitle = app::get('topshop')->_('商品列表');
        return $this->page('topshop/item/list.html', $pagedata);

    }
    public function storeItem()
    {
        $postData = input::get();
        $postData['item']['shop_id'] = $this->shopId;
        $postData['item']['cat_id'] = $postData['cat_id'];
        $postData['item']['approve_status'] = 'instock';
        $postData['item']['shop_cat_id'] = implode(',', $postData['item']['shop_cids']);
         //判断企业是不是自营企业 gongjiapeng
        $selfShopType = app::get('topshop')->rpcCall('shop.get',array('shop_id'=>$this->shopId));
        if($selfShopType['shop_type']=='self')
        {
            $postData['item']['is_selfshop'] = 1;
        }
        try
        {
            $postData = $this->_checkPost($postData);
            $result = app::get('topshop')->rpcCall('item.create',$postData);
            //$url = $postData['return_to_url'];
            $url = url::action('topshop_ctl_item@itemList');
            $msg = app::get('topshop')->_('保存成功');
            return $this->splash('success', $url, $msg, true);
        }
        catch (Exception $e)
        {
            return $this->splash('error', '', $e->getMessage(), true);
        }
    }

    private function _checkPost($postData)
    {
        if(!$postData['item']['mkt_price'])
        {
            $postData['item']['mkt_price'] = 0;
        }
        if(!$postData['item']['cost_price'])
        {
            $postData['item']['cost_price'] = 0;
        }
        if(!$postData['item']['weight'])
        {
            $postData['item']['weight'] = 0;
        }
        if(!$postData['item']['order_sort'])
        {
            $postData['item']['order_sort'] = 1;
        }
        /*
        if(mb_strlen($postData['item']['title'],'UTF8') > 30)
        {
            throw new Exception('商品名称至多30个字符');
        }
         */
        return $postData;
    }


    public function setItemStatus(){

        $postData = input::get();
        try
        {
            if(!$itemId = $postData['item_id'])
            {
                $msg = app::get('topshop')->_('商品id不能为空');
                return $this->splash('error',null,$msg,true);
            }

            if($postData['type'] == 'tosale')
            {
                $shopdata = app::get('topshop')->rpcCall('shop.get',array('shop_id'=>$this->shopId),'seller');
                if( empty($shopdata) || $shopdata['status'] == "dead" )
                {
                    $msg = app::get('topshop')->_('抱歉，您的企业处于关闭状态，不能发布(上架)商品');
                    return $this->splash('error',null,$msg,true);
                }
                $status = 'onsale';
                $msg = app::get('topshop')->_('上架成功');
            }
            elseif($postData['type'] == 'tostock')
            {
                $status = 'instock';
                $msg = app::get('topshop')->_('下架成功');
            }
            else
            {
                return $this->splash('error',null,'非法操作!', true);
            }

            $params['item_id'] = intval($itemId);
            $params['shop_id'] = intval($this->shopId);
            $params['approve_status'] = $status;
            app::get('topshop')->rpcCall('item.sale.status',$params);
            $queue_params['item_id'] = intval($itemId);
            $queue_params['shop_id'] = intval($this->shopId);
            //发送到货通知的邮件
            if($status == "onsale")
            {
                system_queue::instance()->publish('sysitem_tasks_userItemNotify', 'sysitem_tasks_userItemNotify', $queue_params);
            }
            $url = url::action('topshop_ctl_item@itemList');
            return $this->splash('success', $url, $msg, true);
        }
        catch(Exception $e)
        {
            return $this->splash('error',null,$e->getMessage(), true);
        }
    }

    public function deleteItem()
    {
        $postData = input::get();
        try
        {
            if(!$itemId = $postData['item_id'])
            {
                $msg = app::get('topshop')->_('商品id不能为空');
                return $this->splash('error',null,$msg, true);
            }
            app::get('topshop')->rpcCall('item.delete',array('item_id'=>intval($itemId),'shop_id'=>intval($this->shopId)));
        }
        catch(Exception $e)
        {
            return $this->splash('error',null, $e->getMessage(), true);
        }
        return $this->splash('success',null,'删除成功', true);
    }

    public function ajaxGetBrand($cat_id)
    {
        $params['shop_id'] = $this->shopId;
        $params['cat_id'] = input::get('cat_id');
        $brand = app::get('topshop')->rpcCall('category.get.cat.rel.brand',$params);

        if(!$brand)
        {
            return $this->splash('error',null, '当前类目不支持产品发布,请联系平台开启此权限', true);
        }
        return response::json($brand);exit;
    }
}


