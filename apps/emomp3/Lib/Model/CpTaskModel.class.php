<?php

class CpTaskModel extends Model {
    protected $tableName = "cptask";
    protected $fields = array('id','uid','audioids','problems','ctime');

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
            $result['data'] = $this->decodelist($result['data']);
            return $result;
        }
    }

    public function put($uid, $audioids, $problems) {
        // limit parameter type
        $uid = intval($uid);
        // write db
        $row = array();
        $row['uid'] = $uid;
        $row['audioids'] = $audioids;
        $row['problems'] = $problems;
        $row['ctime'] = time();
        $row = $this->encode($row);
        return $this->add($row);
    }

    public function remove($id) {
        $map = array();
        $map['id'] = $id;
        return $this->where($map)->delete();
    }

    public function encode($row) {
        $row['problems'] = json_encode($row['problems']);
        $row['audioids'] = json_encode($row['audioids']);
        return $row;
    }

    public function decode($row) {
        $row['problems'] = json_decode($row['problems'], true);
        $row['audioids'] = json_decode($row['audioids'], true);
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
        foreach($list as $e) {
            $result[] = $this->decode($e);
        }
        return $result;
    }

    public function addaudiototask($audioids, $taskid) {
        //读取数据库中对应的任务
        $task = $this->get($taskid);
        // 将音频添加到任务中
        $task['audioids'] = array_merge($task['audioids'], $audioids);
        $task['audioids'] = array_unique($task['audioids']);
        $task = $this->encode($task);
        // 写入数据库
        unset($task['id']);
        $map = array();
        $map['id'] = $taskid;
        return $this->where($map)->save($task);
    }
}
