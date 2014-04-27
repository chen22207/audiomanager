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

    public function index(){
        $this->display();
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

    public function newtask(){
        redirect(U('emomp3/Index/newtaskstep1'));
    }

    public function listaudio() {
        // read db
        $page = D('CpAudio')->getpage($this->mid);
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

    public function selecttasktodo() {
        $uid = $this->mid;
        // read task list
        $tasklist = D('CpUserTask')->getpagebyuid($uid);
        // display page
        $this->assign('tasklist', $tasklist);
        $this->display();
    }

    public function dotask() {
        // get parameters
        $assignid = intval($_REQUEST['assignid']);
        $taskid = intval($_REQUEST['taskid']);
        // read finish task count/remain task count
        $map = array();
        $map['uid'] = $this->mid;
        $map['assignid'] = $assignid;
        $map['taskid'] = $taskid;
        $map['finishtime'] = 0;
        $finishtaskcount = intval($_SESSION['cptask']['finishcount']);
        $remaintaskcount = D('CpAssignLink')->where($map)->count();
        // get next task
        $map = array();
        $map['uid'] = $this->mid;
        $map['assignid'] = $assignid;
        $map['taskid'] = $taskid;
        $map['finishtime'] = 0; // means not finished
        $assignlink = D('CpAssignLink')->where($map)->order('ctime asc')->limit(1)->select();
        $assignlink = $assignlink[0];
        // read task content
        if($assignlink) {
            $task = D('CpTask')->get($assignlink['taskid']);
        }
        // display
        $this->assign('task', $task);
        $this->assign('audioid', $assignlink['audioid']);
        $this->assign('finishtaskcount', $finishtaskcount);
        $this->assign('remaintaskcount', $remaintaskcount);
        $this->display();
    }

    public function newtaskstep1() {
        $this->display();
    }

    public function donewtaskstep1() {
        $problems = $_REQUEST['problems'];
        // write db
        $taskid = D('CpTask')->put($this->mid, array(), $problems);
        if(!$taskid) {
            $this->jsonerror("写入数据库错误");
            return;
        }
        // goto step2
        $this->jsonsuccess(null, U('emomp3/Index/newtaskstep2', array('taskid'=>$taskid)));
    }

    public function newtaskstep2() {
        $taskid = intval($_REQUEST['taskid']);
        // display
        $this->assign('taskid', $taskid);
        $this->display();
    }

    public function addaudiototask() {
        $taskid = intval($_REQUEST['taskid']);
        $audioids = $_REQUEST['audioids'];
        // write db
        D('CpTask')->addaudiototask($audioids, $taskid);
        // return success
        $this->jsonsuccess("添加成功，可以继续添加", "refresh");
    }

    public function settaskaudio() {
        $taskid = intval($_REQUEST['taskid']);
        $add = $_REQUEST['add'];
        $remove = $_REQUEST['remove'];
        //read audios in task
        $task = D('CpTask')->get($taskid);
        $audioids = $task['audioids'];
        //add audios
        foreach($add as $e) {
            $audioids[] = $e;
        }
        //remove audios
        $audioids = array_intersect($audioids, array_diff($audioids, $remove));
        //write audios to task
        D('CpTask')->settaskaudios($taskid, $audioids);
        //return success
        $url = U('emomp3/Admin/assigntaskstep2', array('taskid'=>$taskid));
        return $this->jsonsuccess("操作成功", U('emomp3/Index/viewtask', array('taskid'=>$taskid)));
    }

    public function commitanswer() {
        // get answer
        $taskid = intval($_REQUEST['taskid']);
        $audioid = intval($_REQUEST['audioid']);
        $answer = $_REQUEST['answer'];
        // check assign exist
        $map = array();
        $map['taskid'] = $taskid;
        $map['audioid'] = $audioid;
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
        // write answer to db
        $map = array();
        $map['taskid'] = $taskid;
        $map['audioid'] = $audioid;
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
        $row['taskcount'] = $this->user['taskcount'] + 1;
        D('User')->where($map)->save($row);
        D('User')->cleancache($this->mid);
        // add search index
        foreach($task['problems'] as $i=>$e) {
            if($e['type'] == 'tag') {
                foreach($answer[$i] as $e2) {
                    D('CpSearch')->put($e2, $audioid);
                }
            } else if($e['type'] == 'choice') {
                $answername = $e['choices'][$answer[$i]];
                D('CpSearch')->put($answername, $audioid);
            }
        }
        // return result
        $this->jsonsuccess("提交成功", 'refresh');
    }

    public function viewtask() {
        $taskid = intval($_REQUEST['taskid']);
        // 读取数据库
        $task = D('CpTask')->get($taskid);
        // 显示
        $this->assign('task', $task);
        $this->display();
    }

    public function listfinishedtask() {
        //read db
        $map = array();
        $map['finishtime'] = array('NEQ', 0);
        $map['uid'] = $this->mid;
        $assignlinks = D('CpAssignLink')->where($map)->order('finishtime desc')->findpage();
        // display
        $this->assign('assignlinks', $assignlinks);
        $this->display();
    }

    public function viewfinishedtask() {
        //get parameter
        $linkid = intval($_REQUEST['linkid']);
        //read db
        $assignlink = D('CpAssignLink')->get($linkid);
        $task = D('CpTask')->get($assignlink['taskid']);
        $audio = D('CpAudio')->get($assignlink['audioid']);
        //get audio url
        $audiourl = getattachurlbyattachid($audio['attachid']);
        //display
        $this->assign('assignlink', $assignlink);
        $this->assign('task', $task);
        $this->assign('audio', $audio);
        $this->assign('audiourl', $audiourl);
        $this->display();
    }

    public function profile() {
        //display
        $this->assign('user', $this->user);
        $this->display();
    }
}