<?php
/**
* @brief 收藏
 */
class topc_ctl_collect extends topc_controller{

    /**
	* @brief 商品收藏添加
	*/
    function ajaxFav() {
        $userId = userAuth::id();
        if(!$userId)
        {
            $url = url::action('topc_ctl_passport@signin');
            return $this->splash('error',$url);
        }
        $params['item_id'] = $_POST['item_id'];
        $params['user_id'] = $userId;
        $params['objectType'] = $_POST['type'];

        if (!app::get('topc')->rpcCall('user.itemcollect.add', $params))
        {
            return $this->splash('error',null, app::get('topc')->_('该商品已经收藏！'));
        }
        else
        {
           return  $this->splash('success',null,app::get('topc')->_('商品收藏添加成功'));
        }
    }
     /**
    * @brief 商品收藏删除
    */

    function ajaxFavDel() {
        $userId = userAuth::id();
        if(!$userId)
        {
            $url = url::action('topc_ctl_passport@signin');
            return $this->splash('error',$url);
        }

        $params['item_id'] = $_POST['item_id'];
        $params['user_id'] = $userId;

        if(empty($params['item_id']))
        {
            return $this->splash('error',null, app::get('topc')->_('商品id不能为空！'));
        }

        if (!app::get('topc')->rpcCall('user.itemcollect.del', $params))
        {
            return $this->splash('error',null, app::get('topc')->_('商品收藏删除失败！'));
        }
        else
        {
           return  $this->splash('success',null,app::get('topc')->_('商品收藏删除成功'));
        }
    }

    /**
    * @brief 添加店铺收藏
     */

    function ajaxFavshop() {
        $userId = userAuth::id();
        if(!$userId)
        {
            $url = url::action('topc_ctl_passport@signin');
            return $this->splash('error',$url);
        }

        $params['shop_id'] = $_POST['shop_id'];
        $params['user_id'] = $userId;
        if (!app::get('topc')->rpcCall('user.shopcollect.add', $params))
        {
            return $this->splash('error',null, app::get('topc')->_('店铺已经收藏！'));
        }
        else
        {
           return  $this->splash('success',null,app::get('topc')->_('店铺收藏添加成功'));
        }
    }

     /**
    * @brief 删除店铺收藏
     */

    function ajaxFavshopDel()
    {
        $userId = userAuth::id();
        if(!$userId)
        {
            $url = url::action('topc_ctl_passport@signin');
            return $this->splash('error',$url);
        }
        $params['shop_id'] = $_POST['shop_id'];
        $params['user_id'] = $userId;
        if(!$params['shop_id'])
        {
            return $this->splash('error',null, app::get('topc')->_('店铺id不能为空！'));
        }
        if (!app::get('topc')->rpcCall('user.shopcollect.del', $params))
        {

            return $this->splash('error',null, app::get('topc')->_('店铺收藏删除失败！'));
        }
        else
        {
           return  $this->splash('success',null,app::get('topc')->_('店铺收藏删除成功'));
        }
    }
}
