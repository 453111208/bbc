<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_init
{
    public function init()
    {
        $prismInitStepModel = app::get('system')->model('prism_initstep');
        $mission = $prismInitStepModel->getNext();
        if( !($mission == null) )
        {
            $this->process();
            return;
        }
        //初始化用户
        $this->__initUser();

        //获取本地的app信息。
        $this->__initApp();

        //初始化api
        $this->__initApi();

        //初始化绑定信息
        $prismInitStepModel->createMission('system_prism_init_bind@bind', $key);

        $this->process();
        return true;
    }

    public function update()
    {
        $prismInitStepModel = app::get('system')->model('prism_initstep');
        $mission = $prismInitStepModel->getNext();
        if( !($mission == null) )
        {
            $this->process();
            return;
        }

        if( !(kernel::single('system_prism_store')->get('prismInitComplete') == true) )
        {
            $this->init();
            return ;
        }

        //更新app
        $this->__updateApp();

        //更新api
        $this->__updateApi();

        //重新绑定一次
        $prismInitStepModel->createMission('system_prism_init_bind@bind', $apiKey);

        $this->process();
        logger::info('Api info is updated to prism, ok.');

        kernel::single('system_prism_queue_script')->update();

        return true;
    }

    public function process($isCommand = false)
    {
        $prismInitStepModel = app::get('system')->model('prism_initstep');
        while(1)
        {
            $mission = $prismInitStepModel->getNext();
            logger::info('prism api/app process : ' . var_export($mission, 1));
            if($isCommand == true)
            {
                echo 'start : ' . $handlar . ' : ' . $mission['params'] . "\n";
            }
            if($mission == null)
            {
                break;
            }
            $prismInitStepModel->setStartTime($mission);
            $result = $this->run($mission['handlar'], $mission['params']);
            $prismInitStepModel->setComplete($mission, $result);
        }

        kernel::single('system_prism_store')->set('prismInitComplete', true);
        return true;
    }

    public function run($handlar, $params)
    {
        $args = explode('@', $handlar);
        $class = $args[0];
        $method = $args[1];
        $result = kernel::single($class)->$method($params);
        return $result;
    }

    /***
     *
     * ==========================================================
     * 下面是工具封装,初始化和更新的工具类
     * ==========================================================
     *
     */

    private function __initUser()
    {
        $prismInitStepModel = app::get('system')->model('prism_initstep');
         //创建用户
        $prismInitStepModel->createMission('system_prism_init_user@create', null);
        //给用户开发者权限（激活用户）
        $prismInitStepModel->createMission('system_prism_init_user@active', null);
        //给用户api开发者权限
        $prismInitStepModel->createMission('system_prism_init_user@apiprovider', null);
        //获取用户的key和secret，顺便保存用户的信息
        $prismInitStepModel->createMission('system_prism_init_user@info', null);

        return null;
    }

    private function __initApp()
    {
        $prismInitStepModel = app::get('system')->model('prism_initstep');
        //现在直接拉取app列表，以后这里需要修改
        $ecapps = app::get('base')->model('apps')->getList('*', ['status'=>'active']);
        $vapp = config::get('prism.virtualApp');
        $ecapps = array_merge($ecapps, $vapp);

        foreach( $ecapps as $ecapp )
        {
            if(in_array( $ecapp['type'] ? $ecapp['type'] : app::get($ecapp['app_id'])->define('type'), ['service', 'site', 'virtual']))
            {
                //初始化app
                $prismInitStepModel->createMission('system_prism_init_app@create', $ecapp['app_id']);
                //初始化app的key
                $prismInitStepModel->createMission('system_prism_init_app@createKey', $ecapp['app_id']);
            }
        }

        return null;
    }

    private function __initApi()
    {
        $prismInitStepModel = app::get('system')->model('prism_initstep');
        $apiJsons = kernel::single('system_prism_apiJson')->getJsonUrl();

        foreach($apiJsons as $key => $apiJson)
        {
            $prismInitStepModel->createMission('system_prism_init_api@import', $key);
            $prismInitStepModel->createMission('system_prism_init_api@setConf', $key);
            $prismInitStepModel->createMission('system_prism_init_api@online', $key);
        }
        kernel::single('system_prism_store')->set('prismApiLog', $apiJsons);

        return null;
    }

    private function __updateApp()
    {
        $prismInitStepModel = app::get('system')->model('prism_initstep');

        //更新key和app，获取已经安装的ecapp，当app没被映射到prism上时，添加一个
        //key只做新增，不做删除
        $ecapps = app::get('base')->model('apps')->getList('*', ['status'=>'active']);
        $vapp = config::get('prism.virtualApp');
        $ecapps = array_merge($ecapps, $vapp);
        foreach($ecapps as $ecapp)
        {
            //变量取值规则：优先取数组中的type字段，否则就去app.xml文件中的type字段
            //
            //逻辑规则：type字段的值为service或者site或者vitual三者之一时上传
            //
            if(in_array( $ecapp['type'] ? $ecapp['type'] : app::get($ecapp['app_id'])->define('type'), ['service', 'site', 'virtual']))
            {
                $keyId = $ecapp['app_id'];
                $appKeys = app::get('base')->getConf('prismKeys');
                if( !( isset($appKeys[$keyId]) && ($appKeys[$keyId] != null) ) )
                {
                    $prismInitStepModel->createMission('system_prism_init_app@create', $ecapp['app_id']);
                    $prismInitStepModel->createMission('system_prism_init_app@createKey', $keyId);
                }
            }
        }

        return null;
    }

    private function __updateApi()
    {

        $prismInitStepModel = app::get('system')->model('prism_initstep');
        //要更新api了！
        $newApiJsonUrls = kernel::single('system_prism_apiJson')->getJsonUrl();
        $oldApiJsonUrls = kernel::single('system_prism_store')->get('prismApiLog');

        foreach($newApiJsonUrls as $apiKey=>$newApiJsonUrl)
        {
            //判断这个api是否导入过的
            if( !( isset($oldApiJsonUrls[$apiKey]) && $oldApiJsonUrls[$apiKey] != null ) )
            {
                $prismInitStepModel->createMission('system_prism_init_api@import', $apiKey);
                $prismInitStepModel->createMission('system_prism_init_api@setConf', $apiKey);
                $prismInitStepModel->createMission('system_prism_init_api@online', $apiKey);
            }
            elseif( $oldApiJsonUrls[$apiKey] != $newApiJsonUrl )
            {
                $prismInitStepModel->createMission('system_prism_init_api@updateUrl', $apiKey);
                $prismInitStepModel->createMission('system_prism_init_api@refresh', $apiKey);
            }
            else
            {
                $prismInitStepModel->createMission('system_prism_init_api@refresh', $apiKey);
            }

        }

        kernel::single('system_prism_store')->set('prismApiReady', $newApiJsonUrls);
        kernel::single('system_prism_store')->set('prismApiLog', $newApiJsonUrls);

        return null;
    }

    public function oauthUpdate()
    {
        kernel::single('system_prism_init_oauth')->set();
        return null;
    }

}

