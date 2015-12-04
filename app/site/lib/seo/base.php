<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_seo_base
{

    function get_default_seo()
    {
        return array(
            'seo_title'=>app::get('site')->getConf('page.default_title'),
            'seo_keywords'=>app::get('site')->getConf('page.default_keywords'),
            'seo_content'=>app::get('site')->getConf('page.default_description')
        );
    }


    final public function get_seo_conf($route, $args=null)
    {
        $objMdlSeo = app::get('site')->model('seo');
        $seo = $objMdlSeo->getRow('param',array('route'=>$route));
        if(!$seo) $seo['param'] = $this->get_default_seo();
        $seo['param'] = $seo['param'];
        return $seo['param'] = $this->toreplace($seo['param'],$args);
    }

    function toreplace($data,$args){
        if(is_array($data)){
            foreach($data as $key=>$val){
                if(preg_match_all('/\{([a-z][a-z0-9_]+)\}/i', $val, $matches)){
                    foreach($matches[1] as $v){
                        if(substr($v,0,4)=='ENV_'){
                            $v = substr($v,4);
                            if(is_array($args)){
                                if(array_key_exists($v, $args)){
                                    $to_replace['{ENV_'.$v.'}'] = $args[$v];
                                }else{
                                    $to_replace['{ENV_'.$v.'}'] = '';
                                }
                            }
                        }else{
                            $to_replace = '';
                        }
                    }
                    if(is_array($to_replace))
                        $data[$key] = str_replace(array_keys($to_replace),array_values($to_replace),$val);
                }
            }
        }

        foreach($data as $key=>$value)
        {
            $arr = explode('_',$value);
            $arr = array_filter($arr);
            if(!$arr)
            {
                unset($data[$key]);
            }
        }
        return $data;
    }

    public function set($route,$args)
    {
        $objMdlSeo = app::get('site')->model('seo');
        $seo = $objMdlSeo->getRow('param',array('route'=>$route));
        if(!$seo) $seo['param'] = $this->get_default_seo();
        $param = $this->toreplace($seo['param'],$args);
        if($param['seo_title'])
        {
            theme::setTitle($param['seo_title']);
        }
        if($param['seo_keywords'])
        {
            theme::setKeywords($param['seo_keywords']);
        }
        if($param['seo_content'])
        {
            theme::setDescription($param['seo_content']);
        }

        if($param['seo_noindex'])
        {
            theme::setNoindex($param['seo_noindex']);
        }

        if($param['seo_nofollow'])
        {
            theme::setNofollow($param['seo_nofollow']);
        }
    }
}//End Class

