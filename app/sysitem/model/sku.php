 <?php

class sysitem_mdl_sku extends dbeav_model{

    public $has_many = array(
        // 'sku_store' => 'sku_store:contrast',
    );

    function save(&$data,$mustUpdate = null, $mustInsert=false){
        if (isset($data['spec_desc']) && $data['spec_desc'] && is_array($data['spec_desc']) && isset($data['spec_desc']['spec_value']) && $data['spec_desc']['spec_value'])
        {
            $oProps = app::get('syscategory')->model('props');
            $tmpSpecInfo = array();
            foreach( $data['spec_desc']['spec_value'] as $spec_v_k => $spec_v_v ){
                $specname = $oProps->getRow( 'prop_name' ,array('prop_id'=>$spec_v_k));
                $tmpSpecInfo[] = $specname['prop_name'].'：'.$spec_v_v;
            }
            $data['spec_info'] = implode('、', (array)$tmpSpecInfo);
        }

        $data['freez'] = intval($data['freez']);

        $flagSaveSku = parent::save($data,$mustUpdate);

        $objMdlSkuStore = app::get('sysitem')->model('sku_store');
        $storeData = array(
            'item_id'=>$data['item_id'],
            'sku_id'=>$data['sku_id'],
            'store'=>$data['sku_store']['store'],
            'freez'=>$data['sku_store']['freez']
        );
        $flagSaveSkuStore = $objMdlSkuStore->save($storeData);
        return $flagSaveSku && $flagSaveSkuStore;
    }

}
