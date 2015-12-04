<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class systrade_data_trade_delivery
{
    // 私有化实例，单件模式使用.
    private static $instance;

    /**
     * 私有构造方法，不能直接实例化，只能通过调用getInstance静态方法被构造
     * @params null
     * @return null
     */
    /*
    private function __construct($model)
    {
        // 异常处理
        if (is_null($model) || !$model)
            {
                trigger_error(app::get('systrade')->_("应用对象不能为空！"), E_USER_ERROR);
            }
        $this->model = $model;
    }
     */

    /**
     * 类静态构造实例的唯一入口
     * @params object app object
     * @params object model object
     * @return object systrade_data_trade_delivery
     */
    public static function getInstance($model)
    {
        if (is_object(self::$instance))
        {
            return self::$instance;
        }

        self::$instance = new systrade_data_trade_delivery($model);

        return self::$instance;
    }

    /**
     * 最终的克隆方法，禁止克隆本类实例，克隆是抛出异常。
     * @params null
     * @return null
     */
    final public function __clone()
    {
        trigger_error(app::get('systrade')->_("此类对象不能被克隆！"), E_USER_ERROR);
    }

    private function gen_id()
    {
        $sign = '1'.date("Ymd");
        $microtime = microtime(true);
        mt_srand($microtime);
        $randval = substr(mt_rand(), 0, -3) . rand(100, 999);
        return $sign.$randval;
    }

    /**
     * 创建发货单
     * @params array - 订单数据
     * @params obj - 应用对象
     * @params string - 支付单生成的记录
     * @return boolean - 创建成功与否
     */
    public function generate($sdf, &$msg='')
    {
        $objMdlOrder = app::get('systrade')->model('order');
        $orders = $objMdlOrder->getList('oid,status,aftersales_status',array('tid'=>$sdf['tid']));
        foreach($orders as $key=>$order)
        {
            if($order['aftersales_status'] && in_array($order['aftersales_status'],['SUCCESS']))
            {
                $oids[] = $order['oid'];
            }
            elseif($order['aftersales_status'] && !in_array($order['aftersales_status'],['CLOSED','SELLER_REFUSE_BUYER']))
            {
                throw new \LogicException("订单中有部分未处理的退款，请至客户服务中处理之后发货");
            }
        }

        // 得到delivery的一些信息
        $sdf['delivery_id'] = $this->gen_id();
        $sdf['seller_id'] = $sdf['seller_id'];
        $sdf['op_name'] = $sdf['seller_account'];
        $objLibTrade = kernel::single('systrade_data_trade');
        $tradeInfo = $objLibTrade->getTradeInfo('*',array('tid'=>$sdf['tid']));

        // 处理返货单据信息，得到订单的发送量。
        $objMdlTrade = app::get('systrade')->model('trade');
        $objMdlDElivery = app::get('syslogistics')->model('delivery');
        $orderItems = $tradeInfo['order'];

        if ($sdf['template_id'])
        {
            $dlytmplInfo = app::get('systrade')->rpcCall('logistics.dlytmpl.get',array('template_id'=>$sdf['template_id'],'fields'=>'corp_id'));
            $corpInfo = app::get('systrade')->rpcCall('logistics.dlycorp.get',array('corp_id'=>$dlytmplInfo['corp_id']));
        }

        $delivery = array(
            'post_fee'          => $tradeInfo['post_fee'],
            'is_protect'        => 0,//$sdf['is_protect'],
            'delivery_id'       => $sdf['delivery_id'],
            'dlytmpl_id'        => $sdf['template_id'],
            'logi_id'           => $corpInfo['corp_id'],
            'logi_name'         => $corpInfo['corp_name'],
            'corp_code'         => $corpInfo['corp_code'],
            'logi_no'           => $sdf['logi_no'],
            'receiver_name'     => $tradeInfo['receiver_name'],
            'receiver_state'    => $tradeInfo['receiver_state'],
            'receiver_city'     => $tradeInfo['receiver_city'],
            'receiver_district' => $tradeInfo['receiver_district'],
            'receiver_address'  => $tradeInfo['receiver_address'],
            'receiver_zip'      => $tradeInfo['receiver_zip'],
            'receiver_mobile'   => $tradeInfo['receiver_mobile'],
            'receiver_phone'    => $tradeInfo['receiver_phone'],
            'memo'              => $sdf['memo'],
        );
        // 发货基础表信息
        $delivery['tid'] = $sdf['tid'];
        $delivery['user_id'] = $tradeInfo['user_id'];
        $delivery['seller_id'] = $sdf['seller_id'];
        $delivery['shop_id'] = $tradeInfo['shop_id'];
        $delivery['t_begin'] = time();
        $delivery['op_name'] = $sdf['op_name'];
        $delivery['type'] = 'delivery';
        $delivery['status'] = 'progress';
        $deliveryId = $delivery['delivery_id'];

        $db = app::get('systrade')->database();
        $db->beginTransaction();

        try
        {
            if ($orderItems)
            {
                //实体商品
                $arrItems = array();

                $iLoop = 0;
                foreach ($orderItems as $dinfo)
                {
                    if($oids && in_array($dinfo['oid'],$oids))
                    {
                        continue;
                    }
                    $item = array(
                        'oid' => $dinfo['oid'],
                        'tid' => $sdf['tid'],
                        'delivery_id' => $delivery['delivery_id'],
                        'item_type' => 'item',
                        'item_id' => $dinfo['item_id'],
                        'sku_id' => $dinfo['sku_id'],
                        'sku_bn' => $dinfo['bn'],
                        'sku_title' => $dinfo['title'],
                        'number' => $dinfo['num'],
                    );
                    $arrItems[] = array(
                        'number' => $dinfo['num'],
                        'sku_title' => $dinfo['title'],
                    );
                    $this->toInsertItem($item);
                    $iLoop++;
                }

                if($iLoop > 0)
                {
                    $deliveryId = $delivery['delivery_id'];
                    $isSave = $objMdlDElivery->insert($delivery);
                    if (!$isSave)
                    {
                        throw new \LogicException(app::get('systrade')->_('发货单生成失败！'));
                    }

                    $arrDelivery['status'] = 'succ';
                    $delivery['status'] = 'succ';
                    $delivery['delivery_id'] = $deliveryId;
                    $isSave = $objMdlDElivery->update($arrDelivery,array('delivery_id'=>$deliveryId));
                    if (!$isSave)
                    {
                        throw new \LogicException(app::get('systrade')->_('发货单修改失败！'));
                    }
                }
                else
                {
                    throw new \LogicException("没有可以发货的商品,商品已经申请退款");
                }
            }
            $modifieTime = time();
            $aUpdate['tid'] = $sdf['tid'];
            $aUpdate['status'] = 'WAIT_BUYER_CONFIRM_GOODS';
            $aUpdate['consign_time'] = $modifieTime;

            if(!$objMdlTrade->save($aUpdate))
            {
                throw new \LogicException(app::get('systrade')->_('发货后修改主订单状态失败！'));
            }

            if ($aUpdate['status'] == 'WAIT_BUYER_CONFIRM_GOODS')
            {
                $logInfo = '发货操作,物流公司：'.$corpInfo['corp_name'].'，物流单号：'.$delivery['logi_no'];
                // 更新发货日志结果
                $sdfTradeLog = array(
                    'rel_id'   => $sdf['tid'],
                    'op_id'    => $sdf['seller_id'],
                    'op_name'  => $sdf['seller_account'] ? $sdf['seller_account'] : '未知',
                    'op_role'  => 'seller',
                    'behavior' => 'delivery',
                    'log_text' => $logInfo,
                );
                if(!$this->addLog($sdfTradeLog))
                {
                    throw new \LogicException(app::get('systrade')->_('发货日志记录失败！'));
                }
            }

            $db->commit();

        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        return true;
    }
    /**
     * 记录订单确认收货日志
     * @param array &$tradeData 订单数据
     * @return bool
     */
    private function addLog(&$sdfTradeLog)
    {
        $objLibLog = kernel::single('systrade_data_trade_log');

        if(!$objLibLog->addLog($sdfTradeLog))
        {
            return false;
        }

        return true;
    }

    /**
     * 修改各个item的相关信息
     * @params array 修改的data
     * @return boolean 成功与否的
     */
    private function toInsertItem(&$data)
    {
        $objMdlOrder = app::get('systrade')->model('order');
        $o = app::get('syslogistics')->model('delivery_detail');

        if ($o->save($data))
        {
            // 更新发货量
            $isUpdateStore = false;
            $tmp = $objMdlOrder->getRow('*',array('oid'=>$data['oid']));
            $objMath = kernel::single('ectools_math');
            $updateData['sendnum'] = $objMath->number_plus(array($tmp['sendnum'], $data['number']));

            if ($tmp['num'] < $updateData['sendnum']){
                $isUpdateStore = false;
            }
            else
            {
                $isUpdateStore = true;
            }

            $modifieTime = time();
            $updateData['oid'] = $tmp['oid'];
            $updateData['consign_time'] = $modifieTime;
            if ($isUpdateStore && $objMdlOrder->save($updateData))
            {
                return true;
            }
            else
            {
                return false;
            }

        }
        return false;
    }
}
