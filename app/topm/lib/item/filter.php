<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topm_item_filter {

    /*
    |--------------------------------------------------------------------------
    | 商品列表页渐进式筛选条件URL参数配置说明
    |--------------------------------------------------------------------------
    |
    | 1 保留参数：a_ ; d_ ; r_ 以这三个开头的作为保留参数
    |   说明：a_ 表示新增对应参数的参数值
    |         示例：参数 cat_id=1&brand_id=1,2&a_brand_id=4
    |         a_brand_id 因为用"a_"开头则表示将brand_id加入到以后brand_id参数中，则解析出来brand_id=>array(1,2,4)
    |
    |         d_ 表示删除对应参数的参数值，用法和a_类似，目前 "a_", "d_" 在参数中只能出现一次，
    |            "a_","d_", 删除和新增的不可以为数组：如，(d_brand_id=1,2,3 错误）（d_brand_id=1 正确)
    |
    |         r_ 表示替换对应参数的参数值,不存在替换参数直接赋值,用法和a_类似，替换主要用于筛选为单选的选项
    |
    |  2 参数全写和缩写说明：如果定义了全写和缩写的那么带html中生成URL地址的时候，参数一定需要用缩写的，后端不会在做合并
    |
    |  3 保留参数: operationtype=remove 应该只有对应的d_参数， operationtype=replace 应该只有对应的r_参数，operationtype=add 应该只有a_参数
    */

    //全写和缩写的对应关系
    public $map = [
        'brand_id'=>'b',
        'cat_id'=>'c',
        'price'=>'p',
        'search_keywords'=>'n',
        'bn'=>'bn',
        'orderBy'=>'sort',
        'shop_cat_id'=>'sc',
        'is_selfshop'=>'sf'
    ];

    private function __join($j)
    {
        $v = array();
        foreach((array)$j as $n)
        {
            $n = trim($n);
            if( $n !== '' ) $v[] = rawurlencode($n);
        }
        return count($v)>0?implode(',',$v):false;
    }

    /**
     * 将参数中以逗号隔开的值，转换为数组
     *
     * @param string $str 参数值
     *
     * @return array
     */
    private function __explode($str)
    {
        if( $str === null || $str === '' ) return false;

        $params = explode(',',rawurldecode($str) );

        if( count($params) == 1 ) return rawurldecode($str);

        foreach( $params as $val)
        {
            if( $val ) $value[] = $val;
        }
        return $value;
    }

    /**
     * 根据get提交的参数，获取到当前新增的参数
     *
     * @param array $params get传入的参数
     * @return array
     */
    private function __addParams($params)
    {
        //取出需要新增的条件
        foreach( $params as $col=>$value )
        {
            if( substr($col,0,2) != 'a_' ) continue;
            unset($params[$col]);

            $addCol = substr($col,2);
            if( !isset($params[$addCol]) )
            {
                $params[$addCol] = $value;
                //一次只能新增一个参数值
                break;
            }

            if( !is_array($params[$addCol]) )
            {
                $params[$addCol] = array($params[$addCol]);
            }

            //一次只能新增一个参数值，如果新增的参数值为数组则需要改造此处
            $params[$addCol][] = $value;
            break;
        }
        return $params;
    }

    /**
     * 根据get提交的参数，获取到当前需要删除的的参数值
     *
     * @param array $params get传入的参数
     * @return array
     */
    private function __delParams( $params )
    {
        //取出需要删除的条件
        foreach( $params as $col=>$value )
        {
            if( substr($col,0,2) != 'd_' ) continue;
            unset($params[$col]);

            $delCol = substr($col,2);
            if( !isset($params[$delCol]) ) continue;

            $params[$delCol] = array_flip($params[$delCol]);
            //如果是删除则表示直接删除一个值，如果要删除一个数组需要改造此处
            unset($params[$delCol][$value]);
            $params[$delCol] = array_flip($params[$delCol]);

            break;
        }
        return $params;
    }

    /**
     * 根据get提交的参数，获取到当前需要替换的的参数值
     *
     * @param array $params get传入的参数
     * @return array
     */
    private function __replaceParams($params)
    {
        //取出需要替换的条件
        foreach( $params as $col=>$value )
        {
            if( substr($col,0,2) != 'r_' ) continue;
            unset($params[$col]);

            $delCol = substr($col,2);
            $params[$delCol] = $value;

            break;
        }
        return $params;
    }

    public function encode($filter)
    {
        if( empty($filter) ) return '';

        $ret = array();
        foreach( $filter as $full=>$value )
        {
            $p = $this->map[$full] ? $this->map[$full] : $full;
            if( false !== ($val = $this->__join($value)) )
            {
                $ret[$p] = $val;
            }
        }
        return $ret;
    }

    public function decode( $params )
    {
        if( empty($params) ) return array();
        $filter = array();

        foreach( $params as $col=>$value )
        {
            $value = $this->__explode($value);
            $params[$col] = $value;
        }

        switch( $params['operationtype'] )
        {
            case 'remove':
                $params = $this->__delParams($params);
                break;
            case 'add':
                $params = $this->__addParams($params);
                break;
            case 'replace':
                $params = $this->__replaceParams($params);
                break;
        }
        unset($params['operationtype']);

        foreach($params as $key=>$value )
        {
            if( $value===null || $value==='' ) continue;

            //缩写转换为全写
            $realKey = array_flip($this->map)[$key];
            $k =  $realKey ? $realKey : $key;

            $filter[$k] = $value;
        }
        return $filter;
    }
}

