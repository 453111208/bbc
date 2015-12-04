<?php

/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class syscategory_mdl_tariff extends dbeav_model{


    /**
     * @brief   获取列表信息
     *
     * @return array 
     */
    public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType='order_sort ASC, cat_id DESC')
    {
        //获取类目信息
        $catModel = app::get('syscategory')->model('cat');
        $filter['level'] = 1;
        $cols = 'cat_id,parent_id,cat_name,guarantee_money,platform_fee,cat_service_rates';
        $catLists = $catModel->getList($cols,$filter,$offset,$limit,$orderType);

        $key=0;
        foreach($catLists as $catkey=>$value)
        {
            $filter['level'] = 2;
            $filter['parent_id'] = $value['cat_id'];
            $catL1 = $this->_getCat($cols,$filter);
            $allcountThreecat = $this->_getCount($value['cat_id']);
            $_allcountThreecat = ($allcountThreecat > 0) ? $allcountThreecat : 1;
            $row[0]=array(
                'MergeDown'=>$_allcountThreecat,
                'value'=>$value['cat_name'],
                'Index'=>0,
            ); 
            $row[4]=array(
                'MergeDown'=>$_allcountThreecat,
                'value'=>$value['platform_fee'],
                'Index'=>4,
            );
            $row[5]=array(
                'MergeDown'=>$_allcountThreecat,
                'value'=>$value['guarantee_money'],
                'Index'=>5,
            );

            foreach($catL1 as $catk=>$val)
            {
                $filter['level'] = 3;
                $filter['parent_id'] = $val['cat_id'];
                $catL2 = $this->_getCat($cols,$filter);
                $countThreecat = count($catL2);
                $row[1]=array(
                    'MergeDown'=>($countThreecat!=0) ? $countThreecat : 1,
                    'value'=>$val['cat_name'],
                    'Index'=>1,
                );
                if($allcountThreecat > 0 && $catk > 0)
                {
                    unset($row[0],$row[4],$row[5]);
                }
                
                foreach($catL2 as $k=>$v)
                {
                    if($allcountThreecat > 0 && $k > 0)
                    {
                        unset($row[0],$row[4],$row[5]);
                    }
                    if($countThreecat > 0 && $k != 0 )
                    {
                        unset($row[1]);
                    }
                    $key = $key+1;
                    $row[2]=array(
                        'value'=>$v['cat_name'],
                        'Index'=>2,
                    );
                    $row[3] = array(
                        'value'=>$v['cat_service_rates']."%",
                        'Index'=>3,
                    );
                    ksort($row);
                    $rows[$key] = $row;
                }
            }

        }
        return $rows;
    }

    /**
     * @brief 定义资费表结构
     *
     * @return array 
     */
    public function get_schema()
    {
        $schema = array(
            'columns'=>array(
                'cat_level_1'=>array(
                    //'type'=>'varchar(100)',
                    'type' => 'string',
                    'length' => 100,
                    'label' => app::get('syscategory')->_( '一级类目'),
                ),
                'cat_level_2'=>array(
                    //'type'=>'varchar(100)',
                    'type' => 'string',
                    'length' => 100,
                    'label' => app::get('syscategory')->_( '二级类目'),
                ),
                'cat_level_3'=>array(
                    //'type'=>'varchar(100)',
                    'type' => 'string',
                    'length' => 100,
                    'label' => app::get('syscategory')->_( '三级类目'),
                ),
                'cat_service_rates'=>array(
                    //'type'=>'varchar(100)',
                    'type' => 'string',
                    'length' => 100,
                    'label' => app::get('syscategory')->_( '类目费率 '),
                ),
                'platform_fee'=>array(
                    'type'=>'money',
                    'label' => app::get('syscategory')->_( '平台使用费:单位-元/年'),
                ),
                'guarantee_money'=>array(
                    //'type'=>'varchar(100)',
                    'type' => 'string',
                    'length' => 100,
                    'label' => app::get('syscategory')->_( '保证金:单位-元'),
                ),
            ),
        );
        return $schema;
    }

    /**
     * @brief 查询cat信息
     *
     * @return array 
     */
    public function _getCat($cols,$filter){
        $catModel = app::get('syscategory')->model('cat');
        $catLists = $catModel->getList($cols,$filter);
        return $catLists;
    }

    public function _getCount($oneCatid){
        $filter = array(
            'level'=>3,
            'cat_path|head'=>','.$oneCatid.',',
        );
        $catModel = app::get('syscategory')->model('cat');
        $catLists = $catModel->count($filter);
        return $catLists;

    }
}
