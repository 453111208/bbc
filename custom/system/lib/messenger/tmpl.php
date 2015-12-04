<?php
class system_messenger_tmpl{
    public function last_modified($tplname)
    {
        $systmpl = app::get('system')->model('messenger_systmpl');
        $aRet = $systmpl->getRow('*',array('active'=>1,'tmpl_name'=>$tplname));
        if($aRet){
            return $aRet['modified_time'];
        }
        return time();
    }

    public function get_file_contents($tplname)
    {
        $systmpl = app::get('system')->model('messenger_systmpl');
        $aRet = $systmpl->getRow('content',array('active'=>1,'tmpl_name'=>$tplname));
        if($aRet){
            return $aRet['content'];
        }
        return null;
    }
}


