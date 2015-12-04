<?php
class sysrate_data_consultation{

    /**
     * @brief 消费者咨询信息新增
     *
     * @param $params
     *
     * @return
     */
    public function createConsultation($params)
    {
        $item = app::get('topc')->rpcCall('item.get',array('item_id'=>$params['item_id'],'fields'=>'title,image_default_id,shop_id,bn'));
        if(!$item)
        {
            throw new \LogicException('您咨询的商品不存在');
        }
        $params['item_title'] = $item['title'];
        $params['bn'] = $item['bn'];
        $params['image_default_id'] = $item['image_default_id'];
        $params['shop_id'] = $item['shop_id'];

        $shop = app::get('topc')->rpcCall('shop.get',array('shop_id'=>$params['shop_id'],'fields'=>'shop_name'));
        if(!$shop)
        {
            throw new \LogicException('您咨询的商品信息有误,商品所在店铺不存在');
        }

        $params['shop_name'] = $shop['shop_name'];

        $objMdlConsultation = app::get('sysrate')->model('consultation');
        $result = $objMdlConsultation->save($params);
        if(!$result)
        {
            throw new \LogicException('保存咨询失败');
        }
        return $result;
    }
    /**
     * @brief 商家回复消费者的咨询信息添加
     *
     * @param $params
     *
     * @return
     */
    public function doReply($params)
    {
        $objMdlConsultation = app::get('sysrate')->model('consultation');
        if($params['id'])
        {
            $data = $objMdlConsultation->getRow('item_title,shop_name,consultation_type,item_id',array('consultation_id' => $params['id']));
        }
        $data['be_reply_id'] = $params['id'];
        $data['content'] = $params['content'];
        $data['author_id'] = $params['author_id'];
        $data['author'] = $params['author'];
        $data['shop_id'] = $params['shop_id'];

        $db = app::get('sysaftersales')->database();
        $db->beginTransaction();
        try
        {
            //保存回复内容
            $result = $objMdlConsultation->save($data);
            if(!$result)
            {
                throw new Exception('回复内容保存失败');
            }
            //修改咨询为已回复
            $result = $objMdlConsultation->update(array('is_reply' => true),array('consultation_id' => $params['id']));
            if(!$result)
            {
                throw new Exception('更新咨询恢复状态失败');
            }
            $db->commit();
        }
        catch(Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return $result;
    }

    /**
     * @brief  更新咨询或回复是否显示与前端
     *
     * @param $params
     *
     * @return
     */
    public function doDisplay($id,$status)
    {
        $objMdlConsultation = app::get('sysrate')->model('consultation');
        if($status == "true")
        {
            $data['is_display'] = "false";
        }
        elseif($status == "false")
        {
            $data['is_display'] = "true";
        }
        $data['consultation_id'] = $id;
        $result = $objMdlConsultation->save($data);
        if(!$result && $status == "true")
        {
            throw new LogicException('关闭显示失败');
        }
        elseif(!$result && $status == "false")
        {
            throw new LogicException('开启显示失败');
        }
        return $result;
    }


    /**
     * @brief 商家删除咨询
     *
     * @param $param
     *
     * @return
     */
    public function doDelete($id,$type="consultation")
    {
        if($type == "reply")
        {
            $objMdlConsultation = app::get('sysrate')->model('consultation');
            $data = $objMdlConsultation->getRow('be_reply_id',array('consultation_id'=>$id));
            $db = app::get('sysaftersales')->database();
            $db->beginTransaction();

            try
            {
                $result = $objMdlConsultation->update(array('is_reply'=>0),array('consultation_id'=>$data['be_reply_id']));
                if(!$result)
                {
                    throw new Exception('更新咨询回复状态失败');
                }

                $result = $objMdlConsultation->delete(array('consultation_id' => $id));
                if(!$result)
                {
                    throw new Exception('删除回复失败');
                }
                $db->commit();
            }
            catch(Exception $e)
            {
                $db->rollback();
                throw $e;
            }
        }
        elseif($type == "consultation")
        {
            $qb = app::get('sysrate')->database()->createQueryBuilder();
            if(is_array($id))
            {
                array_walk($id, function(&$value) use ($qb) {
                    $value = $qb->getConnection()->quote($value);
                });
            }
            $qb->delete(app::get('sysrate')->database()->quoteIdentifier('sysrate_consultation'))
                ->where(
                    $qb->expr()->orX(
                        $qb->expr()->in('consultation_id', $id),
                        $qb->expr()->in('be_reply_id', $id)
                    ));

            $result = $qb->execute() ? true : false;
        }
        if(!$result)
        {
            throw new LogicException('删除咨询失败');
        }
        return $result;
    }

    /**
     * @brief 获取咨询列表
     *
     * @param $row
     * @param $filter
     *
     * @return
     */
    public function getConsultation($row,$filter,$offset=0, $limit=200, $orderBy=null)
    {
        if($filter['reply'])
        {
            $replyFilter = $filter['reply'];
            unset($filter['reply']);
        }
        $objMdlConsultation = app::get('sysrate')->model('consultation');
        $consultation = $objMdlConsultation->getList($row,$filter,$offset, $limit, $orderBy);
        if(!$consultation) return $consultation;

        $consultation = array_bind_key($consultation,'consultation_id');
        $ids = array_column($consultation,'consultation_id');
        $replyFilter['be_reply_id'] = $ids;

        $replyList = $objMdlConsultation->getList($row,$replyFilter);
        if($replyList)
        {
            foreach($replyList as $key=>$val)
            {
                //多条回复使用
                //$reply[$val['be_reply_id']][] = $val;
                $reply[$val['be_reply_id']] = $val;
            }

            foreach($consultation as $k=>$val)
            {
                if(isset($val['is_anonymity']) && $val['is_anonymity'])
                {
                    $consultation[$k]['is_anonymity'] = 1;
                }
                $consultation[$k]['reply'] = $reply[$k];
            }
        }
        return $consultation;
    }
}


