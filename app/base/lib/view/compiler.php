<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class base_view_compiler{

    function compile_modifier_default($attrs,$compiler){
        list($string, $default ) = explode(',',$attrs);
        if($default===''){
            $default = '\'\'';
        }
        return '((isset('.$string.') && \'\'!=='.$string.')?'.$string.':'.$default.')';
    }
    
    function compile_ecos_logo(){
        return '?>Powered By <a href="http://www.shopex.cn" target="_blank">ECOS</a><?php';
    }

    function compile_math($attrs, &$compiler) {
        if(($attrs['equation']{0}=='\'' || $attrs['equation']{0}=='"') && $attrs['equation']{0}==$attrs['equation'][strlen($attrs['equation'])-1]){
            $equation = $attrs['equation'];
        }else{
            $equation = '"'.$attrs['equation'].'"';
        }
    
        $format = $attrs['format'];
        $assign = $attrs['assign'];
    
        unset($attrs['equation'],$attrs['format'],$attrs['assign']);
    
        foreach($attrs as $k=>$v){
            $re['/([^a-z])'.$k.'([^a-z])/i'] = '$1('.$v.')$2';
        }
        $equation = substr(preg_replace(array_keys($re),array_values($re),$equation),1,-1);
        if($format){
            $equation = 'sprintf('.$format.','.$equation.')';
        }
        if($assign){
            $equation = '$this->_vars['.$assign.']='.$equation;
        }
        return 'echo ('.$equation.');';
    }

    function compile_setting($arguments, &$compiler)
    {
        
        if(empty($arguments['app'])) throw new \InvalidArgumentException('missing app argument in setting tag in '.__FILE__.' on line '.__LINE__);
        if(empty($arguments['key'])) throw new \InvalidArgumentException('missing key argument in setting tag in '.__FILE__.' on line '.__LINE__);
        $ouput = '';
        if (isset($arguments['assign']))
        {
            $output = sprintf('echo $this->_var[%s]=app::get(%s)->getConf(%s);',
                               $arguments['assign'], $arguments['app'], $arguments['key']);
        }
        else
        {
            $output = sprintf('echo app::get(%s)->getConf(%s);', $arguments['app'], $arguments['key']);
        }
        return $output;
    }
}
