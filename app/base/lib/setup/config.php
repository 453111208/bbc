<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class base_setup_config{

	/**
	 * The Filesystem instance.
	 *
	 * @var base_filesystem
	 */
	protected $files = null;

	/**
	 * config path
	 *
	 * @var string
	 */
    protected $configPath = '';

	/**
	 * 环境标识
	 *
	 * @var string
	 */
    protected $environment = '';

	/**
	 * 当目标配置文件存在时候
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
    public $overwrite = true;

    public function __construct()
    {
        $this->files = kernel::single('base_filesystem');
        $this->configPath = config::get_path();
        $this->environment = 'production';
    }


    public function groupWrite($group, $groupConfig)
    {
        $sourceFile = "{$this->configPath}/{$group}.php";
        $targetFile = "{$this->configPath}/{$this->environment}/{$group}.php";
            
        if (!$this->files->exists($sourceFile))
        {
            throw new \RuntimeException("Config:{$group} not exisits");
        }

        // 如果是强制写入或者目标文件不存在的情况下, copy
        if ($this->overwrite || !$this->files->exists($targetFile) ) {
            if (!$this->files->copy($sourceFile, $targetFile))
            {
                throw new \RuntimeException("Copy config:{$group} failed");
            }
            $content = file_get_contents($targetFile);
            foreach ($groupConfig as $key => $value) {
                $pattern[] = '%'.strtoupper($key).'%';
                $replacements[] = $value;
            }

            $content = str_replace($pattern, $replacements, $content);
            if (!file_put_contents($targetFile, $content)) {
                throw new \RuntimeException('Writing config file '.$group.'... fail.');
            }
        }
        else
        {
            logger::info("Copy config:{$group}, current is not overwrite mode  and target file already exists, ignore copy.");
        }
    }

    public function write($configs)
    {
        $groupConfigs = array();
        foreach($configs as $key => $config)
        {
            $group = substr($key, 0, strpos($key, '.'));
            $item = substr($key, strpos($key, '.')+1);
            $groupConfigs[$group][$item] = $config;
        }

        array_walk($groupConfigs, function($groupConfig, $group) {
            $this->groupWrite($group, $groupConfig);
        });
        logger::info('Writing all config file... ok.');
        return true;
    }

    

    static function deploy_info()
    {
        return kernel::single('base_xml')->xml2array(
            file_get_contents(ROOT_DIR.'/config/deploy.xml'),'base_deploy');
    }

}
