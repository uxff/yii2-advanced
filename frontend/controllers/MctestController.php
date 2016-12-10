<?php

namespace frontend\controllers;
use Yii;


class MctestController extends \yii\web\Controller
{
    private $_width = 20;
    private $_height = 20;
    private $_deep = 32;
    const MC_KEY_PRE        = 'my_map_0';
    const MC_KEY_VER_PRE    = 'my_map_version';
    const DEFAULT_PX = 4;

    public function actionIndex()
    {
        return $this->actionMakenewmap();
    }
    public function actionCmdget() {
        $key = $_GET['key'];
        $ret = Yii::$app->cache->get($key);
        echo 'ret=';print_r($ret);
    }
    public function actionCmdSet($key, $value) {
        $ret = Yii::$app->cache->set($key, $value, 3600);
        echo 'ret=';print_r($ret);
    }

    public function actionDebugmap() {
        
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $ver = $ver * 1 ? $ver * 1 : 1;
        $stMap = $this->getMymap($ver);
        print_r($stMap);
    }

    public function actionShowmap() {
        return $this->actionShowgdmap();
    }

    public function actionGdmaphtml() {
        echo '<img src="index.php?r=mctest/gdmap"/>';
    }

    public function actionGdmap() {
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $px  = isset($_GET['px']) ? (int)$_GET['px'] : self::DEFAULT_PX;
        $px  = $px < 1 ? 1 : $px;
        $ver = $ver * 1 ? $ver * 1 : 1;
        $stMap = $this->getMymap($ver);
        $map = $stMap['map'];
        if (!empty($map)) {
            $image = imagecreatetruecolor($this->_width * $px, $this->_height * $px);
            
            $white      = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
            //$gray       = imagecolorallocate($image, 0xC0, 0xC0, 0xC0);         //为图像分配颜色为灰色
            //$darkgray   = imagecolorallocate($image, 0x90, 0x90, 0x90);         //为图像分配颜色为暗灰色
            //$navy       = imagecolorallocate($image, 0x00, 0x00, 0x80);         //为图像分配颜色为深蓝色
            //$darknavy   = imagecolorallocate($image, 0x00, 0x00, 0x50);         //为图像分配颜色为暗深蓝色
            //$red        = imagecolorallocate($image, 0xFF, 0x00, 0x00);         //为图像分配颜色为红色
            //$darkred    = imagecolorallocate($image, 0x90, 0x00, 0x00);         //为图像分配颜色为暗红色            

            $colorStep = 256 / ($this->_deep+1);
            $colorArr = [];
            $colorTpl = $this->colorArr2(5);
            //print_r($colorTpl);exit;
            for ($i=0; $i<$this->_deep; ++$i) {
                //$tplIndex = count($colorTpl) - 1 - (int)($i / ($this->_deep / count($colorTpl)));
                $tplIndex = (int)($i / ($this->_deep / count($colorTpl)));
                $color = imagecolorallocate($image, $colorTpl[$tplIndex]['r'], $colorTpl[$tplIndex]['g'], $colorTpl[$tplIndex]['b']);
                $colorArr[$i] = $color;
            }
            imagefill($image, 0, 0, $white);
            foreach ($map as $i=>$mapDot) {
                // $mapDot is a deep value
                $y = (int)($i/$this->_width) * $px;
                $x = (int)($i%$this->_width) * $px;
            
                imagefilledrectangle($image, $x, $y, $x+$px, $y+$px, $colorArr[$mapDot]);
            }

            // 输出
            header('Content-type:image/png');
            //imagepng($image);
            imagejpeg($image);
            imagedestroy($image);
        }
        return null;
    }

