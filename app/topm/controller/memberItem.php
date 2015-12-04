<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topm_ctl_memberItem extends topm_controller {

    public function userNotifyItem()
    {
        try
        {
            $postdata = $this->__checkdata(input::get());
            $params['shop_id'] = $postdata['shop_id'];
            $params['item_id'] = $postdata['item_id'];
            $params['sku_id'] = $postdata['sku_id'];
            $params['email'] = $postdata['email'];
            app::get('topm')->rpcCall('user.notifyitem',$params);
        }
        catch (Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        $msg = app::get('topm')->_('预订成功');
        return $this->splash('success',null,$msg);
    }

    private function __checkdata($data)
    {
        $validator = validator::make(
            ['shop_id' => $data['shop_id'] , 'item_id' => $data['item_id'],'sku_id' => $data['sku_id'],'email' => $data['email']],
            ['shop_id' => 'required'       , 'item_id' => 'required',     'sku_id' => 'required', 'email' => 'required|email'],
            ['shop_id' => '店铺id不能为空！' , 'item_id' => '商品id不能为空！','sku_id' => '货品id不能为空！','email' => '邮件不能为空！|邮件格式不正确!']
        );
        if ($validator->fails())
        {
            $messages = $validator->messagesInfo();
            foreach( $messages as $error )
            {
                throw new LogicException( $error[0] );
            }
        }
        return $data;
    }
}