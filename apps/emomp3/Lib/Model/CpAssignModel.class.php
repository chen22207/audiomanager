<?php

class CpAssignModel extends Model
{
    protected $tableName = "cpassign";
    protected $fields = array('id', 'uid', 'taskid', 'uids', 'ctime');

    public function get($id)
    {
        // limit parameter type
        $id = intval($id);
        // query condition
        $map = array();
        if ($id) {
            $map['id'] = $id;
        }
        // read db
        $result = $this->where($map)->select();
        $result = $result[0];
        $result = $this->decode($result);
        return $result;
    }

    public function getlist($uid = null)
    {
        // limit parameter type
        $uid = intval($uid);
        // query condition
        $map = array();
        if ($uid) {
            $map['uid'] = $uid;
        }
        // read db
        $result = $this->where($map)->order('ctime desc')->select();
        // decode result
        $result = $this->decodelist($result);
        // return
        return $result;
    }

    public function getpage($uid = null)
    {
        // limit parameter type
        $uid = intval($uid);
        // query condition
        $map = array();
        if ($uid) {
            $map['uid'] = $uid;
        }
        // read db
        $result = $this->where($map)->order('ctime desc')->cpfindpage();
        $result['data'] = $this->decodelist($result['data']);
        return $result;
    }

    public function put($uid, $taskid, $uids)
    {
        // limit parameter type
        $uid = intval($uid);
        $taskid = intval($taskid);
        $uids = $this->intarray($uids);
        // encode row
        $row = array();
        $row['uid'] = $uid;
        $row['taskid'] = $taskid;
        $row['uids'] = $uids;
        $row['ctime'] = time();
        $row = $this->encode($row);
        // write db
        return $this->add($row);
    }

    public function getjoincount($assignid)
    {
        $row = $this->get($assignid);
        $uids = array_unique($row['uids']);
        return sizeof($uids);
    }

    public function getfinishcount($assignid)
    {
        $row = $this->get($assignid);
        // get uids
        $uids = $row['uids'];
        // statistic not finished count
        $unfinished = 0;
        foreach ($uids as $uid) {
            $map = array();
            $map['uid'] = $uid;
            $map['assignid'] = $row['id'];
            $r = D('CpAssignLink')->where($map)->limit(1)->select();
            if ($r) {
                $unfinished++;
            }
        }
        // return result
        $totalcount = sizeof($uids);
        return $totalcount - $unfinished;
    }

    public function getProgressByUid($assign_id, $uid)
    {
        // 获取任务总数
        $map = array(
            'assignid' => $assign_id,
            'uid' => $uid,
        );
        $model = D('CpAssignLink');
        $total_count = $model->where($map)->count();

        // 获取完成总数
        $map = array(
            'assignid' => $assign_id,
            'uid' => $uid,
            'finishtime' => array('NEQ', 0),
        );
        $finish_count = D('CpAssignLink')->where($map)->count();

        // 返回完成比例
        $result = $finish_count / $total_count;
        return $result;
    }

    public function getprogress($assignid)
    {
        //get total count
        $map = array();
        $map['assignid'] = $assignid;
        $totalcount = D('CpAssignLink')->where($map)->count();
        //get finish count
        $map = array();
        $map['assignid'] = $assignid;
        $map['finishtime'] = 0;
        $unfinishcount = D('CpAssignLink')->where($map)->count();
        $finishcount = $totalcount - $unfinishcount;
        //return progress
        if ($totalcount == $finishcount) {
            return 1;
        } else {
            return $finishcount / $totalcount;
        }
    }

    private function encode($row)
    {
        $row['uids'] = json_encode($row['uids']);
        return $row;
    }

    private function decode($row)
    {
        $row['uids'] = json_decode($row['uids'], 'json');
        return $row;
    }

    private function decodelist($list)
    {
        $result = array();
        foreach ($list as $e) {
            $result[] = $this->decode($e);
        }
        return $result;
    }

    public function getlistbytaskid($taskid)
    {
        // limit parameter type
        $taskid = intval($taskid);
        // read db
        $map = array();
        $map['taskid'] = $taskid;
        $result = $this->where($map)->order('ctime desc')->select();
        // decode result
        $result = $this->decodelist($result);
        // return result
        return $result;
    }

    public function intarray($a)
    {
        $result = array();
        foreach ($a as $e) {
            $result[] = intval($e);
        }
        return $result;
    }
}
