<?php

class CpFileListWidget extends Widget {
    public function render($data) {
        // get param
        $template = $data['tpl'] ? $data['tpl'] : 'default';
        $name = $data['name'] ? $data['name'] : 'a'.rand(0,100000000).'b'.rand(0,1000000000);
        // read db
        $files = D('CpFiles')->getpage($this->mid);
        $files['data'] = D('CpUtil')->adduserinfo($files['data']);
        // render
        $var['name'] = $name;
        $var['files'] = $files;
        return $this->renderFile(dirname(__FILE__)."/$template.html", $var);
    }

    public function delete() {

    }
}
