<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 11/22/13
 * Time: 2:23 PM
 */

class CpTagInputWidget extends Widget {
    public function render($data) {
        // read param
        $name = $data['name'] ? $data['name'] : rand(0,100000000);
        // render
        $var = array();
        $var['name'] = $name;
        return $this->renderFile(dirname(__FILE__).'/default.html', $var);
    }
}