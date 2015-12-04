<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class api extends PHPUnit_Framework_TestCase{


    public function testApi(){
      //$apis = config::get('apis.routes');
      //foreach($apis as $key=>$api)
      //{
      //    $handlar = $api['uses'];

      //    $args = explode('@',$handlar);

      //    $class = $args[0];
      //    $func  = $args[1];
      //    $cc = new $class;
      //    echo $key . "  :  " . $cc->apiDescription;
      //    echo "\n";

      //}
      //exit;


//      app::get('system')->setConf('prismUserKey', '3wt4hngc');
//      app::get('system')->setConf('prismUserSecret', 'hrk2m46n5fempd2uvoz6');
//      exit;


    //$keys = app::get('base')->getConf('prismKeys');
    //    print_r($keys);

        $initer = kernel::single('system_prism_init');
        //$initer->init();
        exit;

      //$apiInfo = kernel::single('system_prism_apiJson');
      //$ss = $apiInfo->format();
      //$ss = $apiInfo->getJsonUrl();
      //print_r($ss);exit;
      //foreach($ss as $apiKey=>$apiInfoJson)
      //{

      //    $path = '/data/www/json/';
      //    $fileName = $apiKey . '.json';

      //    $realname = $path . $fileName;

      //    $fp = fopen($realname, 'w');

      //    fwrite($fp, $apiInfoJson);

      //    fclose($fp);
      //}
        //print_r($ss);
    }
}

