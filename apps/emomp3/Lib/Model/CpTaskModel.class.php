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
            $result = $this->where($map)->order('ctime desc')->cpfindpage();
            $result['data'] = $this->decodelist($result['data']);
            return $result;
        } else {
            $result = $this->order('ctime desc')->cpfindpage();
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
        if(isset($row['problems'])) {
            $row['problems'] = json_encode($row['problems']);
        }
        if(isset($row['audioids'])) {
            $row['audioids'] = json_encode($row['audioids']);
        }
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

    public function settaskaudios($taskid, $audioids) {
        // unique audioids
        $audioids = array_unique($audioids);
        // write database
        $map = array();
        $map['id'] = $taskid;
        $row = array();
        $row['audioids'] = $audioids;
        $row = $this->encode($row);
        return $this->where($map)->save($row);
    }

    /**
     * 获取任务完成的进度
     * 返回结果取值范围是[0, 1]
     * @param $taskid
     * @param null $uid
     */
    public function getprogress($assignid, $taskid, $uid=null) {
        // 限制参数类型
        $assignid = intval($assignid);
        $taskid = intval($taskid);
        if($uid) {
            $uid = intval($uid);
        }
        // 获取任务总数和完成数量
        if($uid) {
            // 获取任务总数
            $map = array();
            $map['assignid'] = $assignid;
            $map['taskid'] = $taskid;
            $map['uid'] = $uid;
            $totalcount = D('CpAssignLink')->where($map)->count();
            // 获取完成的任务数量
            $map = array();
            $map['assignid'] = $assignid;
            $map['taskid'] = $taskid;
            $map['uid'] = $uid;
            $map['finishtime'] = array('NEQ', 0);
            $finishcount = D('CpAssignLink')->where($map)->count();
        } else {
            // 获取任务数量
            $map = array();
            $map['assignid'] = $assignid;
            $map['taskid'] = $taskid;
            $totalcount = D('CpAssignLink')->where($map)->count();
            // 获取完成的任务数量
            $map = array();
            $map['assignid'] = $assignid;
            $map['taskid'] = $taskid;
            $map['finishtime'] = array('NEQ', 0);
            $finishcount = D('CpAssignLink')->where($map)->count();
        }
        // 计算进度百分比
        if($finishcount == $totalcount) {
            return 1;
        } else {
            return $finishcount / $totalcount;
        }
    }
}