    public function actionShowgdmap() {
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $px  = isset($_GET['px']) ? $_GET['px'] : self::DEFAULT_PX;
        $px  = $px < 1 ? 1 : $px;
        $ver = $ver * 1 ? $ver * 1 : 1;
        $stMap = $this->getMymap($ver);
        $map = $stMap['map'];

        $mapStr = '';
        $i = 0;
        if (!empty($map)) {
            
        }
        return $this->render('showmap', [
            'map' => $map,
            'mapStr' => $mapStr,
            'ver' => $ver,
            'pre' => $stMap['pre'],
            'px' => $px,
            'width' => $this->_width,
            'height' => $this->_height,
            'deep' => $this->_deep,
            'i' => $i,
            'showtd' => 0,
            'showimg' => 1,
            'count' => count($map),
        ]);
    }
    public function actionShowtdmap() {
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $px  = isset($_GET['px']) ? $_GET['px'] : self::DEFAULT_PX;
        $ver = $ver * 1 ? $ver * 1 : 1;
        $stMap = $this->getMymap($ver);
        $map = $stMap['map'];
        // 通过refine生成的map必须排序

        $mapStr = '<table><tr>';
        $i = 0;
        if (!empty($map))
        //foreach ($map as $i=>$mapDot)
        for ($i=0; $i<$this->_width * $this->_height; ++$i)
        {
            $mapDot = isset($map[$i]) ? $map[$i] : 0;
            // $mapDot is a deep value
            $y = (int)($i/$this->_width);
            $x = (int)($i%$this->_width);
            $neighberStr = '';
            // 外围第一圈 显示
            //$neighbers = $this->getNeighberPos($x, $y);
            //foreach ($neighbers as $neighberDot) {
            //    $neighberStr .= $neighberDot[0].','.$neighberDot[1].'='.$map[$neighberDot[1]*$this->_width+$neighberDot[0]]."\n";
            //}
            //$neighberAvg = $this->calcNeighberAvg1($x, $y, $map);
            //$neighberStr .= 'avg='.$neighberAvg."\n";
            //$neighberStr .= 'fall='.(int)(($neighberAvg-$mapDot)/2)."\n";

            // 为了快速计算 去掉外围第二圈显示
            //$neighbers2 = $this->getNeighberPos2($x, $y);
            //foreach ($neighbers2 as $neighberDot) {
            //    $neighberStr .= $neighberDot[0].','.$neighberDot[1].'='.$map[$neighberDot[1]*$this->_width+$neighberDot[0]]."\n";
            //}
            //$neighberAvg2 = $this->calcNeighberAvg2($x, $y, $map);
            //$neighberStr .= 'avg='.$neighberAvg2."\n";
            //$neighberStr .= 'fall='.(int)(($neighberAvg2-$mapDot)/2)."\n";

            $mapStr .= sprintf('<td key="%d" style="background-color:#2%x2;color:#FF6;font-size:9px;width:'.$px.'px;height:'.$px.'px" title="'.$neighberStr.'" val="">%02d</td>', $i, (int)(15-$mapDot/$this->_deep*16), $mapDot);
            if (($i+1)%$this->_width==0) {
                $mapStr .= '</tr><tr>';
            }
        }
        $mapStr .= '</tr></table>';
        return $this->render('showmap', [
            'map' => $map,
            'mapStr' => $mapStr,
            'ver' => $ver,
            'pre' => $stMap['pre'],
            'px' => $px,
            'width' => $this->_width,
            'height' => $this->_height,
            'deep' => $this->_deep,
            'i' => $i,
            'showtd' => 1,
            'showimg' => 0,
            'count' => count($map),
        ]);
    }
    protected function getMymap($ver = 1) {
        $key = self::MC_KEY_PRE.$ver;
        $val = Yii::$app->cache->get($key);
        $this->_width = isset($val['width']) ? $val['width'] : $this->_width;
        $this->_height = isset($val['height']) ? $val['height'] : $this->_height;
        $this->_deep = isset($val['deep']) ? $val['deep'] : $this->_deep;
        return $val;
    }
    protected function getVersion() {
        $versionKey = self::MC_KEY_VER_PRE;
        $val = Yii::$app->cache->get($versionKey);
        if (!$val) {
            $val = '1';
            Yii::$app->cache->set($versionKey, $val, 86400);
        }
        return $val;
    }
    protected function upVersion() {
        $versionKey = self::MC_KEY_VER_PRE;
        $val = Yii::$app->cache->get($versionKey);
        $val = $val + 1;
        Yii::$app->cache->set($versionKey, $val, 86400);
        return $val;
    }

