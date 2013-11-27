<?php
/**
 * 找人首页控制器
 * @author zivss <guolee226@gmail.com>
 * @version TS3.0
 */
class IndexAction extends Action
{
	public function _initialize() {
		$this->appCssList[] = 'emomp3.css';
	}

    public function upload() {
        $this->display();
    }

    public function doupload() {
        // write db
        $d = D('Attach')->upload();
        $success = $d['status'];
        $message = $d['info'];
        // write db
        if($success) {
            $files = $message;
            foreach($files as $file) {
                $mid = $this->mid;
                $attachid = $file['attach_id'];
                $result = D('CpAudio')->put($mid, $attachid);
                if(!$result) {
                    $success = false;
                    $message = "数据库写入错误";
                }
            }
        }
        // render
        if($success) {
            return $this->jsonsuccess("上传成功", U('emomp3/Index/listaudio'));
        } else {
            return $this->jsonerror($message);
        }
    }

    public function listaudio() {
        // read db
        $page = D('CpAudio')->getpage();
        // display
        $this->assign('page', $page);
        $this->display();
    }

    public function viewaudio() {
        $fileid = $_REQUEST['audioid'];
        $file = D('CpAudio')->get($fileid);
        $attachid = $file['attachid'];
        $audiourl = getAttachUrlByAttachId($attachid);
        $this->assign('audiourl', $audiourl);
        $this->assign('audioid', $fileid);
        $map = array();
        $map['audioid'] = $fileid;
        $page_view = D('CpTask')->where($map)->select();
        $this->assign('page_view', $page_view);
        $this->display();
    }
    public function listtask() {
        $page = D('CpTask')->getpage($this->mid);
        $this->assign('page', $page);
        //dump($page);exit;
        $this->display();

    }
    public function deleteaudio() {
        $fileid = $_REQUEST['audioid'];
        $result = D('CpAudio')->remove($fileid);
        if($result) {
            return $this->jsonsuccess('删除成功', 'refresh');
        } else {
            return $this->jsonerror('删除失败', 'refresh');
        }
    }

    public function dotask() {
        // read finish task count/remain task count
        $map = array();
        $map['uid'] = $this->mid;
        $map['finishtime'] = 0;
        $finishtaskcount = intval($_SESSION['cptask']['finishcount']);
        $remaintaskcount = D('CpAssignLink')->where($map)->count();
        // get next task
        $map = array();
        $map['uid'] = $this->mid;
        $map['finishtime'] = 0; // means not finished
        $assignlink = D('CpAssignLink')->where($map)->order('ctime asc')->limit(1)->select();
        $assignlink = $assignlink[0];
        // read task content
        if($assignlink) {
            $task = D('CpTask')->get($assignlink['taskid']);
        }
        // display
        $this->assign('task', $task);
        $this->assign('finishtaskcount', $finishtaskcount);
        $this->assign('remaintaskcount', $remaintaskcount);
        $this->display();
    }

    public function selectPublishAudio()
    {
        // read db
        $page = D('CpAudio')->getpage();
        // display
        $this->assign('page', $page);
        $this->display();
    }

    public function  newtaskstep1()
    {
        $fileid = $_REQUEST['audioid'];
        $userId = $this->mid;
        $problem = $_REQUEST['problems'];
        $result = D('CpTask')->put($userId,$fileid,$problem);
        if($result)
        {
            return $this->jsonsuccess('添加成功', 'refresh');
        }
        else
        {
            return $this->jsonsuccess('添加失败', 'refresh');
        }
    }

    public function commitanswer() {
        // get answer
        $taskid = intval($_REQUEST['taskid']);
        $answer = $_REQUEST['answer'];
        // check assign exist
        $map = array();
        $map['taskid'] = $taskid;
        $map['uid'] = $this->mid;
        $map['finishtime'] = 0;
        $count = D('CpAssignLink')->where($map)->count();
        if(!$count) {
            $this->jsonerror("taskid参数错误");
            return;
        }
        // read task
        $task = D('CpTask')->get($taskid);
        if(!$task) {
            $this->jsonerror("taskid参数错误");
            return;
        }
        // check answer correct
        foreach($task['problems'] as $i=>$e) {
            if($e['type'] == 'choice') {
                if($answer[$i] == 'undefined') {
                    $this->jsonerror("第".($i+1)."题未填写");
                    return;
                }
            } else if($e['type'] == 'tag') {
                if(!$answer[$i]) {
                    $this->jsonerror("第".($i+1)."题未填写");
                    return;
                }
            }
        }
        // write db
        $map = array();
        $map['taskid'] = $taskid;
        $map['uid'] = $this->mid;
        $map['finishtime'] = 0;
        $row = array();
        $row['answer'] = json_encode($answer);
        $row['finishtime'] = time();
        D('CpAssignLink')->where($map)->save($row);
        // increase finishcount in session
        session_start();
        $_SESSION['cptask']['finishcount']++;
        // increase user's taskcount in db
        $map = array();
        $map['uid'] = $this->mid;
        $row = array();
        $row['finishcount'] = $this->user['finishcount'] + 1;
        D('User')->where($map)->save($row);
        D('User')->cleancache($this->mid);
        // add search index
        foreach($task['problems'] as $i=>$e) {
            if($e['type'] == 'tag') {
                foreach($answer[$i] as $e2) {
                    D('CpSearch')->put($e2, $task['audioid']);
                }
            }
        }
        // return result
        $this->jsonsuccess("提交成功", 'refresh');
    }
}