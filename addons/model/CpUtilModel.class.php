<?php
/**
 * Created by JetBrains PhpStorm.
 * User: caipeichao
 * Date: 9/24/13
 * Time: 7:31 PM
 * To change this template use File | Settings | File Templates.
 */

class CpUtilModel extends Model {
    public function ajax($success, $message) {
        $json['success'] = $success;
        $json['message'] = $message;
        $json = json_encode($json);
        header('content-type: application/json');
        echo $json;
    }

    public function adduserinfo($files, $key='uid') {
        foreach($files as $file) {
            $uid = $file[$key];
            $user = D('User')->getuserinfo($uid);
            $file['user'] = $user;
            $result[] = $file;
        }
        return $result;
    }
}