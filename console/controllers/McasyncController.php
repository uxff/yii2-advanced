<?php

namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\caching\Cache;
use yii\helpers\Console;
use yii\console\Exception;
use yii\websocket\Websocket;

const G = 0.000021;

class Orb {
    //public $x;
    //public $y;
    //public $ax;
    //public $ay;
    //public $vx;
    //public $vy;
    //public $dir = PI/2;
    //public $mass = 5;
    //public $lifeStep = 1;
    //public $size = 0.5;
    //public $hue = 0;//(int)mt_rand(1, 16000000);
    //public $id;
    private $_data = [];
    public function __get($k) {
        return $this->_data[$k];
    }
    public function __set($k, $v) {
        $this->_data[$k] = $v;
    }
    public function update(&$orbList) {
        if (!$this->isEternal && $this->lifeStep == 1) {
            $g = $this->calcAllGravity($orbList);
            $this->vx += $g['ax'];
            $this->vy += $g['ay'];
            $this->vz += $g['az'];
            $this->x += $this->vx;
            $this->y += $this->vy;
            $this->z += $this->vz;
        }
        //$this->updateTimes = $this->updateTimes + 1;
    }
    public function calcAllGravity(&$orbList) {
        $g = ['ax'=>0, 'ay'=>0, 'az'=>0];
        foreach ($orbList as $k=>&$target) {
            if ($this->id==$target->id || $this->lifeStep!=1) {
                continue;
            }
            //$dist = self::distance($this->x, $this->y, $target->x, $target->y);
            $dist = $this->calcDist($target);
            if ($dist<3.0) {
                // 小于此距离，碰撞+爆炸
                if ($this->mass > $target->mass) {
                    $this->mass += $target->mass;
                    // 动量守恒定律:   m1v1+m2v2=m1v1ˊ+m2v2ˊ
                    // m1v1+m2v2 = m3v3; v3=(m1v1+m2v2)/m3
                    //this.vx = (target.mass*target.vx+this.mass*this.vx)/this.mass;
                    //this.vy = (target.mass*target.vy+this.mass*this.vy)/this.mass;
                    $this->vx = ($target->mass*$target->vx + $this->mass*$this->vx)/$this->mass;
                    $this->vy = ($target->mass*$target->vy + $this->mass*$this->vy)/$this->mass;
                    $this->vz = ($target->mass*$target->vz + $this->mass*$this->vz)/$this->mass;
                    $target->mass = 0;
                    $target->lifeStep = 2;
                } else {
                    $target->mass += $this->mass;
                    $this->vx = ($target->mass*$target->vx + $this->mass*$this->vx)/$target->mass;
                    $this->vy = ($target->mass*$target->vy + $this->mass*$this->vy)/$target->mass;
                    $this->vy = ($target->mass*$target->vy + $this->mass*$this->vy)/$target->mass;
                    $this->mass = 0;
                    $this->lifeStep = 2;
                }
            } else {
                $gtmp = $this->calcGravity($target, $dist);
                $g['ax'] += $gtmp['ax'];
                $g['ay'] += $gtmp['ay'];
                $g['az'] += $gtmp['az'];
            }
        }
        return $g;
    }
    public function calcDist(&$target) {
        return sqrt(($this->x-$target->x)*($this->x-$target->x) + ($this->y-$target->y)*($this->y-$target->y) + ($this->z-$target->z)*($this->z-$target->z));
    }
    public function calcGravity(&$target, $dist) {
        $force = $target->mass /($dist*$dist) * G;
        $g = [
            'a' => $force,
            //'dir' => $dir,
        ];
        $g['ax'] = $g['a'] * ($this->x - $target->x) / $dist;
        $g['ay'] = $g['a'] * ($this->y - $target->y) / $dist;
        $g['az'] = $g['a'] * ($this->z - $target->z) / $dist;
        return $g;
    }
    public function init($config = ['w'=>1920, 'h'=>1080]) {
        $this->x = mt_rand(100, $config['w']-100);
        $this->y = mt_rand(100, $config['h']-100);
        $this->z = mt_rand(100, $config['h']-100);
        $this->vx = mt_rand(0,200000000)/200000000 * 0.04 - 0.02;
        $this->vy = mt_rand(0,200000000)/200000000 * 0.04 - 0.02;
        $this->vz = mt_rand(0,200000000)/200000000 * 0.04 - 0.02;
        //$this->ax = 0;
        //$this->ay = 0;
        //$this->dir = PI/2;
        $this->mass = mt_rand(1, 100);
        $this->lifeStep = 1;
        $this->size = 0.5;
        $this->hue = (int)mt_rand(1, 16000000);
        //$this->updateTimes = 0;
        //$this->id = 0;
    }
    public function toArray() {
        return $this->_data;
    }
    public function loadData($data) {
        $this->_data = $data;
    }
    static public function distance($x1, $y1, $x2, $y2) {
        return sqrt(($x1 - $x2) * ($x1 - $x2) + ($y1 - $y2) * ($y1 - $y2));
    }
}

