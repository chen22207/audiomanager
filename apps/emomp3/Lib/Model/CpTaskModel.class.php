<?php

class CpTaskModel extends Model {
    protected $tableName = "cptask";
    protected $fields = array('id','uid','audioid','problems','ctime');

    public function get($id) {
        // limit parameter type
        $id = intval($id);
        // read db
        $map = array();
        $map['id'] = $id;
        $result = $this->where($map)->select();
        $result = $result[0];
        $result = $this->decode($result);
        return $result;
    }

    public function getlist($uid=null) {
        // limit parameter type
        $uid = intval($uid);
        // read db
        if($uid) {
            $map = array();
            $map['uid'] = $uid;
            $result = $this->where($map)->order('ctime desc')->select();
            $result = $this->decodelist($result);
            return $result;
        } else {
            $result = $this->order('ctime desc')->select();
            $result = $this->decodelist($result);
            return $result;
        }
    }

    public function getpage($uid=null) {
        // limit parameter type
        $uid = intval($uid);
        // read db
        if($uid) {
            $map = array();
            $map['uid'] = $uid;
            $result = $this->where($map)->order('ctime desc')->findpage();
            $result['data'] = $this->decodelist($result['data']);
            return $result;
        } else {
            $result = $this->order('ctime desc')->findpage();
            $result = $this->decodelist($result['data']);
            return $result;
        }
    }

    public function put($uid, $audioid, $problems) {
        // limit parameter type
        $uid = intval($uid);
        $audioid = intval($audioid);
        // write db
        $row = array();
        $row['uid'] = $uid;
        $row['audioid'] = $audioid;
        $row['problems'] = $problems;
        $row['ctime'] = time();
        $row = $this->encodetask($row);
        return $this->add($row);
    }

    public function remove($id) {
        $map = array();
        $map['id'] = $id;
        return $this->where($map)->delete();
    }

    public function encode($row) {
        $row['problems'] = json_encode($row['problems']);
        return $row;
    }

    public function decode($row) {
        $row['problems'] = json_decode($row['problems'], true);
        return $row;
    }

    public function encodelist($list) {
        $result = array();
        foreach($result as $e) {
            $result[] = $this->encode($e);
        }
        return $result;
    }

    public function decodelist($list) {
        $result = array();
        foreach($result as $e) {
            $result[] = $this->decode($e);
        }
        return $result;
    }
}
