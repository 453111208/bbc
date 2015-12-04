<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author  lujunyi@shopex.cn
 */
class syspromotion_solutions_fulldiscount implements syspromotion_interface_promotions{

    public function apply($params)
    {
        $fulldiscountInfo = app::get('syspromotion')->model('fulldiscount')->getRow('*', array('fulldiscount_id'=>$params['fulldiscount_id']));
        if(!$fulldiscountInfo || $fulldiscountInfo['fulldiscount_status'] != 'agree')
        {
            return 0;
            // throw new \LogicException('不能使用此促销!');
        }
        $now = time();
        if( $now<=$fulldiscountInfo['start_time'] )
        {
            return 0;
            // throw new \LogicException('尚未开始!');
        }
        if( $now>=$fulldiscountInfo['end_time'] )
        {
            return 0;
            // throw new \LogicException('已经结束!');
        }
        $applyNum = app::get('syspromotion')->rpcCall('trade.promotion.applynum', array('promotion_id'=>$params['promotion_id']));
 
        if( $applyNum>=$fulldiscountInfo['join_limit'] )
        {
            return 0;
            // throw new \LogicException('可参与的满折次数已经用完!');
        }
        $valid_grade = explode(',', $fulldiscountInfo['valid_grade']);
        $gradeInfo = app::get('syspromotion')->rpcCall('user.grade.basicinfo');
        if( !in_array($gradeInfo['grade_id'], $valid_grade) )
        {
            return 0;
            // throw new \LogicException('您的当前会员等级不可参加此促销!');
        }
        // 满折金额的规则检验
        $rule = explode(',', $fulldiscountInfo['condition_value']);
        $ruleArray = array();
        foreach($rule as $k => $v)
        {
            $tmpFulldiscountValue = explode('|', $v);
            $ruleArray['full'][$k] = $tmpFulldiscountValue['0'];
            $ruleArray['discount'][$k] = $tmpFulldiscountValue['1'];
        }
        $ruleLength = count($ruleArray['full']);

        if( $params['forPromotionTotalPrice'] >=$ruleArray['full'][$ruleLength-1] )
        {
            $rulePercent = max(0, $ruleArray['discount'][$ruleLength-1]);
            $rulePercent = min($rulePercent, 100);
            $discount_price = ecmath::number_multiple(
                array(
                    $params['forPromotionTotalPrice'], (1-$rulePercent/100)
                )
            );
        }
        elseif( $params['forPromotionTotalPrice'] < $ruleArray['full']['0'] )
        {
            $discount_price = 0;
        }
        else
        {
            for($i=0; $i<$ruleLength-1; $i++)
            {
                if( $params['forPromotionTotalPrice']>=$ruleArray['full'][$i] && $params['forPromotionTotalPrice']<$ruleArray['full'][$i+1] )
                {
                    $rulePercent = max(0, $ruleArray['discount'][$i]);
                    $rulePercent = min($rulePercent, 100);
                    $discount_price = ecmath::number_multiple(
                        array(
                            $params['forPromotionTotalPrice'], (1-$rulePercent/100)
                        )
                    );
                    break;
                }
            }
        }
        if($discount_price<0)
        {
            $discount_price = 0;
        }
        return $discount_price;
    }

}
