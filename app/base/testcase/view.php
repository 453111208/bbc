<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

echo 100;;exit;
class view extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->compiler = app::get('base')->render()->_compiler();
    }

	/**
	 * 测试parse comments
	 *
	 * @return void
	 */
    public function testCompileComments()
    {
        $result = $this->compiler->compile('<{* *}>ddd');
        $this->assertEquals((array)$result, (array)'ddd', 'xiaotiantian');
    }

    public function testDecode()
    {
        $this->assertEquals((array)$this->compiler->_dequote('"asdf"'), (array)'asdf');
        
    }

    // 替换$env为smarty
    public function testEnvReplaceSmarty()
    {
        $result = $this->compiler->compile('<{$env.now}>');
        $verify = '<?php echo time(); ?>';
        $this->assertEquals((array)$result, (array)$verify);
    }

    public function testPluginSetting()
    {
        $result = $this->compiler->compile('<{setting app="base" key="system.main_app" assign="mapp"}>');
        $this->assertEquals('<?php echo $this->_var["mapp"]=app::get("base")->getConf("system.main_app"); ?>', $result);
    }

    public function testPluginSettingx()
    {
        $result = $this->compiler->compile('<{setting app="base" key="system.main_app" assign="mapp"}>');
        
        $this->assertEquals('<?php echo $this->_var["mapp"]=app::get("base")->getConf("system.main_app"); ?>', $result);
    }

    public function testViewFactory()
    {
        //        var_dump(base_facades_view::make());
        var_dump(view::make());exit;
        //::make('abc');
        $this->assertEquals(true, true);
    }
    
}
