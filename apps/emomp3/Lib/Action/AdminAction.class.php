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
}