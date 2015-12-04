<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_view_compilers_tramsy extends base_view_compilers_compiler implements base_view_compilers_interface
{

	/**
	 * The file currently being compiled.
	 *
	 * @var string
	 */
	protected $object;

    protected $leftDelimiter            = "<{";
    protected $rightDelimiter            = "}>";
    protected $ifReplaces = null;
    protected $enable_strip_whitespace = false;

    var $_vars            =    array();
    //    var $_plugins            =    array();    // stores all internal plugins
    var $_file            =    "";        // the current file we are processing
    var $_foreachelse_stack        =    array();
    var $_for_stack            =    0;
    var $_sectionelse_stack     =   array();    // keeps track of whether section had 'else' part
    var $_switch_stack        =    array();
    var $_tag_stack            =    array();
    var $_block_stack = array();
    var $compile_helper = array();
    var $view_helper = array();

    protected $buildinTags = [
        'dump',
        'if', 'else', 'elseif', 'continue', '/if', 'break',
        'foreach', '/foreach',
        'capture', '/capture',
        'switch',  '/switch', 'case',
        'assign',
        'include',
        'link', 'url',
    ];


	/**
	 * 是否为previe mode
	 *
	 * @var bool
	 */
    public $is_preview = false;


    public function __construct()
    {
        foreach(kernel::servicelist('view_compile_helper') as $helper)
        {
            $this->loadCompileHelper($helper);
        }

        foreach(kernel::servicelist('view_helper') as $helper_path=>$helper)
        {
            $this->loadViewHelper($helper);
        }
    }


    public function loadCompileHelper($helper)
    {
        foreach(get_class_methods($helper) as $method)
        {
            if (substr($method,0,8)=='compile_')
            {
                $this->setCompileHelper($method,$helper);
            }
        }
    }

    public function loadViewHelper($helper, $test = null) {
        foreach(get_class_methods($helper) as $method)
        {
            $this->setViewHelper($method,$helper);
        }
    }

	/**
	 * 设置compile helper
	 *
	 * @param  \Illuminate\View\Engines\EngineResolver  $resolver
	 * @return void
	 */
    function setCompileHelper($method, $helper)
    {
        $this->compile_helper[$method] = $helper;
    }

	/**
	 * 设置view helper
	 *
	 * @param  string
     * @param  string
	 * @return void
	 */
    public function setViewHelper($method,$helper,$a)
    {
        $this->view_helper[$method] = $helper;
    }

	/**
	 * 编译前处理
	 *
	 * @param  string  $contents
	 * @return string
	 *
	 * @throws \ErrorException
	 */
    protected function beforeCompile($contents)
    {
        return $contents;
    }


	/**
	 * 编译后处理
	 *
	 * @param  string  $name
	 * @return string
	 *
	 * @throws \ErrorException
	 */
    protected function afterCompile($contents)
    {
        if ($this->enable_strip_whitespace)
        {
            $contents = $this->stripWhitespace($contents);
        }

        $contents = preg_replace(array('/\<\?php\s*\?\>/','/\?\>\s*\<\?php/'),'',$contents);
        return $contents;
    }


	/**
	 * Compile the view of contents.
	 *
	 * @param  string  $content
	 * @return string
	 */
    function compileString($contents)
    {
        $this->_block_stack = array();

        $contents = $this->beforeCompile($contents);
        // 处理注释
        $contents = $this->parseComments($contents);
        // 处理php语法
        $contents = $this->parsePhp($contents);
        // 处理语句
        $contents = $this->parseStatements($contents);

        if($this->_block_stack)
        {
            throw new \ErrorException("Block ".implode(',',$this->_block_stack)." not closed");
        }

        $contents = $this->afterCompile($contents);
        // todo: 过渡写法, 后续改到Engine中去extract
        $contents = '<?php $__template = view::getEngine()->getCompiler();?>'.$contents;
        /*
        $contents = '<?php $__template = kernel::single(\'base_view_compilers_tramsy\');?>'.$contents;
        */

        return $contents;
    }

	/**
	 * Compile the view at the given path.
	 *
	 * @param  string  $path
	 * @return string
	 */
    public function compileFile($path)
    {
        if(file_exists($path))
        {
            return $this->compile(file_get_contents($path));
        }
        else
        {
            throw new \InvalidArgumentException('compile file does\'s not exists ['.$path.']');
        }
    }

	/**
	 * 创建Args的数组字符串
	 *
	 * @param  string  $path
	 * @return string
	 */
    protected function buildArgsArray($arguments)
    {
        foreach($arguments as $key => $value)
        {
            if (is_bool($value)){
                $value = $value ? 'true' : 'false';
            }elseif (is_null($value)){
                $value = 'null';
            }
            $arguments[$key] = "'$key' => $value";
        }
        return $output = 'array(' . implode(',', (array)$arguments) . ')';
    }



	/**
	 * 编译, 去掉注释 格式:"<{*...*}>.
	 *
	 * @param  string  $contents
	 * @return string
	 */
    protected function parseComments($contents)
    {
        $pattern = sprintf('!%s\*.*?\*%s!seu', $this->leftDelimiter, $this->rightDelimiter);
        $contents = preg_replace($pattern, '', $contents);
        return $contents;
    }

	/**
	 * 编译, 对于PHP语法进行处理.
	 *
	 * @param  string  $contents
	 * @return string
	 */
    protected function parsePhp($contents)
    {
        $contents = preg_replace("!(\<\?|\?\>)!",'<?php echo \'\1\'; ?>',$contents);
        return $contents;
    }

	/**
	 * 编译, 处理tramsy语句. 有两种类型, 纯变量或方法
	 *
	 * @param  string  $contents
	 * @return string
	 */
    protected function parseStatements($contents)
    {
        $pattern = '!'.$this->leftDelimiter.'(\s*(?:\/|)[a-z][a-z\_0-9]*|)(.*?)'.$this->rightDelimiter.'!isu';
        $callback = function($match)
        {
            $function = $match[1];
            //            echo $function;
            // 处理arguments中的所有变量
            $arguments = $this->parseVars($match[2]);
            // 对引号间的内容做特殊处理
            $this->beginFixQuote($arguments);
            // 方法处理
            if (!empty($function))
            {
                // if和elseif 需要条件分析
                if (in_array($function, array('if', 'elseif')))
                {
                    $arguments = $this->parseIfCondition($arguments);
                }
                else
                {
                    $arguments = empty($arguments)?:$this->parseArgumentsModifier($arguments);
                }
                $this->endFixQuote($arguments);

                return $this->parseTag($function, $arguments);
            }
            // 纯参数处理
            else
            {
                $arguments = $this->parseArgumentModifier($arguments);
                $this->endFixQuote($arguments);
                return '<?php echo '.$arguments.'; ?>';
            }
        };
        return preg_replace_callback($pattern, $callback, $contents);
    }


    protected function parseTagDump($args)
    {
        return '<?php var_dump('.$args['var'].'); ?>';
    }

protected function parseTagUrl($arguments)
    {
        if (array_key_exists('action', $arguments)){
            $key = $method = 'action';
        }elseif(array_key_exists('to', $arguments)){
            $key = 'to';
            $method = 'url';
        }elseif(array_key_exists('route', $arguments)){
            $key = $method = 'route';
        }else{
            return;
        }
        $route = $arguments[$key];
        unset($arguments[$key]);
        if (isset($arguments['_params']))
        {
            $extendArgs = $arguments['_params'];
            unset($arguments['_params']);
        }
        else
        {
            $extendArgs = 'array()';
        }
        $buildArguments = $this->buildArgsArray($arguments);

        return '<?php echo '.$method.'('.$route.', array_merge('.$buildArguments.' , (array) '.$extendArgs.')); ?>';
    }


    protected function parseTagEndIf($arguments)
    {
        return "<?php endif; ?>";
    }

    protected function parseTagIf($arguments)
    {
        return '<?php if(' . $arguments . '): ?>';
    }

    protected function parseTagElseif($arguments)
    {
        return '<?php elseif('.$arguments.'): ?>';
    }

    protected function parseTagBreak($arguments)
    {
        return '<?php break; ?>';
    }

    protected function parseTagContinue($arguments)
    {
        return '<?php continue; ?>';
    }

    protected function parseTagElse($arguments)
    {
        return '<?php else: ?>';
    }

    protected function parseTagForeach($arguments)
    {
        if (!isset($arguments['from'])){
            trigger_error("missing 'from' attribute in 'foreach' in " . __FILE__ . ' on line ' . __LINE__, E_USER_ERROR);
        }
        if (!isset($arguments['value']) && !isset($arguments['item'])){
            trigger_error("missing 'value' attribute in 'foreach' in " . __FILE__ . ' on line ' . __LINE__, E_USER_ERROR);
        }
        if (isset($arguments['item'])){
            $arguments['value'] = $arguments['item'];
        }
        isset($arguments['key']) ? $arguments['key'] = '$'.trim($this->_dequote($arguments['key']))." => " : $arguments['key'] = '';
        if($arguments['name']){
            array_push($this->_foreachelse_stack, $arguments['name']);
            $_result = '<?php $this->_env_vars[\'foreach\']['.$arguments['name'].']=array(\'total\'=>count('.$arguments['from'].'),\'iteration\'=>0);foreach ((array)' . $arguments['from'] . ' as ' . $arguments['key'] . '$'. trim($this->_dequote($arguments['value'])) . '){
                        $this->_env_vars[\'foreach\']['.$arguments['name'].'][\'first\'] = ($this->_env_vars[\'foreach\']['.$arguments['name'].'][\'iteration\']==0);
                        $this->_env_vars[\'foreach\']['.$arguments['name'].'][\'iteration\']++;
                        $this->_env_vars[\'foreach\']['.$arguments['name'].'][\'last\'] = ($this->_env_vars[\'foreach\']['.$arguments['name'].'][\'iteration\']==$this->_env_vars[\'foreach\']['.$arguments['name'].'][\'total\']);
?>';
        }else{
            array_push($this->_foreachelse_stack, false);
            $_result = '<?php foreach ((array)' . $arguments['from'] . ' as ' . trim($this->_dequote($arguments['key'])) . '$'.trim($this->_dequote($arguments['value'])) . '){ ?>';
        }
        return $_result;
    }

    protected function parseTagEndForeach($arguments)
    {
        if ($name = array_pop($this->_foreachelse_stack)){
            return '<?php } unset($this->_env_vars[\'foreach\']['.$name.']); ?>';
        }else{
            return '<?php } ?>';
        }
    }

    protected function parseTagCapture($arguments)
    {
        $output = sprintf('<?php $_block_capture_args=%s;', $this->buildArgsArray($arguments));
        $output .= 'ob_start(); ?>';
        return $output;
    }

    protected function parseTagEndCapture($arguments)
    {
        $output = '<?php $_block_content = ob_get_contents();ob_end_clean();';
        $output .= '$this->_env_vars[\'capture\'][isset($_block_capture_args[\'name\'])?$_block_capture_args[\'name\']:\'default\'] = $_block_content;';
        $output .= 'if (isset($_block_capture_args[\'assign\'])) ${$_block_capture_args[\'assign\']} = $_block_content;';
        $output .= '$_block_capture_args=\'\'; ?>';
        return $output;
    }

    protected function parseTagSwitch($arguments)
    {
        if (!isset($arguments['from'])){
            trigger_error("missing 'from' attribute in 'switch' in " . __FILE__ . ' on line ' . __LINE__, E_USER_ERROR);
        }
        array_push($this->_switch_stack, array("matched" => false, "var" => $arguments['from']));
        return;
    }

    protected function parseTagEndSwitch($arguments)
    {
        array_pop($this->_switch_stack);
        return '<?php break; endswitch; ?>';
    }

    protected function parseTagCase($arguments)
    {
        if (count($this->_switch_stack) > 0){
            $_result = "<?php ";
            $_index = count($this->_switch_stack) - 1;
            if (!$this->_switch_stack[$_index]["matched"])
                {
                    $_result .= 'switch(' . $this->_switch_stack[$_index]["var"] . '): ';
                    $this->_switch_stack[$_index]["matched"] = true;
                }else{
                $_result .= 'break; ';
            }
            if (!empty($arguments['value']))
                {
                    $_result .= 'case '.$arguments['value'].': ';
                }else{
                $_result .= 'default: ';
            }
            return $_result . ' ?>';
        }else{
            trigger_error("unexpected 'case', 'case' can only be in a 'switch' in " . __FILE__ . ' on line ' . __LINE__, E_USER_ERROR);
        }
    }

    protected function parseTagAssign($arguments)
    {
        if (!isset($arguments['var'])){
            trigger_error("missing 'var' attribute in 'pagedata' in " . __FILE__ . ' on line ' . __LINE__, E_USER_ERROR);
        }
        if (!isset($arguments['value'])){
            trigger_error("missing 'value' attribute in 'pagedata' in " . __FILE__ . ' on line ' . __LINE__, E_USER_ERROR);
        }
        if(false===$arguments['value']){
            $arguments['value']='false';
        }elseif(null===$arguments['value']){
            $arguments['value']='null';
        }elseif(''===$arguments['value']){
            $arguments['value']='';
        }
        return '<?php ${' . trim($arguments['var']) . '}='. $arguments['value'].'; ?>';
    }

    protected function parseTagInclude($arguments)
    {
        if (empty($arguments['file'])) throw new \InvalidArgumentException("missing 'file' attribute in include tag in " . __FILE__ . ' on line ' . __LINE__, E_USER_ERROR);
        $app = $arguments['app'];

        $include_file = $arguments['file'];

        return "<?php echo \$__env->make($include_file, array_except(get_defined_vars(), array('__data', '__content')))->render(); ?>";
        /*
        return sprintf('<?php echo $this->_fetch_compile_include(%s, %s, null, false); ?>', $app, $include_file);
        */
    }

    protected function parseTag($function, $arguments)
    {
        if (in_array($function, $this->buildinTags))
        {
            $method = ($function{0} === '/') ? 'parseTagEnd'.ucfirst(substr($function,1 )) : 'parseTag' .ucfirst($function);
            return $this->$method($arguments);
        }

        $_result = "";
        if ($this->_compile_ui_function($function, $arguments, $_result)){
            return $_result;
        }elseif ($this->_compile_compiler_function($function, $arguments, $_result)){
            return $_result;
        }elseif($this->_compile_custom_block($function, $arguments, $_result)){
            if($function{0}=='/'){
                $previousBlockTag = array_pop($this->_block_stack);
                if(substr($function,1)!=$previousBlockTag){
                    throw new \ErrorException('template: block function '.$function.' not closed, previous block tag: '.$previousBlockTag);
                }
            }else{
                $this->_block_stack[] = $function;
            }
            return $_result;
        }elseif ($this->_compile_custom_function($function, $arguments, $_result)){
            return $_result;
        }else{
            trigger_error("function [$function] does not exist in " . __FILE__ . ' on line ' . __LINE__, E_USER_ERROR);
        }
    }


    function parseIfCondition($arguments)
    {
        if ($this->ifReplaces === null) {
            $patternAndReplace = ['is\s+not\s+odd'=>'%2==0', 'is\s+odd'=>'%2==1', 'is\s+not\s+even'=>'%2==1', 'is\s+even'=>'%2==0',
                                  'neq'=>'!=', 'eq'=>'==', 'ne'=>'!=', 'lt'=>'<', 'gt'=>'>', 'lte'=>'<=', 'le'=>'<=', 'ge'=>'>=',
                                  'and'=>'&&', 'or'=>'||', 'not'=>'!', 'mod'=>'%', 'is'=>'=='];

            foreach($patternAndReplace as $k=>$v){
                $this->ifReplaces[0][] = '!(\s+)'.$k.'(\s+)!i';
                $this->ifReplaces[1][] = '\1'.$v.'\2';
            }
        }

        // 替换掉||, 避免被误认为是modifier
        $arguments = str_replace('||',' or ',$arguments);
        $arguments = preg_replace($this->ifReplaces[0], $this->ifReplaces[1], $arguments.' ');

        // 轮训每一个断句, 判断是否存在modifier "|".
        $arguments = explode(' ',trim($arguments));
        foreach($arguments as $i=>$line)
        {
            $arguments[$i] = $this->parseArgumentModifier($line);
        }
        $arguments = implode(' ',$arguments);

        return $arguments;
    }

    function parseArgumentsModifier($arguments)
    {
        preg_match_all('/([a-z0-9\_\-]+)=(\'|"|)(.*?(?:[^\\\\]|))\2\s/isu',$arguments.' ',$matches,PREG_SET_ORDER);
        $ret = array();
        foreach($matches as $match){
            if($match[2]){
                $ret[$match[1]] = $match[2].$match[3].$match[2];

            }else{
                // 兼容变态的写法 aaa=xxx, bbb=kkk|default:1 等. 需要写引号的时候不写引号
                if (!in_array($match[3]{0}, ['_', '$']))
                {
                    // 不存在modifier的情况
                    if (strpos($match[3], '|')===false)
                    {
                        $match[3] = '\''.$match[3].'\'';
                    }
                    else
                    {
                        $match[3] = '\''.substr($match[3], 0, strpos($match[3], '|')).'\''.substr($match[3],strpos($match[3], '|'));
                    }
                }

                $ret[$match[1]] = $this->parseArgumentModifier($match[3]);
            }
        }
        return $ret;
    }

    function prepareFixQuote($match)
    {
        $this->fixQuotes[$this->fixQuotesCounter] = $match[0];
        return '_!ok'.($this->fixQuotesCounter++).'!_';
    }

    function restoreFixQuote($match)
    {
        return $this->fixQuotes[$match[1]];
    }

    function beginFixQuote(&$variable)
    {
        $this->fixQuotesCounter = 0;
        $this->fixQuotes = array();
        $variable = preg_replace_callback('/([\'"]).*?(?:[^\\\\]|)\1/u',array($this,'prepareFixQuote'),$variable);
    }

    function endFixQuote(&$variable)
    {
        if($this->fixQuotes)
        {
            $variable = preg_replace_callback('/_!ok([0-9]+)!_/u',array($this,'restoreFixQuote'),$variable);
        }
    }

	/**
	 * 获取view_helper的变异结果
	 *
	 * @param  string $function
	 * @return string
	 */
    protected function getRunTimeFunc($function)
    {
        if(isset($this->view_helper[$function]))
        {
            // 后续改造, 传递compiler/factory变量进入编译环境
            return sprintf('$__template->view_helper[\'%s\']->%s', $function, $function);
        }
        else
        {
            return false;
        }
    }

    function parseArgumentModifier($variable)
    {
        if(strpos($variable,'|')){
            $_mods = explode('|',$variable);
            $variable = array_shift($_mods);
            foreach($_mods as $mod){
                if($p=strpos($mod,':')){
                    $_arg = $variable.str_replace(':',',',substr($mod,$p));
                    $mod = substr($mod,0,$p);
                }else{
                    $_arg = $variable;
                }
                if($mod{0}=='@'){
                    $mod = substr($mod,1);
                }
                if(isset($this->compile_helper['compile_modifier_'.$mod])){
                    $variable = $this->compile_helper['compile_modifier_'.$mod]->{'compile_modifier_'.$mod}($_arg,$this);
                }elseif($func = $this->getRuntimeFunc('modifier_'.$mod)){
                    $variable = $func.'('.$_arg.')';
                }elseif(function_exists($mod)){
                    $variable = $mod.'('.$_arg.')';
                }else{
                    $variable = "trigger_error(\"'" . $mod . "' modifier does not exist\", E_USER_NOTICE);";
                }
            }
        }

        return $variable;
    }


    protected function parseEnvVar($varStr)
    {
        $vars = explode('.', $varStr);
        $first = $vars[0];
        $second = isset($vars[1]) ? $vars[1] : false;
        $third = isset($vars[2]) ? $vars[2] : false;
        $forth = isset($vars[3]) ? $vars[3] : false;

        switch(strtoupper($second))
        {
            case 'CONF':
                // 第三个参数用为app_id, 第四个参数以后才为getConf参数
                if($forth === false) throw new \ErrorException('$env conf\'s argument is wrong:('.$varStr.') ');
                $var_ns = 'app::get(\''.$third.'\')->getConf(\''.implode('.', array_slice($vars, 3)).'\')';
                $varStr = '';
                break;
            case 'GET': case 'POST': case 'COOKIE': case 'ENV': case 'SERVER': case 'SESSION':
                $var_ns = '$_'.strtoupper($second);
                $varStr = implode('.', array_slice($vars, 2));
                break;
            case 'BASE_URL':
                $var_ns = 'kernel::base_url()';
                $varStr = '';
                break;
            case 'BASE_FULL_URL':
                $var_ns = 'kernel::base_url(1)';
                $varStr = '';
                break;
            case 'STATICS_HOST_URL':
                $var_ns = 'kernel::get_app_statics_host_url()';
                $varStr = '';
                break;
            case 'NOW':
                $var_ns = 'time()';
                $varStr = '';
                break;
            case 'CONST':
                $var_ns = 'constant(\''.$third.'\')';
                $varStr = '';
                break;
            case 'FOREACH':
                $var_ns = '$this->_env_vars[\'foreach\']';
                $varStr = implode('.', array_slice($vars, 2));
                break;
            default:
                $var_ns = '$this->_env_vars[\''.$second.'\']';
                $varStr = implode('.', array_slice($vars, 2));
                break;
        }
        return array($var_ns, $varStr);
    }

    protected function parseVar($varStr)
    {
        $var_ns = '';
        $vars = explode('.', $varStr);
        $first = $vars[0];
        $second = isset($vars[1]) ? $vars[1] : false;
        $third = isset($vars[2]) ? $vars[2] : false;
        $forth = isset($vars[3]) ? $vars[3] : false;

        if($second !== false)
        {
            if($first=='$env')
            {
                list($var_ns, $varStr) = $this->parseEnvVar($varStr);
            }
            else
            {
                $var_ns = '$'.substr($first, 1);
                $varStr = implode('.', array_slice($vars, 1));
            }

            $callback = function($varStr, $val)
            {
                $varStr .= in_array($val{0}, array('\'', '"', '$')) ? '['. $val . ']' : '[\''. $val . '\']';
                return $varStr;
            };
             // 重新拼装变量数组
            $varStr = $varStr ? array_reduce(explode('.' , $varStr), $callback) : '';

         }

        //$varStr = $var_ns.preg_replace('!\$([a-z0-9\_]+)!iu','$this->_vars[\'\1\']', $varStr);
        $varStr = $var_ns.$varStr;
        return $varStr;

    }

	/**
	 * 编译tag中的所有的变量
	 *
	 * @param string  $expression
	 * @return string
	 */
    protected function parseVars($tagStr)
    {

        $varStackCounter = 0;
        $varStack=array();
        // 违规操作过滤处理
		if(preg_match('/(eval|exec|system|shell_exec|passthru|popen)(\s|\/\*(.*)\*\/)*\(.*\)/i', $tagStr)) return 0;

        // 提取tagStr中包含的所有变量存入变量处理栈
        // 为了避免干扰, 将tagStr中放入栈中的变量, 替换为中间字符串_s{$n}s_
        $output = preg_replace_callback('!(\$[a-z0-9\_\.\$\[\]\"\\\']+)!isu', function($match) use (&$varStackCounter, &$varStack)
        {
            $callback = function($match) use (&$varStackCounter, &$varStack)
            {
                $varStack[$varStackCounter] = $match[1];
                return '."_s'.($varStackCounter++).'s_"';
            };

            $varStack[$varStackCounter] = preg_replace_callback('/\[(\$[a-z0-9\_\.]+)\]/iu', $callback, $match[1]);

            return '"_s'.($varStackCounter++).'s_"';
        }, $tagStr);

        // 对变量处理栈中待处理的变量做处理
        foreach($varStack as $i=>$varStr)
        {
            // 解析单个变量, 将解析好后的变量重新压入$varStack栈中
            $varStack[$i] = $this->parseVar($varStr);
        }

        $callback = function($match) use ($varStack)
        {
            return $varStack[$match[1]];
        };
        // 将_s{$n}s_ 重新替换成栈中处理过的变量
        // 处理第1层

        $output = preg_replace_callback('!"_s([0-9]+)s_"!', $callback,$output);
        // 处理第2层
        $output = preg_replace_callback('!"_s([0-9]+)s_"!', $callback,$output);
        return $output;
    }


    public function stripWhitespace($html)
    {
        //replace <!-- /*   */ -->
        $html = preg_replace("|<!-- /\*(.*)\*/ -->|isU", "", $html);
        //replace all \n\r to null
        $html = preg_replace("![\n\r]{2,}!is", "\n", $html);
        //replace space to null
        $html = preg_replace("!\n[\s\t]{1,}!is", "\n", $html);
        //replace space to null
        $html = preg_replace("![\x20\t]{1,}!is", " ", $html);
        return $html;
    }

    function _dequote($string)
    {
        if (($string{0} == "'" || $string{0} == '"') && $string{strlen($string)-1} == $string{0}){
            return substr($string, 1, -1);
        }else{
            return $string;
        }
    }

    function _compile_ui_function($function, $arguments,&$_result)
    {
        if(method_exists(view::ui(), $function))
        {
            $buildArguments =  $this->buildArgsArray($arguments);
            $_result = '<?php echo ';
            $_result .= 'view::ui()->'.$function . '('.$buildArguments.');';
            $_result .= '?>';
            return true;
        }
        else
        {
            return false;
        }
    }

    function _compile_compiler_function($function, $arguments, &$_result)
    {
        $function = "compile_".$function;
        if (isset($this->compile_helper[$function]))
        {
            $object = $this->compile_helper[$function];
            $_result = '<?php '.$object->$function($arguments,$this).' ?>';
            return true;
        }else{
            return false;
        }
    }

    function _compile_custom_function($function,$arguments, &$_result)
    {
        if ($function =='header') {
            $__test=1;
            //var_dump($this->view_helper);

        }
        $function = $this->getRuntimeFunc("function_".$function, false);
        if ($__test == 1) {
            //            var_dump($function);

        }


        if ($function) {
            $buildArguments = $this->buildArgsArray($arguments);
            $_result = '<?php echo ';
            $_result .= $function . '('.$buildArguments.', $__template);';
            $_result .= '?>';
            return true;
        } else {
            return false;
        }
    }

    function _compile_custom_block($function, $arguments, &$_result)
    {
        if ($function{0} == '/') {
            $start_tag = false;
            $function = substr($function, 1);
        } else {
            $start_tag = true;
        }
        if ($function_call = $this->getRuntimeFunc("block_".$function, false))
        {
            if ($start_tag)
            {
                $buildArguments = $this->buildArgsArray($arguments);
                $_result = "<?php \$__template->_tag_stack[] = array('".str_replace("'","\\'",$function)."', ".$buildArguments."); ";
                // 发现block开始时, 调用对应block function, 以做初始化. 目前暂无此需求, 因此注释掉
                $_result .= $function_call . '('.$buildArguments.', null, $__template); ';
                $_result .= 'ob_start(); ?>';
            }
            else
            {
                $_result .= '<?php $_block_content = ob_get_contents(); ob_end_clean(); ';
                $_result .= '$_block_content = ' . $function_call . '($__template->_tag_stack[count($__template->_tag_stack) - 1][1], $_block_content, $__template); ';
                $_result .= 'echo $_block_content; array_pop($__template->_tag_stack); $_block_content=\'\'; ?>';
            }
            return true;
        }
        else
        {
            return false;
        }
    }
}

