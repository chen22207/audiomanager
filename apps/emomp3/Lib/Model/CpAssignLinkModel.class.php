<?php

class CpAssignLinkModel extends Model {
    protected $tableName = "cpassignlink";
    protected $fields = array('id','assignid','taskid','uid','ctime','answer','finishtime');

    public function get($id) {
        $map = array();
        $map['id'] = $id;
        $result = $this->where($map)->select();
        $result = $result[0];
        $result = $this->decode($result);
        return $result;
    }
//ceshi
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

    public function put($assignid, $taskid, $uid) {
        // limit parameter type
        $assignid = intval($assignid);
        $taskid = intval($taskid);
        $uid = intval($uid);
        // write db
        $row = array();
        $row['assignid'] = $assignid;
        $row['taskid'] = $taskid;
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
        foreach($result as $e) {
            $result[] = $this->decode($list);
        }
        return $result;
    }
}
