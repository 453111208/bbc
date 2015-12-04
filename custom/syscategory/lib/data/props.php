<?php
/**
 * 属性保存，获取属性数据
 */
class syscategory_data_props {

    /**
     * @brief 属性model
     */
    public $propModel = null;

    public function __construct()
    {
        $this->propModel = app::get('syscategory')->model('props');
    }

    /**
     * @brief 新增属性数据
     *
     * @param array $data 需要新增属性和属性值的数据
     * @param string $msg 返回的错误或成功信息
     *
     * @return bool
     */
    public function add($data, &$msg)
    {

        if( !$this->__check($data,$msg) )  return false;
        if(strlen($data['prop_name'])>20)
        {
            $msg = app::get('syscategory')->_('属性名不能超过20个字符');
            return false;
        }
        if($data['is_def']){
            throw new \LogicException("系统默认属性只能编辑不能添加！");
        }
        if($data['order_sort']<1)
        {
            throw new \LogicException("系统默认属性排序为0，其他新添属性必须是大于0的整数！");
        }
        $data['show_type']  = 'text'; //除了颜色销售属性，其他所有新添加的销售属性和自然属性都是text，不能是image
        $data['modified_time'] = time();

        //新增属性
        $propId = $this->propModel->insert($data);
        if( !$propId )
        {
            $msg = app::get('syscategory')->_('属性保存失败');
            return false;
        }

        $flag = $this->propModel->savePropValues($data['prop_value'], $propId, $msg);
        if( $flag ) $msg = app::get('syscategory')->_('属性保存成功');
        return $flag;
    }

    /**
     * @brief 更新属性,更新的数据中包含prop_id 表示更新该属性ID的数据
     *
     * @param $data 更新的数据
     * @param $msg  错误或成功信息
     *
     * @return bool
     */
    public function update($data, &$msg)
    {
        if( empty( $data['prop_id'] ) )
        {
            $msg = app::get('syscategory')->_('参数错误');
            return false;
        }
        $propInfo = $this->propModel->getRow('is_def', array('prop_id'=>$data['prop_id']));
        if($propInfo['is_def'] == 1)
        {
            $data['show_type'] == 'image';
            $data['order_sort'] == '0';
        }
        else
        {
            $data['show_type'] = 'text';
            if($data['order_sort']<1)
            {
                throw new \LogicException("系统默认属性排序为0，其他新添属性必须是大于0的整数！");
            }
        }

        if( !$this->__check($data,$msg) ) return false;

        $data['modified_time'] = time();

        //更新属性
        if( !$this->propModel->update($data, array('prop_id'=>$data['prop_id'])) )
        {
            $msg = app::get('syscategory')->_('属性保存失败');
            return false;
        }

        $propId = $data['prop_id'];
        $flag = $this->propModel->savePropValues($data['prop_value'], $propId, $msg);
        if( $flag ) $msg = app::get('syscategory')->_('属性保存成功');
        return $flag;
    }

    /**
     * @brief 检查保存属性数据的合法性
     *
     * @param array $data
     * @param string $msg
     *
     * @return bool
     */
    private function __check($data, &$msg)
    {
        $i = 0;
        if( empty( $data['prop_value']) )
        {
            $msg = app::get('syscategory')->_('请添加属性值');
            return false;
        }

        $propValueArr = array();
        foreach( (array)$data['prop_value'] as $propValue )
        {
            if( $propValue['prop_value'] == '' )
            {
                $msg = app::get('syscategory')->_('属性值不能为空');
                return false;
            }

            if( $this->__utf8Strlen($propValue['prop_value']) > 20 )
            {
                $msg = app::get('syscategory')->_('属性值不能大于20个字');
                return false;
            }

            if( !in_array($propValue['prop_value'],$propValueArr) )
            {
                $propValueArr[] = $propValue['prop_value'];
            }
            else
            {
                $msg = app::get('syscategory')->_("属性值{$propValue['prop_value']}重复");
                return false;
            }

            $i++;
            if( $i > 30 )
            {
                $msg = app::get('syscategory')->_('属性值的条数不能大于30');
                return false;
            }
        }

        if( $this->__utf8Strlen($data['prop_name']) > 20 )
        {
            $msg = app::get('syscategory')->_('属性名称不能大于20个字');
            return false;
        }

        if ($this->__utf8Strlen($data['prop_memo']) > 20 )
        {
            $msg = app::get('syscategory')->_('属性备注不能大于20个字');
            return false;
        }

        return true;
    }

    /**
     * @brief 计算中文字符串长度
     *
     * @param string $string  字符串
     *
     * @return int  字符串长度
     */
    private function __utf8Strlen($string = null)
    {
        // 将字符串分解为单元
        preg_match_all("/./us", $string, $match);
        // 返回单元个数
        return count($match[0]);
    }

    /**
     * @brief 获取属性默认图片
     *
     * @return array()
     */
    public function getPropDefaultPic()
    {
        $pic['prop_default_pic'] = app::get('syscategory')->getConf('prop.default.pic');
        $pic['prop_image_height'] = app::get('syscategory')->getConf('prop.image.height');
        $pic['prop_image_width'] = app::get('syscategory')->getConf('prop.image.width');
        return $pic;
    }

    public function getNatureProps($catId)
    {
        $objMdlCatRelProp = app::get('syscategory')->model('cat_rel_prop');
        $catRelPropList = $objMdlCatRelProp->getList('*',array('cat_id'=>$catId),0,-1,'order_sort ASC');
        if( !$catRelPropList ) return array();
        $propIds = array();
        foreach($catRelPropList as $v)
        {
            $propIds[] = $v['prop_id'];
        }
        $objMdlProps = app::get('syscategory')->model('props');
        $objMdlPropValues = app::get('syscategory')->model('prop_values');
        $result = $objMdlProps->getList('*',array('prop_id'=>$propIds, 'prop_type'=>'nature'),0,-1,'order_sort ASC');
        foreach($result as &$v)
        {
            $v['prop_value'] = $objMdlPropValues->getList('*',array('prop_id'=>$v['prop_id']));
        }
        return $result;
    }

    public function getPropsList($propId, $fields="*")
    {
        if( $fields != '*' )
        {
            $fields = explode(',' , $fields);

            if( !in_array('prop_id', $fields) )
            {
                $fields[] = 'prop_id';
            }

            $fields = implode(',', $fields);
        }

        $objMdlProps = app::get('syscategory')->model('props');
        $propsList = $objMdlProps->getList($fields,array('prop_id'=>$propId));
        $data = array_bind_key($propsList,'prop_id');
        return $data;
    }

    public function getPropsValueList($propValueId, $fields='*')
    {
        if( $fields != '*' )
        {
            $fields = explode(',' , $fields);

            if( !in_array('prop_value_id', $fields) )
            {
                $fields[] = 'prop_value_id';
            }

            $fields = implode(',', $fields);
        }

        $objMdlPropValues = app::get('syscategory')->model('prop_values');
        $propValuesList = $objMdlPropValues->getList($fields, array('prop_value_id'=>$propValueId) );
        $data = array_bind_key($propValuesList,'prop_value_id');
        return $data;
    }
}

