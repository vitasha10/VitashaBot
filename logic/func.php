<?php
require_once 'logic/random.php';
$RandDotOrg = new random();

class func
{
    public function reboot(){
        //exec ("/sbin/reboot");
        $stop = false;
        /**
         * pcntl_fork() - данная функция разветвляет текущий процесс
         */
        $pid = pcntl_fork();
        if ($pid == -1) {
            /**
             * Не получилось сделать форк процесса, о чем сообщим в консоль
             */
            die('Error fork process' . PHP_EOL);
        } else if ($pid) {
            /**
             * В эту ветку зайдет только родительский процесс, который мы убиваем и сообщаем об этом в консоль
             */
            die('Die parent process' . PHP_EOL);
        } else {
            /**
             * Бесконечный цикл
             */
            while(!$stop) {
                sleep(1);
                $stop = true;
                exec ("/sbin/reboot");
                
            }
        }
        /**
         * Установим дочерний процесс основным, это необходимо для создания процессов
         */
        posix_setsid();
        exit;
    }
    public function coin(){
        global $RandDotOrg;
        return $RandDotOrg->get_integers(1,1,2);
    }
    public function random($one,$two){
        global $RandDotOrg;
        return $RandDotOrg->get_integers(1,$one,$two);
    }
    public function info(){
        $log = memory_get_usage();
        $log = ($log - $log%1024)/1024;
        return "Free - {$log}mb.";
    }
    public function quotes(){
        global $RandDotOrg;
        $handle = fopen(__DIR__."/text1022.txt", "r");
        $contents = fread($handle, filesize(__DIR__."/text1022.txt"));
        fclose($handle);
        $contents = str_replace("{", "", $contents);
        $contents = str_replace("':'", " ", $contents);
        $contents = str_replace("'", "", $contents);
        $pieces = explode("}", $contents);
        $number =  $RandDotOrg->get_integers(1,0,1021);
        return trim($pieces[$number]);
    }
}