<?php

namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\caching\Cache;
use yii\helpers\Console;
use yii\console\Exception;
use yii\websocket\Websocket;
class RedistestController extends \yii\console\Controller {
    /*
    */
    public function actionHello() {
        echo 'hello';
    }
    public function actionRedistest() {
        //print_r(Yii::$app->redis);
        Yii::$app->redis->set('a', 123456);
        $ret = Yii::$app->redis->get('a');
        print_r($ret);
        //Yii::$app->redis->lpush('c', 13);
        //Yii::$app->redis->lpush('c', 14);
        //$ret = Yii::$app->redis->lrange('c', 0, 15);
        $ret = Yii::$app->redis->lpop('c');
        echo 'ret=';print_r($ret);echo "\n";
    }
    public function actionRedistest2() {
        //print_r(Yii::$app->redis);
        $ret = Yii::$app->redis->get('e');
        for ($i=0; $i<100000000; ++$i) {
            ++$ret;
            Yii::$app->redis->set('e', $ret);
            if ($i%1000000==1) {
                echo "i=$i ret=$ret\n";
            }
        }
        print_r($ret);
    }
    public function actionRedistest3() {
        //print_r(Yii::$app->redis);
        $ret = Yii::$app->redis->get('e');
        //print_r($ret);
        echo ' get in for ret='.$ret."\n";
    }
    /*
        抢购逻辑
    */
    public function actionRedistest4() {
        $redis = Yii::$app->redis;//new redis();
        //$result = $redis->connect('10.10.10.119', 6379);  
        $mywatchkey = $redis->get("mywatchkey");  
        $rob_total = 100;   //抢购数量  
        if($mywatchkey<$rob_total){  
            $redis->watch("mywatchkey");  
            $redis->multi();  
              
            //设置延迟，方便测试效果。  
            sleep(5);  
            //插入抢购数据  
            $redis->hset("mywatchlist","user_id_".mt_rand(1, 9999),time());  
            $redis->set("mywatchkey",$mywatchkey+1);  
            $rob_result = $redis->exec();  
            if($rob_result){  
                $mywatchlist = $redis->hgetall("mywatchlist");  
                echo "抢购成功！<br/>";  
                echo "剩余数量：".($rob_total-$mywatchkey-1)."<br/>";  
                echo "用户列表：<pre>";  
                var_dump($mywatchlist);  
            }else{  
                echo "手气不好，再抢购！";exit;  
            }
        }
    }
    public function actionRedispub1() {
        $ret = Yii::$app->redis->publish('f', 11);
        print_r($ret);
    }
    public function actionRedissub1() {
        //Yii::$app->redis;
        $redis = new \Redis;
        $redis->pconnect('127.0.0.1', 6379);
        print_r($redis);
        echo ' mywatchkey='.$redis->get("mywatchkey")."\n";
        // 已实现 pub/sub 机制 阻塞读
        $ret = $redis->subscribe(['f'], function ($redis, $chan, $msg){echo 'chan='.$chan.' msg='.$msg."\n";});
        print_r($ret);
        //sleep(10);
    }
}