    public function actionPassivatemap() {
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $rad = isset($_GET['rad']) ? $_GET['rad'] : 1;
        $union = isset($_GET['union']) ? $_GET['union'] : 0;
        $px = isset($_GET['px']) ? $_GET['px'] : self::DEFAULT_PX;
        $map = $this->getMymap($ver);
        if ($union) {
            $map = $this->passivateMapUnion($map['map'], $rad, $union);
        } else {
            $map = $this->passivateMap($map['map'], $rad);
        }
        $newver = $this->upVersion();
        $this->saveMap($map, $newver, $ver);
        $this->redirect(['mctest/showmap', 'ver'=>$newver, 'px'=>$px]);
    }
    public function actionSharpmymap() {
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $px = isset($_GET['px']) ? $_GET['px'] : self::DEFAULT_PX;
        $map = $this->getMymap($ver);
        $map = $this->sharpMymap($map['map']);
        $newver = $this->upVersion();
        $this->saveMap($map, $newver, $ver);
        $this->redirect(['mctest/showmap', 'ver'=>$newver, 'px'=>$px]);
    }
    // 钝化
    protected function passivateMap($map, $r=1) {
        $newMap = $map;
        $calcFunc = 'calcNeighberAvg'.$r;
        foreach ($map as $i=>$mapDot) {
            $y = (int)($i/$this->_width);
            $x = (int)($i%$this->_width);
            $neighberAvg = $this->$calcFunc($x, $y, $map);
            $mapDot += (int)(($neighberAvg-$mapDot)/2);
            $newMap[$i] = $mapDot;
        }
        return $newMap;
    }
    // 钝化 2
    protected function passivateMapUnion($map, $r=1, $union=1) {
        $newMap = $map;
        foreach ($map as $i=>$mapDot) {
            $y = (int)($i/$this->_width);
            $x = (int)($i%$this->_width);
            $avgSum = 0;
            $avgCount = 0;
            for ($j=$r; $j>=1; --$j) {
                $calcFunc = 'calcNeighberAvg'.$j;
                $neighberAvg = $this->$calcFunc($x, $y, $map);
                $avgSum += $neighberAvg;
                ++$avgCount;
            }
            $avgVal = $avgCount ? (int)($avgSum/$avgCount) : $neighberAvg;
            $mapDot += (int)(($avgVal-$mapDot)/2);
            $newMap[$i] = $mapDot;
        }
        return $newMap;
    }
    // 锐化
    protected function sharpMymap($map) {
        $newMap = $map;
        foreach ($map as $i=>$mapDot) {
            $y = (int)($i/$this->_width);
            $x = (int)($i%$this->_width);
            $neighberAvg = $this->calcNeighberAvg1($x, $y, $map);
            $abs = abs($neighberAvg-$this->_deep);
            $vec = ($neighberAvg-$this->_deep/2 > 0) ? 1 : -1;
            $mapDot += (int)(log($abs, 3))*$vec;
            $mapDot = $mapDot >= $this->_deep ? $this->_deep-1 : $mapDot;
            $mapDot = $mapDot < 0 ? 0 : $mapDot;
            $newMap[$i] = $mapDot;
        }
        return $newMap;
    }
    /*
    ###
    #*#
    ###
    */
    protected function getNeighberPos($x, $y) {
    //    return $this->getNeighberPos1($x, $y);
    //}
    //protected function getNeighberPos1($x, $y) {
        $arrDot = [];
        // x+1, y
        if ($x+1<$this->_width) {
            $arrDot[] = array($x+1, $y);
        }
        // x+1, y+1
        if ($x+1<$this->_width && $y+1<$this->_height) {
            $arrDot[] = array($x+1, $y+1);
        }
        // x,   y+1
        if ($y+1<$this->_height) {
            $arrDot[] = array($x, $y+1);
        }
        // x-1, y+1
        if (0<=$x-1 && $y+1<$this->_height) {
            $arrDot[] = array($x-1, $y+1);
        }
        // x-1, y
        if (0<=$x-1) {
            $arrDot[] = array($x-1, $y);
        }
        // x-1, y-1
        if (0<=$x-1 && 0<=$y-1) {
            $arrDot[] = array($x-1, $y-1);
        }
        // x,   y-1
        if (0<=$y-1) {
            $arrDot[] = array($x, $y-1);
        }
        // x+1, y-1
        if ($x+1<$this->_width && 0<=$y-1) {
            $arrDot[] = array($x+1, $y-1);
        }
        return $arrDot;
    }
    protected function calcNeighberAvg1($x, $y, $map) {
        $dotPos = $this->getNeighberPos($x, $y);
        $sum = 0;
        foreach ($dotPos as $dot) {
            $val = $map[$dot[1]*$this->_width+$dot[0]];
            $sum += $val;
        }
        $avg = (int)($sum/count($dotPos));
        return $avg;
    }

