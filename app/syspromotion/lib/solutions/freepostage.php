<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author  lujunyi@shopex.cn
 */
class syspromotion_solutions_freepostage implements syspromotion_interface_promotions{

    public function apply($params)
    {
        $freepostageInfo = app::get('syspromotion')->model('freepostage')->getRow('*', array('freepostage_id'=>$params['freepostage_id']));
        if(!$freepostageInfo || $freepostageInfo['freepostage_status'] != 'agree')
        {
            return false;
            // throw new \LogicException('不能使用此促销!');
        }
        $now = time();
        if( $now <= $freepostageInfo['start_time'] )
        {
            return false;
            // throw new \LogicException('尚未开始!');
        }
        if( $now >= $freepostageInfo['end_time'] )
        {
            return false;
            // throw new \LogicException('已经结束!');
        }

        $valid_grade = explode(',', $freepostageInfo['valid_grade']);
        $gradeInfo = app::get('syspromotion')->rpcCall('user.grade.basicinfo');
        if( !in_array($gradeInfo['grade_id'], $valid_grade) )
        {
            return false;
            // throw new \LogicException('您的当前会员等级不可参加此促销!');
        }

        // 免邮的规则检验
        if($freepostageInfo['condition_type'] == 'money')
        {
            if( $freepostageInfo['limit_money'] <= $params['forPromotionTotalPrice'] )
            {
                return true;
            }
        }
        if($freepostageInfo['condition_type'] == 'quantity')
        {
            if( $freepostageInfo['limit_quantity'] <= $params['forPromotionTotalQuantity'] )
            {
                return true;
            }
        }

        return false;
    }

}
