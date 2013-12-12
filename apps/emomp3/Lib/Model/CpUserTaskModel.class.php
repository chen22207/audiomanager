<?php

class CpUserTaskModel extends Model {
    protected $tableName = "cpusertask";
    protected $fields = array('id','uid','taskid','assignid','ctime');

    public function get($id) {
        $id = intval($id);
        $map = array();
        $map['id'] = $id;
        $result = $this->where($map)->select();
        return $result[0];
    }

    public function getlist() {
        $result = $this->order('ctime desc')->select();
        return $result;
    }

    public function getpage() {
        $result = $this->order('ctime desc')->findpage();
        return $result;
    }

    public function getlistbyuid($uid) {
        $uid = intval($uid);
        $map = array();
        $map['uid'] = $uid;
        $result = $this->where($map)->order('ctime desc')->select();
        return $result;
    }

    public function getpagebyuid($uid) {
        $uid = intval($uid);
        $map = array();
        $map['uid'] = $uid;
        $result = $this->where($map)->order('ctime desc')->findpage();
        return $result;
    }

    public function getlistbytaskid($taskid) {
        $taskid = intval($taskid);
        $map = array();
        $map['taskid'] = $taskid;
        $result = $this->where($map)->order('ctime desc')->select();
        return $result;
    }

    public function getpagebytaskid($taskid) {
        $taskid = intval($taskid);
        $map = array();
        $map['taskid'] = $taskid;
        $result = $this->where($map)->order('ctime desc')->findpage();
        return $result;
    }

    public function put($uid, $taskid, $assignid) {
        $uid = intval($uid);
        $taskid = intval($taskid);
        $row = array();
        $row['uid'] = $uid;
        $row['taskid'] = $taskid;
        $row['assignid'] = $assignid;
        $row['ctime'] = time();
        return $this->add($row);
    }

    public function remove($id) {
        $id = intval($id);
        $map = array();
        $map['id'] = $id;
        return $this->where($map)->delete();
    }
}
