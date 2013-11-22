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
        $attachid = $file['attach_id'];
        $audiourl = getAttachUrlByAttachId($attachid);
        $this->assign('audiourl', $audiourl);
        $this->assign('audioid', $fileid);
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
}