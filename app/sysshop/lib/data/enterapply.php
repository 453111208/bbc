<?php
/**
 * @brief 入驻资料表增 改 查
 */
class sysshop_data_enterapply{
    /**
     * @brief 构造函数
     *
     * @return
     */
    public function __construct()
    {
        $this->objMdlEnterapply = app::get('sysshop')->model('enterapply');
    }

    /**
     * @brief 保存入驻资料
     *
     * @param $postdata
     *
     * @return bool true or false
     */
    public function savedata($postdata)
    {
        if($postdata['enterapply_id'])
        {
            $this->checkShopName($postdata['shop_name'],$postdata['enterapply_id']);
            $logs = $this->objMdlEnterapply->getRow('enterlog',array('enterapply_id'=>$postdata['enterapply_id']));
            if($logs['enterlog']){
                $log = unserialize($logs['enterlog']);
                $postdata['enterlog'] = array_merge($log,$postdata['enterlog']);
            }
        }
        $db = app::get('sysshop')->database();
        $db->beginTransaction();
        try
        {
            $result = $this->objMdlEnterapply->save($postdata);
            if(!$result)
            {
                throw new \LogicException('申请提交失败');
            }
            $db->commit();
            return true;
        }
        catch(\LogicException $e)
        {
            $db->rollback();
            throw new \LogicException($e->getMessage());
            return false;
        }
    }

    /**
     * @brief 检测店铺名称是否唯一
     *
     * @param $shop_name
     * @param $enterapplyId
     *
     * @return
     */
    public function checkShopName($shop_name,$enterapplyId)
    {
        if($shop_name)
        {
            $shop_name = $this->objMdlEnterapply->getRow('shop_name,enterapply_id',array('shop_name'=>$shop_name,'enterapply_id|noequal'=>$enterapplyId));
            if($shop_name['shop_name'])
            {
                $msg = "商店名称已经存在";
                throw new \LogicException($msg);
            }
        }
    }

    /**
     * @brief 获取当前登录用户已经提交未审核的申请信息
     *
     * @param $user_id
     *
     * @return array
     */
    public function getData($id,$type="seller_id")
    {
        $filter = array(
            $type=>$id,
        );
        $list =  $this->objMdlEnterapply->getRow('*',$filter);
        return $list;
    }

    /**
     * @brief 获取当前登录用户提交的申请信息的状态
     *
     * @param $user_id
     *
     * @return status
     */
    public function checkIfenter($seller_id)
    {
        $list =  $this->objMdlEnterapply->getRow('status,seller_id',array('seller_id'=>$seller_id));
        return $list;
    }

    /**
     * @brief 审批
     *
     * @param $params array 申请数据
     *
     * @return bool suss  fail
     */
    public function consentRepulse($params)
    {
        $objMdlEnterapply = app::get('sysshop')->model('enterapply');
        $enterapply = $objMdlEnterapply->getRow("*",array('enterapply_id'=>$params['enterapply_id']));
        $shop = unserialize($enterapply['shop']);

        if($params['status'] == 'successful')
        {
            if($params['shop'])
            {

                $filter = array(
                    'enterapply_id'=>$params['enterapply_id'],
                    'shop_type'=>$params['shop_type'],
                    'shop'=>$params['shop'],
                );
                unset($params['shop']);

                $check = $this->checkBrand($filter,$msg);
                if(!$check)
                {
                    throw new \LogicException($msg);
                    return false;
                }
            }
            $params['agree_time'] = time();
            $params['enterlog']=array(array(
                'plan' => '入驻审核通过/等待签约',
                'times' => time(),
                'hint' => '您的入驻申请已通过，客户人员会与您联系合同事宜',
            ));
        }
        else
        {
            unset($params['shop']);
            $params['refuse_time'] = time();
            $params['enterlog']=array(array(
                'plan' => '入驻审核未通过',
                'times' => time(),
                'hint' => '您的入驻申请审核未通过，原因如下：'.$params['reason'],
                'status' => 'failing',
            ));
        }
        $result = $this->savedata($params);
        return $result;
    }

    /**
     * @brief 检测品牌选择是否符合规则
     *
     * @param $param array post
     * @param $msg string 报错信息
     *
     * @return bool
     */
    public function checkBrand($param)
    {
        $objShopType = kernel::single('sysshop_data_shoptype');
        $shoptype = $objShopType->getShoptype('is_exclusive',array('shop_type'=>$param['shop_type']));
        if($shoptype['is_exclusive'] && $param['shop']['shop_brand'])
        {
            $filter = array(
                'shop_type'=>$param['shop_type'],
                'status|in' => array('successful','finish'),
            );
            if($param['enterapply_id']) $filter['enterapply_id|noequal']=$param['enterapply_id'];

            $enterapply = $this->objMdlEnterapply->getList('enterapply_id,shop,shop_type',$filter);
            foreach($enterapply as $value)
            {
                $shop = unserialize($value['shop']);
                if($shop['shop_brand'] == $param['shop']['shop_brand'])
                {
                    $msg = "该品牌旗舰店已被使用";
                    throw new \LogicException($msg);
                    return false;
                }
            }
        }

        if($param['shop_type'] != "cat" && $param['new_brand'])
        {
            $brand = app::get('sysshop')->rpcCall('category.brand.get.list',array('brand_name'=>$param['new_brand'],'fields'=>'brand_name,brand_id'));
            if($brand)
            {
                $msg = "新增的品牌已经存在";
                throw new \LogicException($msg);
                return false;
            }
        }
        return true;
    }

    public function enterapply_id($filter,&$msg)
    {
        $this->objMdlEnterapply;
        return true;
    }

    /**
     * @brief 检测输入项
     *
     * @param $string
     * @param $type (email mobile telephone)
     *
     * @return
     */
    private function __checkTyep($string,$type)
    {
        if($type=="email" && strpos($string,'@')) return true;

        if($type=="mobile" && preg_match("/^1[34578]{1}[0-9]{9}$/",$string)) return true;

        if($type=="telephone" && preg_match("/^([0-9]{3,4}-)?[0-9]{7,8}$/",$string)) return true;

        $isIDCard2 = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/";
        if($type=="identity" && preg_match($isIDCard2,$string)) return true;

        return false;
    }
}


