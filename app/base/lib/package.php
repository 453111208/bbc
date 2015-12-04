<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class base_package implements Iterator{
    
    static function walk($file){
        $obj = new base_package;
        $obj->open($file);
        $obj->filename = $file;
        return $obj;
    }
    
    public function open($file){
        $this->rs = fopen($file,'rb');
        $this->maxsize = filesize($file);
    }
    
    public function rewind() {
        $this->offset = 0;
    }

    public function valid() {
        if($this->offset > $this->maxsize){
            return false;
        }
        fseek($this->rs,$this->offset);
        $this->block = fread($this->rs,512);
        if($this->block){
            if($this->block==str_repeat(chr(0),512)){
                return false;
            }else{
                return true;
            }
        }
        return true;
    }

    public function current() {
        $data = unpack('a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1typeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor', $this->block);
        $file = array();
        $file['name'] = trim($data['filename']); 
        $file['mode'] = OctDec(trim($data['mode'])); 
        $file['uid'] = OctDec(trim($data['uid'])); 
        $file['gid'] = OctDec(trim($data['gid'])); 
        $file['size'] = OctDec(trim($data['size'])); 
        $file['mtime'] = OctDec(trim($data['mtime'])); 
        $file['checksum'] = OctDec(trim($data['checksum']));
        if($file['checksum']!=$this->checksum($this->block)){
            trigger_error('Bad tar format: '.$this->filename,E_USER_ERROR);
            return false;
        }
        
        $file['type'] = $data['typeflag'];
        $file['offset'] = $this->offset+512;
        $file['rs'] = $this->rs;
        $this->item = $file;
        $this->block = null;
        return $this->item;
    }

    public function key() {
        return $this->item['name'];
    }

    public function next() {
        $this->offset += 512 + (ceil($this->item['size'] / 512 ) * 512);
        return 1;
    }
    
    private function checksum($bytestring) {
        $unsigned_chksum = 0;
        for($i=0; $i<512; $i++)
            $unsigned_chksum += ord($bytestring[$i]);
        for($i=0; $i<8; $i++)
            $unsigned_chksum -= ord($bytestring[148 + $i]);
        $unsigned_chksum += ord(" ") * 8;

        return $unsigned_chksum;
    }
    
    static function extra($file,$dir){
        $filename = $dir.'/'.$file['name'];
        $dirname = dirname($filename);
        if(!file_exists($dirname)){
            utils::mkdir_p($dirname);
        }
        if($file['type']=='0'){
            fseek($file['rs'],$file['offset']);
            $rs = fopen($filename,'w');
            $output = 0;
            while($output<$file['size']){
                $read = min($file['size']-$output,1024);
                fputs($rs,fread($file['rs'],$read));
                $output+=$read;
            }
            fclose($rs);
            touch($filename,$file['mtime']);
        }
    }
    
}
