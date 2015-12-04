<?php

class sysrate_appeal {


    public function __construct()
    {
        $this->objMdlAppeal = app::get('sysrate')->model('appeal');
    }

    /**
     * 商品对评论进行申诉
     *
     * @param array $data 申诉的参数
     *
     * @return bool
     */
    public function add($data,$shopId)
    {
        $this->__checkAppealData($data,$shopId);

        if( $data['is_again'] )
        {
            $updateRateData['appeal_again'] = 1;
            $flag = $this->__againAppeal($data);
        }
        else
        {
            $appealData = $this->objMdlAppeal->getRow('rate_id',['rate_id'=>$data['rate_id']]);
            if( $appealData )
            {
                throw new \LogicException(app::get('sysrate')->_('评价已经申诉'));
            }
            $insertData['rate_id'] = $data['rate_id'];
            $insertData['content'] = trim($data['content']) ? utils::_RemoveXSS(trim($data['content'])) : trim($data['content']);
            $insertData['evidence_pic'] = $data['evidence_pic'];
            $insertData['appeal_type'] = $data['appeal_type'] == 'APPLY_UPDATE' ? 'APPLY_UPDATE' : 'APPLY_DELETE';
            $insertData['appeal_time'] = time();
            $insertData['modified_time'] = time();

            $flag = $this->objMdlAppeal->insert($insertData);
        }

        if( !$flag )  return false;

        //更新评论表是否需要申诉的判断为不需求申诉
        $updateRateData['is_appeal'] = 0;//1 为可以申诉，0为不可以申诉
        $updateRateData['appeal_status'] = 'WAIT';//评价表中存储申诉状态用户筛选
        $updateRateData['appeal_time'] = time();//评价表中存储申诉时间用户筛选
        $updateRateData['modified_time'] = time();
        $result = app::get('sysrate')->model('traderate')->update($updateRateData,['rate_id'=>$data['rate_id']]);

        return $result ? true : false;
    }

    /**
     * 对首次驳回的申诉进行再次申诉
     */
    private function __againAppeal($data)
    {

        $appealData = $this->objMdlAppeal->getRow('appeal_id,rate_id,content,evidence_pic,appeal_time,reject_reason',['rate_id'=>$data['rate_id']]);
        if( empty($appealData) )
        {
            throw new \LogicException(app::get('sysrate')->_('不可以再次申诉，请先进行第一次申诉'));
        }

        if( empty($data['evidence_pic']) )
        {
            throw new \LogicException(app::get('sysrate')->_('申诉图片凭证必填'));
        }

        $updateData['content'] = trim($data['content']) ? utils::_RemoveXSS(trim($data['content'])) : trim($data['content']);
        $updateData['evidence_pic'] = $data['evidence_pic'];
        $updateData['status'] = 'WAIT';
        $updateData['reject_reason'] = '';
        $updateData['appeal_again'] = 1;
        $updateData['appeal_log'] = ['content'=>$appealData['content'],'evidence_pic'=>$appealData['evidence_pic'],'appeal_time'=>$appealData['appeal_time'],'reject_reason'=>$appealData['reject_reason'] ];
        $updateData['appeal_time'] = time();
        $updateData['modified_time'] = time();

        $flag = $this->objMdlAppeal->update($updateData, ['appeal_id'=>$appealData['appeal_id']]);
        if( !$flag )  return false;

        return true;
    }

    /**
     * 平台审核商家申诉
     *
     * @param int $appeal 申诉ID
     * @param array $data
     */
    public function check($appealId, $data)
    {
        $appealData = $this->objMdlAppeal->getRow('appeal_id,rate_id,status,appeal_again,appeal_type',array('appeal_id'=>$appealId));
        if( empty($appealData) )
        {
            throw new \LogicException(app::get('sysrate')->_('审核的申诉不存在'));
        }

        if( $appealData['status'] != 'WAIT' )
        {
            throw new \LogicException(app::get('sysrate')->_('申诉已审核'));
        }

        if( $data['result'] == 'true' )//申诉通过
        {
            $flag = $this->__AppealSuccessAfter($appealData['rate_id'], $appealId, $appealData['appeal_type']);
        }
        else
        {
            $flag = $this->__AppealRejectAfter($appealData['rate_id'], $appealId, $data['reject_reason'], $appealData['appeal_again']);
        }

        return $flag;
    }

