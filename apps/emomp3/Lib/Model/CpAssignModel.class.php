<?php

class CpAssignModel extends Model {
    protected $tableName = "cpassign";
    protected $fields = array('id','uid','assignids', 'uids', 'ctime');

    public function get($id) {
        // limit parameter type
        $id = intval($id);
        // query condition
        $map = array();
        if($id) {
            $map['id'] = $id;
        }
        // read db
        $result = $this->where($map)->select();
        $result = $result[0];
        $result = $this->decode($result);
        return $result;
    }

    public function getlist($uid=null) {
        // limit parameter type
        $uid = intval($uid);
        // query condition
        $map = array();
        if($uid) {
            $map['uid'] = $uid;
        }
        // read db
        $result = $this->where($map)->order('ctime desc')->select();
        // decode result
        $result = $this->decodelist($result);
        // return
        return $result;
    }

    public function getpage($uid=null) {
        // limit parameter type
        $uid = intval($uid);
        // query condition
        $map = array();
        if($uid) {
            $map['uid'] = $uid;
        }
        // read db
        $result = $this->where($map)->order('ctime desc')->findpage();
        $result['data'] = $this->decodelist($result['data']);
        return $result;
    }

    public function put($uid, $assignids, $uids) {
        // limit parameter type
        $uid = intval($uid);
        $assignids = $this->intarray($uid);
        $uids = $this->intarray($uids);
        // encode row
        $row = array();
        $row['uid'] = $uid;
        $row['assignids'] = $assignids;
        $row['uids'] = $uids;
        $row['ctime'] = time();
        $row = $this->encode($row);
        // write db
        return $this->add($row);
    }

    public function getjoincount($assignid) {
        $row = $this->get($assignid);
        $uids = array_unique($row['uids']);
        return sizeof($uids);
    }

    public function getfinishcount($assignid) {
        $row = $this->get($assignid);
        // get uids
        $uids = $row['uids'];
        // statistic not finished count
        $unfinished = 0;
        foreach($uids as $uid) {
            $map = array();
            $map['uid'] = $uid;
            $map['assignid'] = $row['id'];
            $r = D('CpAssignLink')->where($map)->limit(1)->select();
            if($r) {
                $unfinished++;
            }
        }
        // return result
        $totalcount = sizeof($uids);
        return $totalcount - $unfinished;
    }

    private function encode($row) {
        $row['assignids'] = json_encode($row['assignids']);
        $row['uids'] = json_encode($row['uids']);
        return $row;
    }

    private function decode($row) {
        $row['assignids'] = json_decode($row['assignids'], 'json');
        $row['uids'] = json_decode($row['uids'], 'json');
        return $row;
    }

    private function decodelist($list) {
        $result = array();
        foreach($list as $e) {
            $result[] = $this->decode($e);
        }
        return $result;
    }
}
