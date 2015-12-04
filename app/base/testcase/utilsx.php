<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class utilsx extends PHPUnit_Framework_TestCase
{
    protected $conn = null;

    public function setUp()
    {
    }

	/**
	 * 濞村鐦痯arse comments
	 *
	 * @return void
	 */
    public function testApath()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => ['c1' => 33,
                    'c2' => ['c21' => 'wwww', 'c22' => 'kkk'],
            ],


        ];

        $mk = 'c/c2';
        // 路径数组
        $mkKeys = explode('/',$mk);
        // 路径第一层
        $mkKey = array_pop( $mkKeys );

        //        eval(' $subMustUpdate = $data'.($mkKeys?'["'.implode('"]["',$mkKeys).'"]':'').'["'.$mkKey.'"] ; ');
        //        var_dump($subMustUpdate);
        //        vaar_dump(utils::apath($data, explode('/', $mk)));exit;
        //app::get('sysitem')->model('item')->update(['shop_id' => 3, 'brand_id'=>10, 'cat_id'=> 10], ['item_id' => 111], ['aaa'=>1, 'brand_id' =>10, 'cat_id'=>2]);
        //        app::get('sysitem')->model('item')->update(['shop_id' => 3, 'brand_id'=>10, 'cat_id'=> 10, 'aaa'=> 10], ['item_id' => 111]);


        $users = app::get('desktop')->model('users');
        var_dump($users->get_schema());
        echo '------';
        var_dump($users->_columns());
        exit;
        $param_id = 41;
        $sdf = $users->dump($param_id,'*', array( 'pam_account:account@desktop'=>array('account_id'),'roles'=>array('*') ));
        var_dump($sdf);exit;


        //        var_dump($users->dump($param_id,'*' 'test'));
        exit;
        //        utils::stripslashes_array($data,explode('/',$v));
        echo 44;
        var_dump(base_static_utils::apath($data, ['c', 'c2[first()]']));
        var_dump(base_static_utils::apath($data, ['x']));
    }

    public function testSave() {
        $data = [
            'user_id' => 3,
            'status' =>0,
            'lastlogin' => time(),
            'super' => 0,
            ];
        $mustUpdate = [
            'user_id'=>2,
            'super'=>0,
            'abc' => 2,

        ];
        //        app::get('desktop')->model('users')->update($data, array(),$mustUpdate);

    }
}
