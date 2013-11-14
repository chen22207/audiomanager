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

    public function delete() {
        $fileid = $_POST['fileid'];
        $result = D('CpFiles')->remove($fileid);
        if($result) {
            $this->ajaxReturn("删除成功", null, 1);
        } else {
            $this->ajaxReturn("删除失败", null, 0);
        }
    }

    public function view() {
        $fileid = $_REQUEST['id'];
        $file = D('CpFiles')->get($fileid);
        $attachid = $file['attach_id'];
        $audiourl = getAttachUrlByAttachId($attachid);
        $this->assign('audiourl', $audiourl);
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
                $result = model('CpFiles')->put($mid, $attachid);
                if(!$result) {
                    $success = false;
                    $message = "数据库写入错误";
                }
            }
        }
        // render
        if($success) {
            return D('CpUtil')->ajax(1, "上传成功");
        } else {
            return D('CpUtil')->ajax(0, $message);
        }
    }
}