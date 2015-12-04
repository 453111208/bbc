<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_finder_builder_to_run_import {

    function run(&$cursor_id,$params){
        base_kvstore::instance($params['app'].'_'.$params['mdl'])->fetch($params['file_name'].'_sdf',$sdfContents);
       $sdfContents = unserialize( $sdfContents );
        $o = app::get($params['app'])->model($params['mdl']);
        $i = 0;
        while( $v = array_shift( $sdfContents ) ){
           if(!empty($v['store'])){
                $v['product'][0]['store'] = $v['store'];
            }
            $o->save($v);

            if( ++$i == 100 ){
                base_kvstore::instance($params['app'].'_'.$params['mdl'])->store($params['file_name'].'_sdf',serialize( $sdfContents ));
                return 1;
                break;
            }
        }
        base_kvstore::instance($params['app'].'_'.$params['mdl'])->delete($params['file_name']);
        base_kvstore::instance($params['app'].'_'.$params['mdl'])->delete($params['file_name'].'_sdf');
        base_kvstore::instance($params['app'].'_'.$params['mdl'])->delete($params['file_name'].'_error');
        return 0;
    }

    function turn_to_sdf(&$cursor_id,$params){
      base_kvstore::instance($params['app'].'_'.$params['mdl'])->fetch($params['file_name'],$contents);
        base_kvstore::instance($params['app'].'_'.$params['mdl'])->fetch($params['file_name'].'_sdf',$sdfContents);
        base_kvstore::instance($params['app'].'_'.$params['mdl'])->fetch($params['file_name'].'_error',$errorContents);
     //   base_kvstore::instance($params['app'].'_'.$params['mdl'])->fetch($params['file_name'].'_msg',$msgContents);
        $contents = unserialize( $contents );
        $sdfContents = unserialize( $sdfContents );
        $errorContents = unserialize( $errorContents );
       // $msgContents = unserialize( $msgContents );
        reset($contents);
        if(empty($contents[0][0])){
            $msg = array( 'error'=>app::get('desktop')->_('瀵煎叆鍟嗗搧涓虹┖') );
            $msgList['error'][] = $msg['error'];
            return $msgList;
        }

        $msgList = array();
       $o = app::get($params['app'])->model($params['mdl']);
       $oIo = kernel::servicelist('desktop_io');

       foreach( $oIo as $aIo ){
            if( $aIo->io_type_name == $params['file_type'] ){
                $importType = $aIo;
                break;
            }
        }
        unset($oIo);
        $objFunc = 'prepared_import_'.$importType->io_type_name.'_obj';
        $rowFunc = 'prepared_import_'.$importType->io_type_name.'_row';

        $i = 0;
        $tmpl = array();
        $tTmpl = array();
        $gTitle = array();
        $data = array();
        $tObjContent = array();
        $errorObj = false;
        
        $importType->prepared_import( $params['app'],$params['mdl'] );
        while( true ){
            $curContent = array_shift( $contents );
            $newObjFlag = false;
            $msg = '';
            $rowData = $o->$rowFunc( $curContent,$data['title'],$tmpl,$mark,$newObjFlag,$msg );
            if( $msg['error'] )$msgList['error'][] = $msg['error'];
            if( $msg['warning'] ){
                foreach( $msg['warning'] as $mk => $mv ){
                    $msgList['warning'][] = $mv;
                }
            }
            if( $newObjFlag ){
                if( $errorObj ){
                    $errorList[] = $tObjContent;
                    $errorObj = false;
                }

                $tObjContent = array();
                if( $mark != 'title' ){
                    $msg ='';

                    $saveData = $o->$objFunc( $data,$mark,$tmpl,$msg);
                    if( $msg['error'] )$msgList['error'][] = $msg['error'];
                    if( $msg['warning'] ){
                        foreach( $msg['warning'] as $mk => $mv ){
                            $msgList['warning'][] = $mv;
                        }
                    }
                   if( $saveData === false ){
                       return $msgList;
                        $errorContents[] = $gTitle;
                        foreach( $tObjContent as $ck => $cv ){
                            $errorContents[] = $cv;
                        }
                    
                    }
                    if( $saveData )
                   $sdfContents[] = $saveData;
                    if( $mark )
                        eval('$data["'.implode('"]["',explode('/',$mark)).'"] = array();');

                }else{
                    $tTmpl = $rowData;
                    $gTitle = $curContent;
                }
                /*
                if( ++$i == 100 ){
                    $rs = 1;
                    break;
                }
                 */
                $tObjContent[] = $curContent;
                if( $rowData === false ){
                    return $msgList;
                    $errorObj = true;
                }
            }
             if( $mark ){
                if( $mark == 'title' )
                    eval('$data["'.implode('"]["',explode('/',$mark)).'"] = $rowData;');
                else
                    eval('$data["'.implode('"]["',explode('/',$mark)).'"][] = $rowData;');
            }
            if( !current($contents) && current( $data['contents'] )){
               $saveData = $o->$objFunc( $data,$mark,$tmpl,$msg);
                
               if( $msg['error'] )$msgList['error'][] = $msg['error'];
               if( $msg['warning'] ){
                   foreach( $msg['warning'] as $mk => $mv ){
                           $msgList['warning'][] = $mv;
                   }
               }
               
                if( $saveData === false ){
                    return $msgList;
                    $errorContents[] = $gTitle;
                    foreach( $tObjContent as $ck => $cv ){
                        $errorContents[] = $cv;
                    }
                }
                if( $saveData )
                $sdfContents[] = $saveData;
                if( $mark )
                    eval('$data["'.implode('"]["',explode('/',$mark)).'"] = array();');
             //   break;
            }
            if( !$curContent ) break;

       }
        if( !$contents ){
            $rs = 0;
        }else{
            $contents = array_unshift( $contents,$gTitle );
        }

        base_kvstore::instance($params['app'].'_'.$params['mdl'])->store($params['file_name'],serialize($contents));
        base_kvstore::instance($params['app'].'_'.$params['mdl'])->store($params['file_name'].'_sdf',serialize($sdfContents));
        base_kvstore::instance($params['app'].'_'.$params['mdl'])->store($params['file_name'].'_error',serialize($errorContents));

        if( !$rs ){
            $params = array(
            //        'sdfdata'=>$sdfContents,
                    'app' => $params['app'],
                    'mdl' => $params['mdl'],
                    'file_name' => $params['file_name']
            );
            system_queue::instance()->publish('desktop_tasks_runimport', 'desktop_tasks_runimport', $params);
        }

        if( $msgList['error'] || $msgList['warning'] )
            return $msgList;
        return 0;
    }

    function get_import_type($file_type){

    }

}
