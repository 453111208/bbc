<?php
class sysshop_mdl_shop extends dbeav_model{

    public $has_many = array(
        'cat' => 'shop_rel_lv1cat:replace',
        'brand' => 'shop_rel_brand:replace',
        'shopinfo' => 'shop_info:replace',
    );

    public $subSdf = array(
        'default' => array(
            'cat' => array('*'),
            'brand' => array('*'),
            'shopinfo' => array('*'),
        )
    );
}