class McasyncController extends \yii\console\Controller {
    private $mckey = 'mcasync1';
    private $mckey2 = 'mcasync2';
    private $keyAdd = 'orbadd1';
    private $calcTimes = 100;
    private $maxParticles = 1;
    private $list = [];
    private $config = [];
    public function init() {
        parent::init();
        //Yii::$app->cache->serializer = false;
    }
    public function initList() {
        $this->list = [];
        for ($i=0; $i<$this->maxParticles; ++$i) {
            $orb = new Orb;
            $orb->id = $i;
            $orb->init($this->config);
            $this->list[] = $orb;
        }
        $this->addEternal();
    }
    public function initConfig($w, $h) {
        $this->config['w'] = $w;//$_GET['w'];
        $this->config['h'] = $h;//$_GET['h'];
    }
    public function updateList() {
        foreach ($this->list as $k=>&$o) {
            $o->update($this->list);
        }
    }
    public function addEternal() {
        $orb = new Orb;
        $orb->init($this->config);
        $orb->mass = 10000;
        $orb->x = $this->config['w']/2;
        $orb->y = $this->config['h']/2;
        $orb->z = $this->config['h']/2;
        $orb->id = mt_rand();
        $orb->size = 3;
        $orb->isEternal = 1;
        $this->list[] = $orb;
    }
    // addOrb on runtime
    public function addOrb($count=1) {
        $addList = Yii::$app->cache->get($this->keyAdd);
        $addList = is_array($addList) ? $addList : [];
        for ($i=0; $i<$count; ++$i) {
            $orb = new Orb;
            $orb->init($this->config);
            $orb->mass = mt_rand(10, 100);
            $orb->id = mt_rand();
            //echo mt_rand(-0.02, 0.02);exit;
            //$this->list[] = $orb;
            //Yii::$app->redis->set($this->keyAdd, [], 3600*12);
            $addList[] = $orb->toArray();
        }
        Yii::$app->cache->set($this->keyAdd, $addList, 3600*12);
        return $addList;
    }
    // mergeAdd on runtime
    public function mergeAdd() {
        $addList = Yii::$app->cache->get($this->keyAdd);
        //echo __METHOD__;print_r($addList);
        if ($addList && is_array($addList)) {
            foreach ($addList as $k=>$v) {
                $orb = new Orb;
                $orb->loadData($v);
                $this->list[] = $orb;
            }
            $addList = [];
            //$this->list = array_merge($this->list, $addList);
            Yii::$app->cache->set($this->keyAdd, $addList, 3600*12);
            //$this->saveList($this->list);
        }
    }
    public function saveList($targetList) {
        $list = [];
        foreach ($targetList as $k=>&$v) {
            if ($v->lifeStep*1 != 1) {
                //$orb = new Orb;
                //$orb->init(['w'=>1600, 'h'=>800]);
                //$orb->mass = mt_rand(10, 100);
                //$orb->id = mt_rand();//count($this->list);
                //echo "thereis a fuck dot with lifeStep=".($v->lifeStep)."! id=".$orb->id." time=".time()."\n";
                //$data = $orb->toArray();
                //$v->loadData($data);
                //$list[] = $data;
                continue;
            }
            //echo "there is common dot!\n";
            $list[] = $v->toArray();
        }
        //echo 'save to '.$this->mckey2.' ok'."\n";
        $list = @json_encode($list);
        Yii::$app->cache->set($this->mckey2, $list, 3600*100);
    }
    public function loadList() {
        $list = Yii::$app->cache->get($this->mckey2);
        $this->list = [];
        foreach ($list as $k=>$v) {
            $orb = new Orb;
            $orb->loadData($v);
            $this->list[] = $orb;
        }
    }
    public function actionIndex() {
        echo 'INDEX';
        $this->actionNew(1920, 1080);
        $this->actionUpdate();
    }
    public function actionNew($w=1920, $h=1080) {
        echo __METHOD__."\n";
        $this->initConfig($w, $h);
        $this->initList();
        //for ($i=0; $i<$this->calcTimes; ++$i) {
        //    $this->updateList();
        //}
        $this->saveList($this->list);
        print_r($this->list);
    }
    public function actionAdd($count=1, $w=1920, $h=1080) {
        echo __METHOD__."\n";
        //echo 'before list.count='.count($this->list)."\n";
        $this->initConfig($w, $h);
        $this->loadList();
        $addList = $this->addOrb($count);
        //echo 'after list.count='.count($this->list)."\n";
        print_r($addList);
        echo "done addList.count=".count($addList)." this.list.count=".count($this->list)."\n";
        //print_r($this->list);
    }
    public function actionUpdate($times=100) {
        $startTimeStr = microtime();
        list($startTimeMsec, $startTimeSec) = explode(' ', $startTimeStr);
        //echo " =$startTimeMsec $startTimeSec\n";
        $this->loadList();
        $saveCount = 0;
        for ($i=1; $i<=$times; ++$i) {
            $this->updateList();
            if ($i % 200 == 0) {
                $this->mergeAdd();
                $this->saveList($this->list);
                ++$saveCount;
            }
        }
        // 
        $this->mergeAdd();
        $this->saveList($this->list);
        //$endTime = time();
        list($endTimeMsec, $endTimeSec) = explode(' ', microtime());
        $timeSpan = ($endTimeSec - $startTimeSec) + ($endTimeMsec - $startTimeMsec);
        $speed = $times / (($timeSpan)*1.0 +0.000021);
        $cqps = $saveCount / (($timeSpan)*1.0 +0.000021);
        echo "list.count=".count($this->list)." timeSpan=".$timeSpan." cps=$speed cqps=$cqps\n";
        //print_r($this->list);
    }
    public function actionGet() {
        $list = Yii::$app->cache->get($this->mckey2);
        $list = @json_decode($list, true);
        echo "done key=".($this->mckey2)." list.count=".count($list)."\n";
    }
    public function getList() {
        $list = Yii::$app->cache->get($this->mckey2);
        $list = @json_decode($list, true);
        return $list;
    }
    public function actionGetlist() {
        $list = $this->getList();
        print_r($list);
        echo "done key=".($this->mckey2)." list.count=".count($list)."\n";
        //$this->jsonSuccess('ok', $list);
    }


