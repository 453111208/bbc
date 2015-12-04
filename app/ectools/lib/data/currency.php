<?php
class ectools_data_currency{

    /**
     * @brief 获取所有的货币符号及货币值
     *
     * @param string $isSign (all 获取所有 ；sign 获取指定货币的符号)
     *
     * @return
     */
    public function getCurrency($isSign="",$cur="")
    {
        $return = array();
        foreach (file(app::get('ectools')->app_dir.'/currency.txt') as $row)
        {
            list($code,$sign,$cnname,$enname) = explode("\t",trim($row));

            if ($isSign == "all")
            {
                $return[$code] = $sign.' '.$cnname;
            }
            elseif($isSign == "sign")
            {
                $return[$code] = $sign;
            }
            else
            {
                $name = explode('，',$cnname);
                $return[$code] = $name['1'];
            }
        }

        if($cur) return $return[$cur];
        return $return;
    }

    /**
     * @brief 处理页面金额的显示
     *
     * @param $money
     * @param $cur
     * @param $decimals
     * @param $operation_carryset
     *
     * @return
     */
    public function showMoney($money="",$cur="",$decimalDigit='', $decimalType='',$isCount=false)
    {
        if ($money === null)
        {
            $money = intval($money);
        }

        if(!$cur)
        {
            $cur = app::get('ectools')->getConf('system.currency.default');
        }

        //如果没有设置显示小数位数和取整方式就获取计算用的值
        if(!$decimalDigit)
        {
            $decimalDigit = app::get('ectools')->getConf('system.money.decimals');
        }

        if(!$decimalType)
        {
            $decimalType = app::get('ectools')->getConf('system.money.operation.carryset');
        }

        $objMath = kernel::single('ectools_math');

        //$money =  $objMath->number_multiple(array($money,1));
        $money = $objMath->formatNumber($money, $decimalDigit, $decimalType);
        $cur = $this->getCurrency('sign',$cur);
        if($isCount)
        {
            return $money;
        }
        return $cur.$money;
    }

}
