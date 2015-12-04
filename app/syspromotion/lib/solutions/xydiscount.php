<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author  lujunyi@shopex.cn
 */
class syspromotion_solutions_xydiscount implements syspromotion_interface_promotions{

    public function apply($params)
    {
        $xydiscountInfo = app::get('syspromotion')->model('xydiscount')->getRow('*', array('xydiscount_id'=>$params['xydiscount_id']));
        if(!$xydiscountInfo || $xydiscountInfo['xydiscount_status'] != 'agree')
        {
            return 0;
            // throw new \LogicException('不能使用此促销!');
        }
        $now = time();
        if( $now<=$xydiscountInfo['start_time'] )
        {
            return 0;
            // throw new \LogicException('尚未开始!');
        }
        if( $now>=$xydiscountInfo['end_time'] )
        {
            return 0;
            // throw new \LogicException('已经结束!');
        }
        $applyNum = app::get('syspromotion')->rpcCall('trade.promotion.applynum', array('promotion_id'=>$params['promotion_id']), 'buyer');
 
        if( $applyNum>=$xydiscountInfo['join_limit'] )
        {
            return 0;
            // throw new \LogicException('可参与的X件Y折次数已经用完!');
        }
        $valid_grade = explode(',', $xydiscountInfo['valid_grade']);
        $gradeInfo = app::get('syspromotion')->rpcCall('user.grade.basicinfo');
        if( !in_array($gradeInfo['grade_id'], $valid_grade) )
        {
            return 0;
            // throw new \LogicException('您的当前会员等级不可参加此促销!');
        }
        // X件Y折金额的规则检验
        $rule = explode(',', $xydiscountInfo['condition_value']);
        $ruleArray = array();
        foreach($rule as $k => $v)
        {
            $tmpXyDiscountValue = explode('|', $v);
            $ruleArray['limit_number'][$k] = $tmpXyDiscountValue['0'];
            $ruleArray['discount'][$k] = $tmpXyDiscountValue['1'];
        }
        $ruleLength = count($ruleArray['limit_number']);
        //echo '<pre>';print_r($ruleArray);exit;
        if( $params['forPromotionTotalQuantity'] >= $ruleArray['limit_number'][$ruleLength-1] )
        {
            $rulePercent = $ruleArray['discount'][$ruleLength-1];
            $discount_price = ecmath::number_multiple(
                array(
                    $params['forPromotionTotalPrice'], (1-$rulePercent/100)
                )
            );
        }
        elseif( $params['forPromotionTotalQuantity'] < $ruleArray['limit_number']['0'] )
        {
            $discount_price = 0;
        }
        else
        {
            for($i=0; $i<$ruleLength-1; $i++)
            {
                if( $params['forPromotionTotalQuantity']>=$ruleArray['limit_number'][$i] && $params['forPromotionTotalQuantity']<$ruleArray['limit_number'][$i+1] )
                {
                    $rulePercent = $ruleArray['discount'][$i];
                    $discount_price = ecmath::number_multiple(
                        array(
                            $params['forPromotionTotalPrice'], (1-$rulePercent/100)
                        )
                    );
                    break;
                }
            }
        }
        // X件Y折金额的规则检验
        /*if( $params['forPromotionTotalQuantity'] >= $xydiscountInfo['limit_number'] )
        {
            $rulePercent = max(0, $xydiscountInfo['discount']);
            $rulePercent = min($rulePercent, 100);
            $discount_price = ecmath::number_multiple(
                array(
                    $params['forPromotionTotalPrice'], (1-$rulePercent/100)
                )
            );
        }*/

        if($discount_price<0)
        {
            $discount_price = 0;
        }

        return $discount_price;
    }

}
