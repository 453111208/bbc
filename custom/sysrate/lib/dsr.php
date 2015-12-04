<?php

class sysrate_dsr {

    public function __construct()
    {
        $this->objMdlDsr = app::get('sysrate')->model('dsr');
    }

    public function updateDsr($shopId, $catId, $plusData)
    {
        $dsrData = $this->objMdlDsr->getRow('*', ['shop_id'=>$shopId]);
        if( $dsrData )
        {
            $dsrData = $this->__preDsrData($dsrData, $plusData);
            $return = $dsrData;
            $result = $this->objMdlDsr->update($dsrData, ['shop_id'=>$shopId]);
        }
        else
        {
            $dsrData = $this->__preDsrData($dsrData, $plusData);
            $return = $dsrData;//店铺评分在insert之后返回的为字符串，因此在此处先赋值
            $dsrData['shop_id'] = $shopId;
            $dsrData['cat_id'] = $catId;
            $result = $this->objMdlDsr->insert($dsrData);
        }

        return $return;
    }

    private function __preDsrData($dsrData, $plusData)
    {
        $dsrColumn = ['tally_dsr','attitude_dsr','delivery_speed_dsr'];
        foreach( $dsrColumn as $col )
        {
            //i 对应的评分的分值
            for( $i=1; $i<=5; $i++ )
            {
                $plus = $plusData[$col][$i] ? $plusData[$col][$i] : 0;
                $dsrData[$col][$i] = $dsrData[$col][$i] + $plus;
            }
        }

        return $dsrData;
    }

    /**
     * 获取店铺DSR信息
     *
     * @param int $shopId 店铺ID
     * @param bool $percentage 是否需要返回行业平均分同
     * @param bool $countNum 是否需要返回每个动态评分的数量
     */
    public function getShopDsr($shopId, $percentage, $countNum)
    {
        $fields = 'shop_id,cat_id,tally_dsr,attitude_dsr,delivery_speed_dsr';

        $dsrData = $this->objMdlDsr->getRow($fields,array('shop_id'=>$shopId));
        if( empty($dsrData) ) return array();

        //如果需要返回行业百分比数据
        if( $percentage == 'true' )
        {
            $returnData['tally_dsr'] = $this->getDsrNumber($dsrData['tally_dsr']);
            $returnData['attitude_dsr'] = $this->getDsrNumber($dsrData['attitude_dsr']);
            $returnData['delivery_speed_dsr'] = $this->getDsrNumber($dsrData['delivery_speed_dsr']);
            $returnData['catDsrDiff'] = $this->gitShopDsrToCat($dsrData['cat_id'], $returnData);
        }

        if( $countNum )
        {
            $returnData['countNum']['tally_dsr'] = $dsrData['tally_dsr'];
            $returnData['countNum']['attitude_dsr'] = $dsrData['attitude_dsr'];
            $returnData['countNum']['delivery_speed_dsr'] = $dsrData['delivery_speed_dsr'];
        }

        return $returnData;
    }

    /**
     * 计算DSR的值
     */
    public function getDsrNumber($data)
    {
        foreach( $data as $score=>$number )
        {
            $totalScore += $score*$number;
            $totalNumber += $number;
        }

        $num = sprintf('%.4f',$totalScore/$totalNumber);

        return $num;
    }

    /**
     * 计算当前店铺的同行业百分比 todo 计算精度修改
     *
     * @param int $catId 类目ID，店铺绑定的相同类目ID,表示同一行业
     * @param array $shopDsr 店铺当前的DSR数据
     */
    public function gitShopDsrToCat($catId, $shopDsr)
    {
        $catDsrData = $this->getCatDsr($catId);
        #echo '<pre>';
        #echo '我是行业最大值<br>';
        #print_r($catDsrData['max']);
        #echo '我是行业最小值<br>';
        #print_r($catDsrData['min']);

        $maxCatDsr = $catDsrData['max'];
        $minCatDsr = $catDsrData['min'];

        foreach( $shopDsr as $key=>$row )
        {
            //行业动态平均分数
            $avgval = $this->getDsrNumber($catDsrData['count'][$key]);
            #echo '<hr>';
            #echo $key.'的行业平均值<h1>'.$avgval.'</h1> <br>';

            //店铺动态评分和同行业平均分数相等，则持平
            if( $shopDsr[$key] == $avgval )
            {
                $data[$key]['trend'] = 'flat';
                $data[$key]['percentage'] = '持平';
            }

            //店铺动态评分大于行业动态平均分
            if( $shopDsr[$key] > $avgval )
            {
                /*
                 * 商家的得分是 4.71234 分，同行业平均分是 4.61234 分，同行业最高分是4.91234 分，
                 * 计算方法是：
                 * (4.71234-4.61234)/( 4.91234-4.61234)=33.33%，
                 * 结论是：商家的“描述相 符”比同行业平均水平高 33.33%。 *
                 **/
                $data[$key]['trend'] = 'go-up';
                $data[$key]['percentage'] = (bcdiv(bcsub($shopDsr[$key],$avgval,4),bcsub($maxCatDsr[$key],$avgval,4),4) * 100).'%';
            }

            //店铺动态评分小于行业动态平均分
            if( $shopDsr[$key] <  $avgval )
            {

                /*
                    商家的得分是 4.61234 分，同行业平均分是 4.71234 分，同行业最低分是4.41234 分，
                    计算方法是：
                    (4.71234-4.61234)/( 4.71234-4.41234)=33.33%，
                    结论是：商家的“宝贝与描述相符”比同行业平均水平低 33.33%
                 */
                $data[$key]['trend'] = 'go-down';
                $percentage = bcdiv(bcsub($avgval,$shopDsr[$key],4), bcsub($avgval,$minCatDsr[$key],4),4);
                $data[$key]['percentage'] = (abs($percentage) * 100).'%';
            }
        }

        return $data;
    }

    /**
     * 获取指定行业的DSR最高分和最低分
     *
     * @param int $catId 类目ID，店铺绑定的相同类目ID,表示同一行业
     */
    public function getCatDsr($catId)
    {
        $key = 'shopDsr_'.$catId;
        base_kvstore::instance('sysrate_dsr')->fetch($key, $value);
        return $value;
    }

    /**
     * 设置指定行业的DSR最高分和最低分
     *
     * @param int $catId 类目ID，店铺绑定的相同类目ID,表示同一行业
     * @param array $catDsr 当前类目的DSR数据
     */
    public function setCatDsr($catId, $catDsr)
    {
        return base_kvstore::instance('sysrate_dsr')->store('shopDsr_'.$catId, $catDsr);
    }

}