    protected function saveMap($map, $ver, $pre=null, $next=null) {
        $key = self::MC_KEY_PRE.$ver;
        $arr = [
            'ver' => $ver,
            'map' => $map,
            'pre' => $pre,
            'next' => $next,
            'width' => $this->_width,
            'height' => $this->_height,
            'deep' => $this->_deep,
            'count' => count($map),
        ];
        if ($map) {
            Yii::$app->cache->set($key, $arr, 86400);
        }
        return $arr;
    }
    public function actionClearmap($ver) {
        $key = self::MC_KEY_PRE.$ver;
        if ($ver) {
            $ret = Yii::$app->cache->set($key, 0, time());
        }
        $this->redirect(['mctest/showmap', 'ver'=>$ver]);
    }
    public function actionMakenewmap() {
        $width = isset($_GET['width']) ? (int)$_GET['width'] : $this->_width;
        $newver = $this->upVersion();
        if ($width) {
            $this->_width = $this->_height = $width;
        }
        $map = $this->makeMap();
        $this->saveMap($map, $newver);
        $this->redirect(['mctest/showmap', 'ver'=>$newver]);
    }
    protected function makeMap($width = 0) {
        $width = $width ? $width : $this->_width;
        $height = $width ? $width : $this->_height;
        $deep = $this->_deep;
        $arr = [];
        for ($i=0; $i<$width*$height; ++$i) {
            $arr[$i] = mt_rand(0, $deep-1);
        }
        return $arr;
    }
    protected function findNeighber($x, $y, $r) {
        $testPos = array($x+$r, $y);
        
    }
    /*
    找到周围如下坐标 距离为2
      [][][]         ###
    []      []      #   #
    []  []  []      # * #
    []      []      #   #
      [][][]         ###
    */
    protected function getNeighberPos2($x, $y) {
        $arrDot = [];
        // x+2, y
        if ($x+2<$this->_width) {
            $arrDot[] = array($x+2, $y);
        }
        // x+2, y+1
        if ($x+2<$this->_width && $y+1<$this->_height) {
            $arrDot[] = array($x+2, $y+1);
        }
        // x+1, y+2
        if ($x+1<$this->_width && $y+2<$this->_height) {
            $arrDot[] = array($x+1, $y+2);
        }
        // x,   y+2
        if ($y+2<$this->_height) {
            $arrDot[] = array($x, $y+2);
        }
        // x-1, y+2
        if (0<=$x-1 && $y+2<$this->_height) {
            $arrDot[] = array($x-1, $y+2);
        }
        // x-2, y+1
        if (0<=$x-2 && $y+1<$this->_height) {
            $arrDot[] = array($x-2, $y+1);
        }
        // x-2, y
        if (0<=$x-2) {
            $arrDot[] = array($x-2, $y);
        }
        // x-2, y-1
        if (0<=$x-2 && 0<=$y-1) {
            $arrDot[] = array($x-2, $y-1);
        }
        // x-1, y-2
        if (0<=$x-1 && 0<=$y-2) {
            $arrDot[] = array($x-1, $y-2);
        }
        // x,   y-2
        if (0<=$y-2) {
            $arrDot[] = array($x, $y-2);
        }
        // x+1, y-2
        if ($x+1<$this->_width && 0<=$y-2) {
            $arrDot[] = array($x+1, $y-2);
        }
        // x+2, y-1
        if ($x+2<$this->_width && 0<=$y-1) {
            $arrDot[] = array($x+2, $y-1);
        }
        return $arrDot;
    }
    protected function calcNeighberAvg2($x, $y, $map) {
        $dotPos = $this->getNeighberPos2($x, $y);
        $sum = 0;
        foreach ($dotPos as $dot) {
            $val = $map[$dot[1]*$this->_width+$dot[0]];
            $sum += $val;
        }
        $avg = $sum ? (int)($sum/count($dotPos)) : $map[$y * $this->_width + $x];
        return $avg;
    }

