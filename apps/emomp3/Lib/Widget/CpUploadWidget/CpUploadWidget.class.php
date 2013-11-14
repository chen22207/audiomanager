<?php

class CpUploadWidget extends Widget {
    public function render($data) {
        // get param
        $template = $data['tpl'] ? $data['tpl'] : 'default';
        $name = $data['name'] ? $data['name'] : 'a'.rand(0,100000000).'b'.rand(0,1000000000);
        // render
        $var['name'] = $name;
        return $this->renderFile(dirname(__FILE__)."/$template.html", $var);
    }

    public function upload() {
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
