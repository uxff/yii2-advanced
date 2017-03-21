<?php

namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\caching\Cache;
use yii\helpers\Console;
use yii\console\Exception;
use common\helpers\CurlHelper;

class TaskObj {
    public $command;
    public $params;
    public function __construct($command, $params=[]) {
        $this->command = $command;
        $this->params = $params;
    }
    public function execute() {
        
    }
    //public function 
}


class LogipController extends \yii\console\Controller {
    public function actionHello() {
                echo "hello\n";
        }
    public function actionNotifyLogin() {
        echo "to do:someone login my server by ssh.".__FILE__.":".__LINE__."\n";
        //print_r($_SERVER);
        $sshClient = $_SERVER['SSH_CLIENT'];
        $sshClient = explode(' ', $sshClient);
        $sshClientIp = is_array($sshClient) ? $sshClient[0] : $sshClient;
        $sshConnection = $_SERVER['SSH_CONNECTION'];

        $message = "有人链接了您的aliyun 149 服务器：\n";
        $message .= "SSH_CLIENT=".$sshClientIp."\n";
        $message .= "SSH_CONNECTION=".$sshConnection."\n";
        $message .= "USER=".$_SERVER['USER']."\n";
        $message .= "TIME=".date('Y-m-d H:i:s')."\n";

        // 踢出登录者的链接 从邮件中点击可以踢出登录者
        //


        $ret = $this->getIpInfo($sshClientIp);
        if (!empty($ret[1])) {
            $clientInfo = @json_decode($ret[1], true);
            $message .= "ip info:".implode(' ',$clientInfo['retData'])."\n";
        }

        Yii::warning($message);
        $subject = "服务器登录提示：".$sshConnection;
        //$ret = $this->sendMail("服务器登录提示：".$sshConnection, $message);
        //$ret = $this->addAsyncTask('$YIIC logip/send-mail subject="'.$subject.'" message="'.$message.'" to="'.$to.'"'.$message);
        $taskInfo = [
            'command' => 'sendMail',
            'params' => [
                'subject' => $subject,
                'message' => $message,
                'to' => $to,
            ],
        ];

        if($ret) {
            return "success";
        } else {
            return "failse";
        }
    }
    public function addAsyncTask($taskInfo, $env = null) {
        //\Yii::$app->redis->pub('shell task', $task);
        //echo 'add a task:';
        $ret = Yii::$app->redis->publish('fff', json_encode());
        Yii::warning('will add a task:'.$task, __METHOD__);
        //echo $task;
        //echo "\n";
    }
    public function exeAsyncTask() {
        //$redis = new \Redis;
        //$redis->pconnect('127.0.0.1', 6379);
        $taskInfo = $redis->subscribe(['f']);
    }
    public function actionSendMail($subject, $message, $to) {
        $ret = $this->sendMail($subject, $message, $to);
        return $ret;
    }

    public function sendMail($subject, $message, $to='154807895@qq.com') {
        $mail = Yii::$app->mailer->compose(); //加载模板这样写：$mail= Yii::$app->mailer->compose('moban',['key'=>'value']);
        $mail->setTo($to);
        $mail->setSubject($subject);
        $mail->setTextBody($message);
        //$mail->setHtmlBody("htmlbody");
        return $mail->send();
    }

    public function getIpInfo($ip) {
        $apiUrl = 'http://apis.baidu.com/showapi_open_bus/ip/ip';
        $apiUrl = 'http://apis.baidu.com/apistore/iplookupservice/iplookup';
        $apiFinal = $apiUrl.'?ip='.$ip;
        $apiRet = CurlHelper::get($apiFinal, ['apikey:208148e191ba17a6297b31f18116d52a']);
        //Yii::warning('curl:'.$apiFinal);
        //Yii::warning('ret='.json_encode($apiRet));
        return $apiRet;
    }
}
