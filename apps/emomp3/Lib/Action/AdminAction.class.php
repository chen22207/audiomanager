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
}