    /*
        @param $zoomTimes 放大倍数 一个细化成三个
    */
    public function refine($map, $zoomTimes=3) {
        $newMap = [];
        foreach ($map as $i=>$mapDot) {
            // 放大之前的坐标
            $y = (int)($i/$this->_width);
            $x = (int)($i%$this->_width);
            $neighberAvg = $this->calcNeighberAvg2($x, $y, $map);
            //// 填充周边
            $this->pushDot($newMap, $x, $y, $mapDot, $neighberAvg, $zoomTimes);
            // 放大之前的邻居
            $arrDotPos = $this->getNeighberPos($x, $y);
            $arrDotPosVal = [];
            // 获得放大前周边点坐标和值
            foreach ($arrDotPos as $dot) {
                $neighberVal = $map[$dot[1]*$this->_width+$dot[0]];
                $arrDotPosVal[] = [
                    'x' => $dot[0],
                    'y' => $dot[1],
                    'v' => $neighberVal,
                ];
                // 利用原来坐标差值找出新坐标 ($dot[0]-$x)取值范围在-1到1 表示周围差值
                $tx = (int)($x * $zoomTimes + $zoomTimes/2) + ($dot[0]-$x);
                $ty = (int)($y * $zoomTimes + $zoomTimes/2) + ($dot[1]-$y);
                $tIndex = $this->_width * $zoomTimes * $ty + $tx;
                // 计算随机大小范围
                $min = $neighberVal>$mapDot ? $mapDot : $neighberVal;
                $max = $neighberVal>$mapDot ? $neighberVal : $mapDot;
                
                // 给随机值
                $r = mt_rand($min, $max);
                //Yii::error($i.': min='.$min.' max='.$max.' mapDot='.$mapDot.' tx='.$tx.' ty='.$ty.' r='.$r, __METHOD__);
                $newMap[$tIndex] = $r;
            }
            // 中间坐标保持原来数据
            $tCenterX = (int)($x * $zoomTimes + $zoomTimes/2);
            $tCenterY = (int)($y * $zoomTimes + $zoomTimes/2);
            $tCenterIndex = $this->_width * $zoomTimes * $tCenterY + $tCenterX;
            $newMap[$tCenterIndex] = $mapDot;
        }
        ksort($newMap);
        return $newMap;
    }
    // 给某点周围布点
    protected function pushDot(&$map, $x, $y, $mapDot, $avg, $zoomTimes) {
        $min = $avg>$mapDot ? $mapDot : $avg;
        $max = $avg>$mapDot ? $avg : $mapDot;
        $zoomXstart = (int)($x * $zoomTimes);
        $zoomYstart = (int)($y * $zoomTimes);
        // for x
        for ($i=0; $i<$zoomTimes; ++$i) {
            $tx = $zoomXstart + $i;
            // for y
            for ($j=0; $j<$zoomTimes; ++$j) {
                $ty = $zoomYstart + $j;
                $tIndex = $this->_width * $zoomTimes * $ty + $tx;
                $map[$tIndex] = mt_rand($min, $max);
            }
        }
        // 中间坐标保持原来数据
        $tCenterX = (int)($zoomXstart + $zoomTimes/2);
        $tCenterY = (int)($zoomYstart + $zoomTimes/2);
        $tCenterIndex = $this->_width * $zoomTimes * $tCenterY + $tCenterX;
        $map[$tCenterIndex] = $mapDot;
        return true;
    }
    public function actionRefine() {
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $zoomTimes = isset($_GET['zoomTimes']) ? $_GET['zoomTimes'] : 3;
        $px = isset($_GET['px']) ? $_GET['px'] : self::DEFAULT_PX;
        $px = (int)($px/$zoomTimes);
        $px = $px<1 ? 1 : $px;
        $map = $this->getMymap($ver);
        $map = $this->refine($map['map'], $zoomTimes);
        $newver = $this->upVersion();
        $this->_width = $this->_width * $zoomTimes;
        $this->_height = $this->_height * $zoomTimes;
        $this->saveMap($map, $newver, $ver);
        $this->redirect(['mctest/showmap', 'ver'=>$newver, 'px'=>$px]);
    }
    // redis test
    public function actionRedis() {
        $key = 'test1';
        Yii::$app->redis->set($key, 1);
        $val = Yii::$app->redis->get($key);
        echo 'val='.$val;
    }

