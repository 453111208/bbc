<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysrate_tasks_finishAppeal extends base_task_abstract implements base_interface_task {

    public function __construct()
    {
        $this->objMdlAppeal = app::get('sysrate')->model('appeal');
    }

    //申诉审核如果平台没有在10个中昨日内处理，那么则进行自动驳回
    public function exec($params=null)
    {
        //获取10天之内申诉未审核的数据
        $filter['modified_time|sthan'] = strtotime('-10 days');
        $filter['status'] = 'WAIT';

        $data = $this->objMdlAppeal->getList('appeal_id,rate_id,appeal_again', $filter);
        if( empty($data) ) return true;

        foreach( $data as $row )
        {
            if( $row['appeal_again'] )
            {
                //为再次申诉
                $againData['rate_id'] = $row['rate_id'];
                $againData['appeal_id'] = $row['appeal_id'];
            }
            else
            {
                //首次申诉
                $firstData['rate_id'] = $row['rate_id'];
                $firstData['appeal_id'] = $row['appeal_id'];
            }
        }

        //再次申诉
        $this->__AppealRejectAfter($againData['rate_id'], $againData['appeal_id'], true);

        //首次申诉
        $this->__AppealRejectAfter($firstData['rate_id'], $firstData['appeal_id'], false);

        return true;
    }

    /**
     * 审核申诉拒绝处理
     *
     * @param int    $rateId     评论ID
     * @param int    $appealId   申诉ID
     * @param bool   $isAgain    是否可以再次申诉
     */
    private function __AppealRejectAfter($rateId, $appealId, $isAgain)
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

        $updateRateData['modified_time'] = time();
        $result = app::get('sysrate')->model('traderate')->update($updateRateData,['rate_id'=>$rateId]);
        if( !$result )  return false;

        $updateData['modified_time'] = time();
        $updateData['reject_reason'] = '系统自动驳回';
        $flag = $this->objMdlAppeal->update($updateData, ['appeal_id'=>$appealId]);
        return $flag ? true : false;
    }
}

