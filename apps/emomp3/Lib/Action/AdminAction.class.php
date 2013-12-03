<?php
/**
 * 找人首页控制器
 * @author zivss <guolee226@gmail.com>
 * @version TS3.0
 */

require_once(SITE_PATH.'/getid3/getid3/getid3.php');

class AdminAction extends Action
{
	public function _initialize() {
		$this->appCssList[] = 'emomp3.css';
	}

    public function listaudio() {
        // read db
        $page = D('CpAudio')->getpage();
        // display
        $this->assign('page', $page);
        $this->display();
    }

    public function viewaudio() {
        // get param
        $audioid = intval($_REQUEST['audioid']);
        // read db
        $audio = D('CpAudio')->get($audioid);
        // get audio file
        $attach = D('Attach')->getattachbyid($audio['attachid']);
        $audiofile = SITE_PATH.'/data/upload/'.$attach['save_path'].$attach['save_name'];
        // get audio length
        $getid3 = new GetID3();
        $audioinfo = $getid3->analyze($audiofile);
        // display
        $this->assign('audio', $audio);
        $this->assign('audiolength', $audioinfo['playtime_string']);
        $this->display();
    }

    public function listtask() {
        // read db
        $tasks = D('CpTask')->getpage();
        // display
        $this->assign('tasks', $tasks);
        $this->display();
    }

    public function deletetask() {
        // get parameters
        $taskid = intval($_REQUEST['taskid']);
        // ensure task not assigned
        $map = array();
        $map['taskid'] = $taskid;
        $count = D('CpAssignLink')->where($map)->count();
        if($count) {
            $this->jsonerror("删除失败，因为任务已经分配");
            return;
        }
        // write db
        $result = D('CpTask')->remove($taskid);
        // return result
        if($result) {
            $this->jsonsuccess('删除成功', 'refresh');
            return;
        } else {
            $this->jsonerror('删除失败，写入数据库错误');
            return;
        }
    }

    public function viewtask() {
        // get parameter
        $taskid = intval($_REQUEST['taskid']);
        // read database
        $task = D('CpTask')->get($taskid);
        // display
        $this->assign('task', $task);
        $this->display();
    }

    public function assigntask() {
        //第一步：选择要分配的任务
        // read db
        $tasks = D('CpTask')->getpage();
        // clear session
        session_start();
        unset($_SESSION['cpassign']);
        // display
        $this->assign('tasks', $tasks);
        $this->display();
    }

    public function assigntaskstep2() {
        //第二步：指定人员
        // get taskid from parameter
        $taskid = intval($_REQUEST['taskid']);
        // read session
        $uids = $_SESSION['cpassign']['uids'];
        // read users
        $users = D('User')->findpage();
        // display
        $this->assign('uids', $uids);
        $this->assign('users', $users);
        $this->assign('taskid', $taskid);
        $this->display();
    }

    public function addassignuser() {
        //get parameter
        $uids = $_REQUEST['uids'];
        if(!$uids) {
            $uids = array();
        }
        //merge uids
        $uids2 = $_SESSION['cpassign']['uids'];
        if($uids2) {
            $uids = array_merge($uids, $uids2);
        }
        $uids = array_unique($uids);
        //write session
        session_start();
        $_SESSION['cpassign']['uids'] = $uids;
        //return success
        $this->jsonsuccess('添加成功', 'refresh');
    }

    public function commitassigntask() {
        // get parameters
        $taskid = intval($_REQUEST['taskid']);
        $uid = $this->mid;
        // read assign target users
        $uids = $_SESSION['cpassign']['uids'];
        // add assignment to database
        $assignid = D('CpAssign')->put($uid, $taskid, $uids);
        // add assignment link to database
        $task = D('CpTask')->get($taskid);
        foreach($uids as $e) {
            foreach($task['audioids'] as $audioid) {
                D('CpAssignLink')->put($assignid, $taskid, $audioid, $e);
            }
        }
        // return success
        $this->jsonsuccess('分配成功', U('emomp3/Admin/listassign'));
        return;
    }

    public function listassign() {
        // read db
        $assignlist = D('CpAssign')->getpage();
        // display
        $this->assign('assignlist', $assignlist);
        $this->display();
    }

    public function viewassign() {
        // get parameter
        $assignid = intval($_REQUEST['assignid']);
        // read db
        $assign = D('CpAssign')->get($assignid);
        // display
        $this->assign('assign', $assign);
        $this->display();
    }

    public function search() {
        $this->display();
    }

    public function dosearch() {
        //get keyword
        $keyword = strval($_REQUEST['keyword']);
        //read db
        $result = D('CpSearch')->getpage($keyword);
        //display
        $this->assign('result', $result);
        $this->assign('keyword', $keyword);
        $this->display();
    }
}