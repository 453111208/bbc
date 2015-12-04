<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class sysstat_data_filter {
    /**
     * 商品搜索的搜索条件解析
     *
     * @param string $encodeFilter URL传入的条件
     *
     * @return array
     */
    public function filter( $params )
    {
        //处理分页查询相关数据
        $limit = 2;
        $filter['limit'] = $limit;
        if($params['pages'])
        {
            $filter['start'] = $limit * (intval($params['pages'])-1);
            $filter['limit'] = $limit;
            unset($params['pages']);
        }

        if( $params['orderBy'] )
        {
            $filter['orderBy'] = $params['orderBy'];
            unset($params['orderBy']);
        }

        foreach($params as $key=>$value)
        {
            $filter['filter'][$key] = $value;
        }

        $filter['rows'] = '*';

        return $filter;
    }
}

