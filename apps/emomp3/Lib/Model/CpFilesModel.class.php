<?php

class CpFilesModel extends Model {
    protected $tableName = "emo_files";
    protected $fields = array('emo_files_id','uid','attach_id','ctime');
    
    public function put($uid, $attachid) {
        $row['uid'] = $uid;
        $row['attach_id'] = $attachid;
        $row['ctime'] = time();
        return $this->add($row);
    }

    public function getpage($uid=null) {
        $map = array();
        if($uid != null) {
            $map['uid'] = $uid;
        }
        $result = $this->where($map)->order('ctime desc')->findpage();
        return $result;
    }

    public function remove($fileid) {
        $map['emo_files_id'] = $fileid;
        $result = $this->where($map)->delete();
        return $result;
    }

    public function get($fileid) {
        $map['emo_files_id'] = $fileid;
        $result = $this->where($map)->select();
        return $result[0];
    }
}
