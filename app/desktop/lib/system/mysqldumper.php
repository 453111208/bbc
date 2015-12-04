<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

class desktop_system_mysqldumper {
    public $_isDroptables;
    public $tableid;    //数据表ID
    public $startid;

    public function __construct() {
    }


    /*
     * @params $app 所属app
     * @params $bakdir 备份临时文件夹
     */
    public function multi_dump_sdf($appId, $bakdir)
    {
        $dirname = $bakdir .'/sdf';
        $dbschema_dirname = $bakdir .'/dbschema';
        is_dir($dirname) or mkdir( $dirname,0755,true );
        is_dir($dbschema_dirname) or mkdir( $dbschema_dirname,0755,true );

        $appIds = array_column(app::get('base')->database()->executeQuery('SELECT app_id FROM base_apps WHERE status=?', ['active'])->fetchAll(), 'app_id');


        if ($appId)
        {
            $appIds = array_slice($appIds, array_flip($appIds)[$appId]);
            $nextAppId = next($appIds);
        }
        else
        {
            $appId = current($appIds);
            $nextAppId = next($appIds);
        }

        if ($appId === false) return false;

        if(is_dir(APP_DIR . '/' . $appId .'/dbschema'))
        {
            foreach(with(new base_application_dbtable)->detect($appId) as $item)
            {
                //echo $item->key();
                $columnDefine = $item->load();
                $this->dump_data($dirname, $appId, $item->key());
            }
            utils::cp( APP_DIR .'/'. $appId .'/dbschema',$dbschema_dirname.'/'.$appId);
        }
        
        return $nextAppId;
    }
    
    
    
    private function dump_data( $dirname, $app, $model ) {
        $len = 10;
        $cols = $startid = $filesize = 0;
        while(true) {
            
            $bakfile = $this->get_bak_file( $app,$model,$cols );
            //            echo $model.'---';
            $tname = "{$app}_{$model}";

            $qb = app::get($app)->database()->createQueryBuilder()->select('*')->from($tname);
            if( strtolower($app)=='base' && strtolower($model)=='kvstore' )
            {
                $qb->where($qb->expr()->notLike('prefix', $qb->getConnection()->quote('cache/%')));
            }
             $aData = $qb->setFirstResult($startid)->setMaxResults($len)->execute()->fetchAll();
        
             $db = require(APP_DIR . "/{$app}/dbschema/{$model}.php");
            
            if(empty($aData)) { $startid=0; break; }
            
            foreach($aData as $row) {
                foreach( $row as $key => &$val )
                {
                    if( $db['columns'][$key]['type']=='serialize' )
                    {
                        $val = unserialize($val);
                    }
                    elseif ($tmpVal = unserialize($val))
                    {
                        $val = $tmpVal;
                    }
                }
                
                $i_str = serialize($row);
                $filesize += strlen($i_str);
                
                $this->write( $dirname,$bakfile,$i_str."\r\n" );
                if( $filesize>1024*800 ) {
                    $cols++;
                    $bakfile = $this->get_bak_file( $app,$model,$cols );
                    $filesize = 0;
                }
                $startid++;
            }
            
            if( count($aData)<$len ) { $startid=0; break; }
        }
    }
    
    
    private function write( $dirname, $bakfile, $str ) {
        $fp = fopen($dirname.'/'.$bakfile, 'a+');
        fwrite( $fp, $str );
        fclose($fp);
    }
    
    private function get_bak_file( $app,$model,$cols ) {
        $ext = 'sdf';
        if( empty($cols) ) {
            return "{$app}.{$model}.{$ext}";
        } else {
            return "{$app}.{$model}.{$cols}.{$ext}";
        }
    }


    //截最后一个是否是半个UTF-8中文
    public function utftrim($str)
    {
        $found = false;
        for($i=0;$i<4&&$i<strlen($str);$i++)
        {
            $ord = ord(substr($str,strlen($str)-$i-1,1));
            //UTF-8中文分{四/三/二字节码},第一位分别为11110xxx(>192),1110xxxx(>192),110xxxxx(>192);接下去的位数都是10xxxxxx(<192)
            //其他ASCII码都是0xxxxxxx
            if($ord> 192)
            {
                $found = true;
                break;
            }
            if ($i==0 && $ord < 128){
                break;
            }
        }

        if($found)
        {
            if($ord>240)
            {
                if($i==3) return $str;
                else return substr($str,0,strlen($str)-$i-1);
            }
            elseif($ord>224)
            {
                if($i>=2) return $str;
                else return substr($str,0,strlen($str)-$i-1);
            }
            else
            {
                if($i>=1) return $str;
                else return substr($str,0,strlen($str)-$i-1);
            }
        }
        else return $str;
    }
    
}
