<?php
class system_mdl_messenger_systmpl extends dbeav_model{

    public function _file($name)
    {
        if($p = strpos($name,':'))
        {
            $type = substr($name,0,$p);
            $name=substr($name,$p+1);
            if($type=='messenger'){
                $aTmp = explode('/',$name);
                $tmpl = explode('_',$aTmp[0]);
                $app_id = $tmpl[0];
                $tmpl[0] = "view/admin";
                $html_dir = implode('/',$tmpl).'/'.$aTmp[1];
                return ROOT_DIR.'/app/'.$app_id.'/'.$html_dir.'.html';
            }
        }
        else
        {
            return ROOT_DIR.'/app/system/view/'.$name.'.html';
        }
    }

   public function get($name)
    {
        $aRet = $this->getRow('*',array('active'=>1,'tmpl_name'=>$name));
        $filemtime =  filemtime($this->_file($name));
        if($aRet['content'] && $aRet['modified_time'] >= $filemtime)
        {
            return $aRet['content'];
        }
        else
        {
            $body = file_get_contents($this->_file($name));
            $this->set($name,$body);
            return $body;
        }
    }

    public function set($name,$body)
    {
        //file_put_contents($this->_file($name),$body);
        $body = str_replace(array('&lt;{','}&gt;'),array('<{','}>'),$body);
        $body = preg_replace_callback('/<{(.+?)}>/',array(&$this,'tpl_src'),$body);
        $sdf['tmpl_name'] = $name;
        $sdf['modified_time'] = time();
        $sdf['active'] = 1;
        $sdf['content'] = $body;
        $result = $this->save($sdf);
        return $result;
    }

    function fetch($tplname,$data=null)
    {
        $content = view::make($tplname, $data)->render();
        return $content;
    }
}


