<?php

class CpAudioModel extends Model {
    protected $tableName = "cpaudio";
    protected $fields = array('id','uid','attachid','ctime');

    public function get($id) {
        $map = array();
        $map['id'] = $id;
        $result = $this->where($map)->select();
        return $result[0];
    }

    public function put($uid, $attachid) {
        $row['uid'] = $uid;
        $row['attachid'] = $attachid;
        $row['ctime'] = time();
        return $this->add($row);
    }

    public function getlist($uid=null) {
        $uid = intval($uid);
        // read db
        if($uid) {
            $map = array();
            $map['uid'] = $uid;
            return $this->where($map)->order('ctime desc')->select();
        } else {
            return $this->order('ctime desc')->select();
        }
    }

    public function getpage($uid=null) {
        $uid = intval($uid);
        // read db
        if($uid) {
            $map = array();
            $map['uid'] = $uid;
            return $this->where($map)->order('ctime desc')->findpage();
        } else {
            return $this->order('ctime desc')->findpage();
        }
    }

    public function remove($audioid) {
        $map = array();
        $map['id'] = $audioid;
        return $this->where($map)->delete();
    }
}
