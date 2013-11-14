<?php
/**
 * Created by JetBrains PhpStorm.
 * User: caipeichao
 * Date: 9/5/13
 * Time: 3:55 PM
 * To change this template use File | Settings | File Templates.
 */

class CpDebugModel extends Model {
    private $logfile = '/tmp/phplog.log';

    public function dump($x) {
        $this->filedump($x);
    }

    public function filedump($x) {
        ob_start();
        dump($x);
        $message = ob_get_clean();
        $message = html_entity_decode($message);
        $this->log($message);
    }

    public function log($message) {
        $handle = fopen($this->logfile, 'a');
        fwrite($handle, $message);
        fwrite($handle, "\n");
        fclose($handle);
    }
}
