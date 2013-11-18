<?php

class CpSearchModel extends Model {
    protected $tableName = "cpsearch";
    protected $fields = array('id','keyword','audioid','count');

    public function get($id) {
        // limit parameter type
        $id = intval($id);
        // read db
        $map = array();
        $map['id'] = $id;
        $result = $this->where($map)->select();
        $result = $result[0];
        return $result;
    }

    public function getlist($keyword) {
        // limit parameter type
        $keyword = strval($keyword);
        // write db
        $map = array();
        $map['keyword'] = $keyword;
        return $this->where($map)->order('count desc')->select();
    }

    public function getpage($keyword) {
        // limit paramter type
        $keyword = strval($keyword);
        // read db
        $map = array();
        $map['keyword'] = $keyword;
        return $this->where($map)->order('count desc')->findpage();
    }

    public function put($keyword, $audioid) {
        // limit parameter type
        $keyword = strval($keyword);
        $audioid = intval($audioid);
        // read db
        $map = array();
        $map['keyword'] = $keyword;
        $map['audioid'] = $audioid;
        $r = $this->where($map)->select();
        $r = $r[0];
        // write db
        if(!$r) {
            $row = array();
            $row['keyword'] = $keyword;
            $row['audioid'] = $audioid;
            $row['count'] = 1;
            return $this->add($row);
        } else {
            $map = array();
            $map['id'] = $r['id'];
            $row = array();
            $row['count'] = $r['count'] + 1;
            return $this->where($map)->save($row);
        }
    }

    public function remove($id) {
        // limit parameter type
        $id = intval($id);
        // write db
        $map = array();
        $map['id'] = $id;
        return $this->where($map)->delete();
    }
}
