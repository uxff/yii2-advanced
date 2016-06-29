<?php

namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\caching\Cache;
use yii\helpers\Console;
use yii\console\Exception;

class LogipController extends \yii\console\Controller {
	public function actionHello() {
		echo "to do: write log who login in my server with ssh here. here is ".__FILE__.":".__LINE__."\n";
		//print_r($_SERVER);
		$sshClientIp = $_SERVER['SSH_CLIENT'];
		$sshConnection = $_SERVER['SSH_CONNECTION'];

		$message = "有人链接了您的aliyun服务器：\n";
		$message .= "SSH_CLIENT=".$sshClientIp."\n";
		$message .= "SSH_CONNECTION=".$sshConnection."\n";
		$message .= "USER=".$_SERVER['USER']."\n";
		$message .= "TIME=".date('Y-m-d H:i:s')."\n";

		// 踢出登录者的链接 从邮件中点击可以踢出登录者
		// 
		

		$ret = $this->sendMail("服务器登录提示：".$sshConnection, $message);		

		if($ret) {
			return "success";
		} else {
			return "failse";
		}
	}

	public function sendMail($subject, $message, $to='154807895@qq.com') {
		$mail = Yii::$app->mailer->compose(); //加载模板这样写：$mail= Yii::$app->mailer->compose('moban',['key'=>'value']);
		$mail->setTo($to);
		$mail->setSubject($subject);
		$mail->setTextBody($message);
		//$mail->setHtmlBody("htmlbody");
		return $mail->send();
	}
}
