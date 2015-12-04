<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class getOrderInfo extends PHPUnit_Framework_TestCase {

    public function testGet()
    {
        try
        {
            //测试联通erp后订单发货

            $item = array(
                ['oid' =>'1508131649450014', 'sku_id' =>'708', 'num' =>'1', 'title' =>'mac笔记本', 'bn' =>'S55CC4EEF20604',],
                ['oid' =>'1508131649440014', 'sku_id' =>'707', 'num' =>'1', 'title' =>'mac笔记本', 'bn' =>'S55CC4EEF205F3',],
            );

            $item = json_encode($item);
            $params = array(
                //基础信息
                //配送方式及物流信息
                //子订单信息(发货的商品信息)
                'delivery_id'  =>'1201508131755845838',
                'tid'       =>'1508131649430014',
                'seller_id' =>'1',
                'user_id'   =>'14',
                'shop_id'   =>'1',
                'post_fee'  =>'0.00',
                'template_name' =>'天天快递',
                'logi_no'    =>'312321321323',
                'corp_code'  =>'HHTT',
                'items'       =>$item,
                'memo'          =>'testerp发货',
            );
            error_log(print_r($params,1),3,DATA_DIR."/aaaa.log");
            $result = app::get('systrade')->rpcCall('logistics.trade.delivery', $params);
            var_dump($result);
            exit;
            //获取订单列表
            $params = array(
                'create_time_start' => '2015-07-01',
                'create_time_end' => '2015-07-05',
                'fields' =>'*,order.*',
            );
            $result = app::get('systrade')->rpcCall('trade.get.list', $params);
            error_log(var_export($result,1),3,DATA_DIR."/trade.get.list.log");

            //获取订单详情
            $params['tid'] = "1507201657020001";
            $params['fields'] = "*,orders.*";
            $result = app::get('systrade')->rpcCall('trade.get', $params);
            error_log(var_export($result,1),3,DATA_DIR."/trade.get.log");

            //发货单添加
            $deliveryData = array(
                'tid' => '1507061738340006',
                'oids' => '1507061738350006,1507061738360006',
                'seller_id' => '1',
                'shop_id' => '1',
                'op_name' => "shopex01",
            );
            $result = app::get('systrade')->rpcCall('delivery.create', $deliveryData);
            error_log(var_export($result,1),3,DATA_DIR."/delivery.create.log");

            //发货单更新
            $deliveryData = array(
                'delivery_id' => "120150721785861835",
                'template_id' => '4',
                'logi_no' => '880244261926850874',
                'tid' => '1507201657020001',
                'post_fee' => "5",
            );
            $result = app::get('systrade')->rpcCall('delivery.update', $deliveryData);
            error_log(var_export($result,1),3,DATA_DIR."/delivery.update.log");

            //获取售后列表
            //aftersales.list.get
            $params = array(
                'user_id' =>'6',
                'fields' => "*",
            );
            $result = app::get('systrade')->rpcCall('aftersales.list.get', $params,'buyer');
            error_log(var_export($result,1),3,DATA_DIR."/aftersales.list.get.log");

            //获取售后单详情
            //aftersales.get.bn
            //aftersales.get
            $params = array(
                'aftersales_bn' =>'1507271136030106',
                'user_id' =>'6',
                'fields' => "*",
            );
            $result = app::get('systrade')->rpcCall('aftersales.get', $params,'buyer');
            error_log(var_export($result,1),3,DATA_DIR."/aftersales.get.log");

            $params = array(
                'oid' =>'1507201649430006',
                'fields' => "*",
            );
            $result = app::get('systrade')->rpcCall('aftersales.get.bn', $params,'buyer');
            error_log(var_export($result,1),3,DATA_DIR."/aftersales.get.bn.log");

        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
        catch (\LogicException $e)
        {
            // 逻辑处理
            echo $e->getMessage();
        }
        catch (\RunException $e)
        {
            // 运行时错误处理
            echo $e->getMessage();
        }
        exit;
    }
}
