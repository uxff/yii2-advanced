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

    public function actionIndex()
    {
        return $this->actionMakenewmap();
    }

    public function actionDebugmap()
    {
        
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $ver = $ver * 1 ? $ver * 1 : 1;
        $stMap = $this->getMymap($ver);
        print_r($stMap);
    }

    public function actionShowmap()
    {
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $px  = isset($_GET['px']) ? $_GET['px'] : 6;
        $ver = $ver * 1 ? $ver * 1 : 1;
        $stMap = $this->getMymap($ver);
        $map = $stMap['map'];
        // 通过refine生成的map必须排序
        //ksort($map);
//print_r($map);exit;
        $mapStr = '<table><tr>';
        $i = 0;
        if (!empty($map))
        foreach ($map as $i=>$mapDot) {
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

            $mapStr .= sprintf('<td key="%d" style="background-color:#2%x2;color:#FF6;font-size:6px;width:'.$px.'px;height:'.$px.'px" title="'.$neighberStr.'" val="%02d"></td>', $i, (int)(15-$mapDot/$this->_deep*16), $mapDot);
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
        $map = $this->getMymap($ver);
        if ($union) {
            $map = $this->passivateMapUnion($map['map'], $rad, $union);
        } else {
            $map = $this->passivateMap($map['map'], $rad);
        }
        $newver = $this->upVersion();
        $this->saveMap($map, $newver, $ver);
        $this->redirect(['mctest/showmap', 'ver'=>$newver]);
    }
    public function actionSharpmymap() {
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $map = $this->getMymap($ver);
        $map = $this->sharpMymap($map['map']);
        $newver = $this->upVersion();
        $this->saveMap($map, $newver, $ver);
        $this->redirect(['mctest/showmap', 'ver'=>$newver]);
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
        $avg = (int)($sum/count($dotPos));
        return $avg;
    }

    /*
        @param $zoomTimes 放大倍数 一个细化成三个
    */
    public function refine($map, $zoomTimes=3) {
        //$this->_width;
        //$this->_height;
        $newMap = [];
        foreach ($map as $i=>$mapDot) {
            $y = (int)($i/$this->_width);
            $x = (int)($i%$this->_width);
            $neighberAvg = $this->calcNeighberAvg1($x, $y, $map);
            //$abs = abs($neighberAvg-$this->_deep);
            //$vec = ($neighberAvg-$this->_deep/2 > 0) ? 1 : -1;
            $this->pushDot($newMap, $x, $y, $mapDot, $neighberAvg, $zoomTimes);
            //$mapDot = mt_rand($min, $max);//(int)(log($abs, 3))*$vec;
            //$mapDot = $mapDot >= $this->_deep ? $this->_deep-1 : $mapDot;
            //$mapDot = $mapDot < 0 ? 0 : $mapDot;
            //$newMap[$i] = $mapDot;
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
        $px = isset($_GET['px']) ? $_GET['px'] : 6;
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

}