    /**
     * 申诉成功的后续处理，如果申诉为修改则评价锁定打开，申诉为删除的则删除评论
     *
     * @param int    $rateId     评论ID
     * @param int    $appealId   申诉ID
     * @param string $appealType 申诉类型
     */
    private function __AppealSuccessAfter($rateId, $appealId, $appealType)
    {
        $updateData['status'] = 'SUCCESS';
        $updateData['modified_time'] = time();

        $db = app::get('sysrate')->database();
        $db->beginTransaction();

        $flag = $this->objMdlAppeal->update($updateData, ['appeal_id'=>$appealId]);
        if( !$flag )
        {
            $db->rollback();
            return false;
        }

        if( $appealType == 'APPLY_DELETE' )
        {
            $rateData = app::get('sysrate')->model('traderate')->getRow('rate_id,result,item_id',array('rate_id'=>$rateId));
            if( !$rateData )
            {
                $db->rollback();
                return false;
            }

            if( $rateData['result'] == 'good' )
            {
                $filter['rate_good_count'] = -1;
            }
            elseif( $rateData['result'] == 'bad' )
            {
                $filter['rate_bad_count'] = -1;
            }
            else
            {
                $filter['rate_neutral_count'] = -1;
            }
            $filter['item_id'] = $rateData['item_id'];
            if( !app::get('sysrate')->rpcCall('item.updateRateQuantity', $filter) )
            {
                $db->rollback();
            }
            $updateRateData['disabled'] = 1;
        }
        else
        {
            $updateRateData['is_lock'] = 0;//打开锁定
        }
        $updateRateData['is_appeal'] = 0;//不可以申诉
        $updateRateData['appeal_status'] = 'SUCCESS';
        $updateRateData['modified_time'] = time();
        $result = app::get('sysrate')->model('traderate')->update($updateRateData,['rate_id'=>$rateId]);
        if( !$result )
        {
            $db->rollback();
            return false;
        }

        $db->commit();
        return true;
    }

    /**
     * 审核申诉拒绝处理
     *
     * @param int    $rateId     评论ID
     * @param int    $appealId   申诉ID
     * @param string $reason     申诉理由
     * @param bool   $isAgain    是否可以再次申诉
     */
    private function __AppealRejectAfter($rateId, $appealId, $reason, $isAgain)
    {
        if( !$isAgain )//不是再次(第二轮)申诉，首次申诉
        {
            $updateData['status'] = 'REJECT';

            $updateRateData['is_appeal'] = 1;//1 为可以申诉，0为不可以申诉
            $updateRateData['appeal_status'] = 'REJECT';
        }
        else//再次申诉驳回
        {
            $updateData['status'] = 'CLOSE';

            $updateRateData['is_appeal'] = 0;
            $updateRateData['appeal_status'] = 'CLOSE';
        }

        $updateData['reject_reason'] = $reason;
        $updateRateData['modified_time'] = time();
        $result = app::get('sysrate')->model('traderate')->update($updateRateData,['rate_id'=>$rateId]);
        if( !$result )  return false;

        $updateData['modified_time'] = time();
        $flag = $this->objMdlAppeal->update($updateData, ['appeal_id'=>$appealId]);
        return $flag ? true : false;
    }

    /**
     * 检查申诉传入的数据是否合法
     */
    private function __checkAppealData($data,$shopId)
    {
        $rateData = app::get('sysrate')->model('traderate')->getRow('rate_id,is_appeal,shop_id',array('rate_id'=>$data['rate_id']));
        if( empty($rateData) )
        {
            throw new \LogicException(app::get('sysrate')->_('要申诉的评论不存在'));
        }

        if( $rateData['shop_id'] != $shopId)
        {
            throw new \LogicException(app::get('sysrate')->_('无操作权限,可能已退出登录，请重新登录'));
        }

        if( $rateData['is_appeal'] == 0 )
        {
            throw new \LogicException(app::get('sysrate')->_('该评论不能申诉'));
        }

        if( empty($data) || mb_strlen(trim($data['content']),'utf8') > 300 || mb_strlen(trim($data['content']),'utf8') < 5 )
        {
            throw new \LogicException(app::get('sysrate')->_('请填写5-300个字的内容'));
        }

        $evidencePic = explode(',',$data['evidence_pic']);
        if( $evidencePic && count($evidencePic) > 5 )
        {
            throw new \LogicException(app::get('sysrate')->_('申诉最多上传5张图片'));
        }

        return true;
    }

    public function getAppealList()
    {
        $data['appeal'] = array();
        $countTotal = app::get('sysrate')->model('appeal')->count($filter);
        if( $countTotal )
        {
            $pageTotal = ceil($countTotal/$params['page_size']);
            $page =  $params['page_no'] ? $params['page_no'] : 1;
            $limit = $params['page_size'] ? $params['page_size'] : 10;
            $currentPage = $pageTotal < $page ? $totalPage : $page;
            $offset = ($currentPage-1) * $limit;

            $orderBy = $params['orderBy'] ? $params['orderBy'] : 'modified_time desc';
            $data['appeal'] = app::get('sysrate')->model('appeal')->getList($params['fields'], $filter, $offset, $limit, $orderBy);
        }
        $data['total_results'] = $countTotal;
    }

}

