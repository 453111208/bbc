<?php
/**
 * 品牌保存，获取品牌数据
 */
class sysspfb_data_brand {

    public $brandModel = null;

    public function __construct()
    {
        $this->objMdlBrand = app::get('sysspfb')->model('brand');
    }

    /**
     * 添加品牌数据
     *
     * @param array $data
     *
     * @return bool true 保存成功| false 保存失败
     */
    public function add($data)
    {
        if( !$this->__check($data) )  return false;

        $saveData['brand_name']  = trim($data['brand_name']);
        $saveData['brand_alias'] = trim($data['brand_alias']);
        $saveData['order_sort']  = $data['order_sort'];
        $saveData['brand_logo']  = $data['brand_logo'];
        $saveData['modified_time'] = time();

        $res = $this->objMdlBrand->insert($saveData);
        return $res;
    }

    /**
     * @brief 根据品牌ID，更新品牌数据
     *
     * @param  array $data
     * @param  string $msg
     *
     * @return bool
     */
    public function update($data)
    {
        if( !$this->__check($data) )  return false;

        $saveData['brand_name']  = trim($data['brand_name']);
        $saveData['brand_alias'] = trim($data['brand_alias']);
        $saveData['order_sort']  = $data['order_sort'];
        $saveData['brand_logo']  = $data['brand_logo'];
        $saveData['modified_time'] = time();

        $res = $this->objMdlBrand->update($saveData,array('brand_id'=>array($data['brand_id'])));
        return $res;
    }

    /**
     * @brief 检查保存品牌数据合法性
     *
     * @param array $data
     * @param string $msg
     *
     * @return bool
     */
    private function __check($data)
    {
        if( !trim($data['brand_name']) )
        {
            throw new \LogicException('品牌名称必填');
            return false;
        }

        if( strlen(trim($data['brand_name'])) > 50 )
        {
            throw new \LogicException('品牌名称不能大于50个字符');
            return false;
        }

        $brandId = $this->objMdlBrand->getBrandIdByName($data['brand_name']);
        if( $brandId && $brandId != $data['brand_id'] )
        {
            throw new \LogicException('品牌名称已存在，不能重复');
            return false;
        }

        if( !is_numeric($data['order_sort']) )
        {
            throw new \LogicException('排序必须为数字');
            return false;
        }

        return true;
    }
}