    /*
        将memcache的orbList转发给websocket客户端
    */
    public function actionSwooleserver() {
        $host = '0.0.0.0';
        $port = 9501;
        $server = new \swoole_websocket_server($host, $port);

        $server->on('open', function (\swoole_websocket_server $server, $request) {
            echo "server: handshake success with fd{$request->fd} \n";
        });

        $server->on('message', function (\swoole_websocket_server $server, $frame) {
            echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
            $key = $frame->data;
            $reply = 'this is swoole server: your key='.$key;
            //Yii::log('serializer='.Yii::$app->cache->serializer);// yii 对memcache的value序列化存储了
            $listOri = Yii::$app->cache->get($key);//$this->keyAdd;
            $list = @json_decode($listOri, true);
            //print_r($list);
            $data = [
                'code' => 0,
                'msg'  => 'ok',
                'data' => [
                    'reply' => $reply,
                    'total' => count($list),
                    //'frame' => $frame,
                    'list' => $list,
                    //'listOri' => $listOri,
                    //'_SERVER' => $_SERVER,
                    //'_COOKIE' => $_COOKIE,
                    //'_GET' => $_GET,
                    //'_SESSION' => $_SESSION,
                    //'_ENV' => $_ENV,
                    //'_REQUEST' => $_REQUEST,
                    //'connection_info' => $server->connection_info,
                    //'connections' => $server->connections,
                ]
            ];
            $server->push($frame->fd, json_encode($data));
        });

        $server->on('close', function ($ser, $fd) {
            echo "client {$fd} closed\n";
        });

        $server->start();
        echo "swolle server start by $host:$port\n";
    }
    
	public function actionIn() {
		echo "to do: write log who login in my server with ssh here. here is ".__FILE__.":".__LINE__."\n";
        $timeStart = time();
        for ($i=1; $i<=$this->maxcalc; ++$i) {
            $info = Yii::$app->cache->get($this->mckey);
            if (!$info) {
                $info = 1;
            }
            $info = $info+1;
            Yii::$app->cache->set($this->mckey, $info, 3600*12);
            if ($i%(int)($this->maxcalc/10)==0) {
                echo 'i='.$i." info=$info\n";
            }
        }
        $timeEnd = time();
        $timeUsed = $timeEnd-$timeStart;
        $qps = $this->maxcalc/$timeUsed;
        echo "done. use $timeUsed sec, qps=$qps.\n";
	}

	public function actionOut() {
        $info = Yii::$app->cache->get($this->mckey);
        echo "info=$info\n";
        //print_r($info);
	}
}