    public function actionGdmap2() {
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $px  = isset($_GET['px']) ? (int)$_GET['px'] : self::DEFAULT_PX;
        $px  = $px < 1 ? 1 : $px;
        $ver = $ver * 1 ? $ver * 1 : 1;
        $image = imagecreatetruecolor(100 * $px, 100 * $px);
        
        $white      = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
        //$gray       = imagecolorallocate($image, 0xC0, 0xC0, 0xC0);         //为图像分配颜色为灰色
        //$darkgray   = imagecolorallocate($image, 0x90, 0x90, 0x90);         //为图像分配颜色为暗灰色
        //$navy       = imagecolorallocate($image, 0x00, 0x00, 0x80);         //为图像分配颜色为深蓝色
        //$darknavy   = imagecolorallocate($image, 0x00, 0x00, 0x50);         //为图像分配颜色为暗深蓝色
        //$red        = imagecolorallocate($image, 0xFF, 0x00, 0x00);         //为图像分配颜色为红色
        //$darkred    = imagecolorallocate($image, 0x90, 0x00, 0x00);         //为图像分配颜色为暗红色            

        imagefill($image, 0, 0, $white);
        $colorArr = [];
        for ($i=0; $i<20; ++$i) {
            $color = imagecolorallocate($image, 1, ($i)*12, 1);
            $colorArr[$i] = $color;
        }
        for ($i=0; $i<count($colorArr); ++$i) {
            // $mapDot is a deep value
            $x = (int)($i) * $px;
            $y = 0;//(int)($i) * $px;
        
            imagefilledrectangle($image, $x, $y, $x+$px, $y+$px, $colorArr[$i]);
        }

        // 输出
        header('Content-type:image/png');
        //imagepng($image);
        imagejpeg($image);
        imagedestroy($image);
        return null;
    }
    public function actionGdmaphtml2() {
        echo '<img src="index.php?r=mctest/gdmap2"/>';
    }
    public function actionDrawcolorstep() {
        $px  = isset($_GET['px']) ? (int)$_GET['px'] : self::DEFAULT_PX;
        $px *= 4;
        $mapStr = '<table><tr>';
        $i = 0;
        $j = 0;
        $colorArr = [];
        $stepCount = 5;
        $colorArr = $this->colorArr2($stepCount);
        $stepCount = count($colorArr);
        for ($j=0; $j<$stepCount; ++$j)
        {
            for ($i=0; $i<$stepCount; ++$i) {
                // $mapDot is a deep value
                $x = (int)($i) * $px;
                $y = 0;//(int)($i) * $px;
                //$color = $this->rgb(($i)*12, ($stepCount-$i-1)*12, 0);
            
                //imagefilledrectangle($image, $x, $y, $x+$px, $y+$px, $colorArr[$i]);
                $mapStr .= sprintf('<td key="%d" style="background-color:#%06X;color:#FF6;font-size:9px;width:'.$px.'px;height:'.$px.'px" title="'.'" val="">'.'%d</td>', $i, $colorArr[$i]['rgb'], $i);
            }
            //if (($j+1)%$stepCount==0) {
                $mapStr .= '</tr><tr>';
            //}
        }
        $mapStr .= '</tr></table>';
        echo $mapStr;
    }
    public function rgb($r, $g, $b) {
        $r = (int)$r;
        $g = (int)$g;
        $b = (int)$b;
        return [
            'r' => $r,
            'g' => $g,
            'b' => $b,
            '#' => sprintf('%02X%02X%02X', $r, $g, $b),
            'rgb' => (($r)<<16) + (($g)<<8) + ($b),
        ];
    }
    /*
        @param $max 数组的最大length
    */
    public function colorArr($max = 30) {
        $r = 0;
        $g = 0;
        $b = 0;
        $colorArr = [];
        $xStart = 0;   // -0.5 pi
        $xEnd = M_PI;   // 3/4  pi
        $step = M_PI / $max;
        Yii::warning('xStart='.$xStart.' xEnd='.$xEnd.' step='.$step, __METHOD__);
        for ($i=0; $i<$max; ++$i) {
            $x = $xStart + $i * $step;
            //$r =  cos($x) * 255;
            //$g =  sin($x+M_PI/4) * 255;
            //$b =  sin($x-M_PI/4) * 255;
            $r =  sin($x-M_PI/3) * 255;
            $g =  sin($x) * 255;
            $b =  sin($x*2) * 255;
            //$b =  sin($x/2) * 255;
            // 不能小于0
            $r = $r>0 ? (int)$r : 0;
            $g = $g>0 ? (int)$g : 0;
            $b = $b>0 ? (int)$b : 0;
            Yii::warning('i='.$i.' x='.$x.' r='.$r.' g='.$g.' b='.$b, __METHOD__);
            $colorArr[] = $this->rgb($r, $g, $b);
        }
        return $colorArr;
    }
    /*
        @param $max 每个色组数组的最大length
    */
    public function colorArr2($max = 4) {
        $r = 0;
        $g = 0;
        $b = 0;
        $colorArr = [];
        $xStart = 0;   // -0.5 pi
        $xEnd = M_PI;   // 3/4  pi
        $sectionSize = (int)($max);
        $step = (int)(256 / $sectionSize);
        //Yii::warning('xStart='.$xStart.' xEnd='.$xEnd.' step='.$step, __METHOD__);
        //// 蓝到青
        //for ($i=0; $i<$sectionSize*1; ++$i) {
        //    $r = 0;
        //    $g = $i * $step;
        //    $b = 250;
        //    Yii::warning('i='.$i.' r='.$r.' g='.$g.' b='.$b, __METHOD__);
        //    $colorArr[] = $this->rgb($r, $g, $b);
        //}
        //// 青到绿
        //for ($i=0; $i<$sectionSize*1; ++$i) {
        //    $r = 0;
        //    $g = 255;// - $i * $step * 0.1;
        //    $b = 255 - $i * $step;
        //    Yii::warning('i='.$i.' r='.$r.' g='.$g.' b='.$b, __METHOD__);
        //    $colorArr[] = $this->rgb($r, $g, $b);
        //}
        // 绿到黄
        for ($i=0; $i<$sectionSize*1; ++$i) {
            $r = $i * ($step + 1);
            $g = 256 - ($sectionSize - $i) * $step * 0.3;
            $b = 0;
            //Yii::warning('i='.$i.' r='.$r.' g='.$g.' b='.$b, __METHOD__);
            $colorArr[] = $this->rgb($r, $g, $b);
        }
        // 黄到红
        for ($i=0; $i<$sectionSize*1; ++$i) {
            $r = 255;
            $g = 255 - ( ($i)) * ($step * 0.7) - $i*$sectionSize/2;// - $i * $sectionSize;
            $b = 0;
            //Yii::warning('i='.$i.' r='.$r.' g='.$g.' b='.$b, __METHOD__);
            $colorArr[] = $this->rgb($r, $g, $b);
        }
        // 红到紫
        for ($i=0; $i<$sectionSize*1/2; ++$i) {
            $r = 255 - $i * $step;
            $g = 0;
            $b = $i * $step;
            //Yii::warning('i='.$i.' r='.$r.' g='.$g.' b='.$b, __METHOD__);
            $colorArr[] = $this->rgb($r, $g, $b);
        }
        // 红到紫
        for ($i=0; $i<$sectionSize*1/2; ++$i) {
            $r = 255 - ($sectionSize/2) * $step+ $i*$step;
            $g = 0 + $i*$step;
            $b = ($sectionSize/2) * $step+ $i*$step;
            //Yii::warning('i='.$i.' r='.$r.' g='.$g.' b='.$b, __METHOD__);
            $colorArr[] = $this->rgb($r, $g, $b);
        }
        //// 红到紫
        //for ($i=0; $i<$sectionSize*1; ++$i) {
        //    $r = 250;//255 - $i * $step;
        //    $g = 0;
        //    $b = $i * $step;
        //    Yii::warning('i='.$i.' r='.$r.' g='.$g.' b='.$b, __METHOD__);
        //    $colorArr[] = $this->rgb($r, $g, $b);
        //}
        //// 紫到白
        //for ($i=0; $i<$sectionSize*1; ++$i) {
        //    $r = 250;
        //    $g = $i * $step;
        //    $b = 250;
        //    Yii::warning('i='.$i.' r='.$r.' g='.$g.' b='.$b, __METHOD__);
        //    $colorArr[] = $this->rgb($r, $g, $b);
        //}
        return $colorArr;
    }

}
