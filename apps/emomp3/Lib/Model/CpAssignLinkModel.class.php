<?php

class CpAssignLinkModel extends Model {
    protected $tableName = "cpassignlink";
    protected $fields = array('id','assignid','taskid','audioid','uid','ctime','answer','finishtime');

    public function get($id) {
        $map = array();
        $map['id'] = $id;
        $result = $this->where($map)->select();
        $result = $result[0];
        $result = $this->decode($result);
        return $result;
    }

    public function getlist($taskid=null, $uid=null) {
        // limit parameter type
        $taskid = intval($taskid);
        $uid = intval($uid);
        // generate query condition
        $map = array();
        if($taskid) {
            $map['taskid'] = $taskid;
        }
        if($uid) {
            $map['uid'] = $uid;
        }
        // read db
        $result = $this->where($map)->order('ctime desc')->select();
        $result = $this->decodelist($result);
        return $result;
    }

    public function getlistbytaskid($taskid) {
        // limit parameter type
        $taskid = intval($taskid);
        // read db
        $map = array();
        $map['taskid'] = $taskid;
        $result = $this->where($map)->order('ctime desc')->select();
        // decode reuslt
        $result = $this->decodelist($result);
        // return result
        return $result;
    }

    public function getlistbyaudioid($audioid) {
        // limit parameter type
        $audioid = intval($audioid);
        // generate query condition
        $map = array();
        $map['audioid'] = $audioid;
        // read db
        $result = $this->where($map)->order('ctime desc')->select();
        $result = $this->decodelist($result);
        return $result;
    }

    public function getpage($taskid=null, $uid=null) {
        // limit parameter type
        $taskid = intval($taskid);
        $uid = intval($uid);
        // generate query condition
        $map = array();
        if($taskid) {
            $map['taskid'] = $taskid;
        }
        if($uid) {
            $map['uid'] = $uid;
        }
        // read db
        $result = $this->where($map)->order('ctime desc')->findpage();
        $result['data'] = $this->decodelist($result['data']);
        return $result;
    }

    public function put($assignid, $taskid, $audioid, $uid) {
        // limit parameter type
        $assignid = intval($assignid);
        $taskid = intval($taskid);
        $uid = intval($uid);
        // write db
        $row = array();
        $row['assignid'] = $assignid;
        $row['taskid'] = $taskid;
        $row['audioid'] = $audioid;
        $row['uid'] = $uid;
        $row['ctime'] = time();
        $row['answer'] = '';
        $row['finishtime'] = 0;
        return $this->add($row);
    }

    public function remove($id) {
        // limit parameter type
        $id = intval($id);
        // write db
        $map = array();
        $map['id'] = $id;
        return $this->where($map)->delete();
    }

    public function encode($row) {
        $row['answer'] = json_encode($row['answer']);
        return $row;
    }

    public function decode($row) {
        $row['answer'] = json_decode($row['answer'], true);
        return $row;
    }

    public function decodelist($list) {
        $result = array();
        foreach($list as $e) {
            $result[] = $this->decode($e);
        }
        return $result;
    }
}
