<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class syscategory_mdl_props extends dbeav_model{

    var $has_many = array(
        'prop_value' => 'prop_values:contrast'
    );

    public $defaultOrder = array('order_sort',' asc',',prop_id',' DESC');
    /**
     * 构造方法
     * @param object model相应app的对象
     * @return null
     */
    public function __construct($app){
        parent::__construct($app);
        $this->propValuesModel = app::get('syscategory')->model('prop_values');
    }

    /**
     * @brief 根据属性ID，获取对应的属性数据，包含属性值
     *
     * @param int $propId 属性ID
     *
     * @return array 如果存在则返回属性和属性值数据，不存在返回空数组
     */
    public function getPropRow($propId)
    {
        $propInfo = $this->getRow('*',array('prop_id'=>$propId));
        if( empty($propInfo) ) return array();
        $propId = $propInfo['prop_id'];
        $propValue = $this->getPropValueByPropId($propId);
        $propInfo['prop_value'] = $propValue;
        return $propInfo;
    }

    /**
     * @brief 根据属性ID，获取属性值数据
     *
     * @param int $propId 属性ID
     *
     * @return array
     */
    public function getPropValueByPropId($propId)
    {
        $tempPropValueInfo = $this->propValuesModel->getList('*',array('prop_id'=>$propId));
        $propValueInfo = array();
        foreach( $tempPropValueInfo as $propValueRow )
        {
            $orderSort = $propValueRow['order_sort'];
            $propValueInfo[$orderSort] = $propValueRow;
        }
        ksort($propValueInfo);
        return $propValueInfo;
    }

    /**
     * @brief 保存属性值
     *
     * @param array $propValue 属性值数组
     * @param int   $propId    属性ID
     * @param string $msg      返回的错误信息
     *
     * @return bool
     */
    public function savePropValues($propValue, $propId, &$msg)
    {
        $oldPropValue = $this->propValuesModel->getList('prop_value_id',array('prop_id'=>$propId));
        foreach( $oldPropValue as $row )
        {
            $tempPropValueId[$row['prop_value_id']] = $row['prop_value_id'];
        }

        $orderSort = 0;
        foreach( (array)$propValue as $key=>$propValueRow )
        {
            $savePropValue = array();
            if( !empty($propValueRow['prop_value_id']) )
            {
                $savePropValue['prop_value_id'] = $propValueRow['prop_value_id'];
                unset($tempPropValueId[$savePropValue['prop_value_id']]);
            }
            $savePropValue['prop_id'] = $savePropValue['prop_id'] ? $savePropValue['prop_id'] : $propId;
            $savePropValue['prop_value'] = $propValueRow['prop_value'];
            $savePropValue['prop_image'] = $propValueRow['prop_image'];
            $savePropValue['order_sort'] = $orderSort;
            if( !$this->propValuesModel->save($savePropValue) ){
                $msg = app::get('syscategory')->_("属性值{$propValueRow['prop_value']}保存失败");
                return false;
            }
            $orderSort++;
        }
        if( $tempPropValueId )
        {
            $this->propValuesModel->delete(array('prop_value_id'=>$tempPropValueId));
        }
        return true;
    }

    function dump($filter,$field = '*',$subSdf = null){
        $rs = parent::dump($filter,$field,$subSdf);

        if( $rs['prop_value'] ){
            $tSpecValue = current( $rs['prop_value'] );
            if( $tSpecValue['order_sort'] && $tSpecValue['prop_value_id'] ){
                $specValue = array();
                foreach( $rs['prop_value'] as $k => $v ){
                    $specValue[$v['order_sort']] = $v;
                }
                ksort($specValue);
                $rs['prop_value'] = array();
                foreach( $specValue as $vk => $vv ){
                    $rs['prop_value'][$vv['prop_value_id']] = $vv;
                }
            }
        }
        return $rs;
    }

    /**
     * 删除属性
     * @param  int/array $prop_id 属性id
     * @return bool
     */
    public function doDelete($propId)
    {
        $objMdlProp = app::get('syscategory')->model('props');
        $propInfo = $objMdlProp->getRow('prop_id, is_def', array('prop_id'=>$propId));
        if($propInfo['is_def']==1)
        {
            $msg = app::get('syscategory')->_('系统默认属性，不可删除');
            throw new \LogicException($msg);
            return false;
        }

        #获取当前属性关联的类目
        $relProp = $this->_checkBindingcat($propId);
        if($relProp)
        {
            $msg = app::get('syscategory')->_('属性已经与类目绑定，不可删除');
            throw new \LogicException($msg);
            return false;
        }
        $delete = $objMdlProp->delete(array('prop_id'=>$propId));
        if(!$delete)
        {
            $msg = app::get('syscategory')->_('属性删除失败');
            throw new \LogicException($msg);
            return false;
        }
        return true;
    }

    private function _checkBindingcat($proId)
    {
        $objMdlProps = app::get('syscategory')->model('cat_rel_prop');
        $relprops = $objMdlProps->getList('cat_id',array('prop_id'=>$proId));
        return $relprops;
    }
}
