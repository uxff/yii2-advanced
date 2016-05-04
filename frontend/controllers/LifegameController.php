<?php

namespace frontend\controllers;
use Yii;


class LifegameController extends \yii\web\Controller
{
    private $_width = 20;
    private $_height = 20;
    private $_deep = 32;
    private $_lifeCount = 5;
    private $_maxLived = 0;
    private $_maxLivedDur = 0;
    const MC_KEY_PRE        = 'lg_map_0';
    const MC_KEY_VER_PRE    = 'lg_map_version';
    const DEFAULT_PX = 4;

    public function actionIndex()
    {
        return $this->actionMakenewmap();
    }

    public function actionDebugmap() {
        
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $ver = $ver * 1 ? $ver * 1 : 1;
        $stMap = $this->getMymap($ver);
        print_r($stMap);
    }

    public function actionShowmap() {
        return $this->actionShowtdmap();
    }

    public function actionGdmaphtml() {
        echo '<img src="index.php?r=lifegame/gdmap"/>';
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

            $mapStr .= sprintf('<td key="%d" style="background-color:#%s;color:#FF6;font-size:9px;width:'.$px.'px;height:'.$px.'px" title="'.$neighberStr.'" val=""></td>', $i, ($mapDot ? '111' : 'FFF'), $mapDot);
            if (($i+1)%$this->_width==0) {
                $mapStr .= '</tr><tr>';
            }
        }
        $mapStr .= '</tr></table>';
        return $this->render('showmap', [
            'map'           => $map,
            'mapStr'        => $mapStr,
            'ver'           => $ver,
            'pre'           => $stMap['pre'],
            'dur'           => $stMap['dur'],
            'px'            => $px,
            'width'         => $this->_width,
            'height'        => $this->_height,
            'deep'          => $this->_deep,
            'i'             => $i,
            'showtd'        => 1,
            'showimg'       => 0,
            'count'         => $this->_lifeCount,
            'maxLived'      => $this->_maxLived,
            'maxLivedDur'   => $this->_maxLivedDur,
        ]);
    }
    protected function getMymap($ver = 1) {
        $key = self::MC_KEY_PRE.$ver;
        $val = Yii::$app->cache->get($key);
        $this->_width = isset($val['width']) ? $val['width'] : $this->_width;
        $this->_height = isset($val['height']) ? $val['height'] : $this->_height;
        $this->_deep = isset($val['deep']) ? $val['deep'] : $this->_deep;
        $this->_lifeCount = isset($val['count']) ? $val['count'] : $this->_lifeCount;
        $this->_maxLived = isset($val['maxLived']) ? $val['maxLived'] : $this->_maxLived;
        $this->_maxLivedDur = isset($val['maxLivedDur']) ? $val['maxLivedDur'] : $this->_maxLivedDur;
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

    public function actionMakelife() {
        $ver = isset($_GET['ver']) ? $_GET['ver'] : 1;
        $px = isset($_GET['px']) ? $_GET['px'] : self::DEFAULT_PX;
        $map = $this->getMymap($ver);
        // 执行多次
        $times = 200;
        $tmpmap = $map['map'];
        for ($i=1; $i<=$times; ++$i) {
            $tmpmap = $this->makeLife($tmpmap);
            ($this->_lifeCount > $this->_maxLived) && (($this->_maxLived = $this->_lifeCount) && ($this->_maxLivedDur = $map['dur']+$i));
        }
        $newmap = $tmpmap;

        $newver = $this->upVersion();
        $this->saveMap($newmap, $newver, $ver, $map['dur']+$times);
        $this->redirect(['lifegame/showmap', 'ver'=>$newver, 'px'=>$px]);
    }
    // 生存规则
    /*
        参考： http://www.qlcoder.com/task/75d8
        无边界地图
        如果一个生命体周围少于2生命体，那么该生命体会因为人口缺少而在下一轮死去，这个格子变成了空地。
        如果一个生命体周围大于3个生命体，那么该生命体会因为人口的过度拥挤，资源匮乏，而在下一轮死去。这个格子变成空地。
        如果一个生命体周围有2-3个生命体，那么该生命体能在下一轮继续活下去。
        如果一个空地周围有3个生命体，那么该空地在下一轮会繁殖出新的生命体。
    */
    protected function makeLife($map) {
        $newMap = $map;
        $this->_lifeCount = 0;
        foreach ($map as $i=>$mapDot) {
            $y = (int)($i/$this->_width);
            $x = (int)($i%$this->_width);
            $lifeCount = $this->calcNeighber($x, $y, $map);
            if (!$mapDot) {
                // 如果是空地
                if ($lifeCount==3) {
                    $newMap[$i] = 1;
                }
            } else {
                // 如果有生命
                if ($lifeCount<2) {
                    $newMap[$i] = 0;
                } elseif ($lifeCount>3) {
                    $newMap[$i] = 0;
                }
            }
            $this->_lifeCount += $newMap[$i];
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
    protected function calcNeighber($x, $y, $map) {
        $dotPos = $this->getNeighberPos($x, $y);
        $sum = 0;
        foreach ($dotPos as $dot) {
            $val = $map[$dot[1]*$this->_width+$dot[0]];
            $sum += $val;
        }
        return $sum;
    }

    protected function saveMap($map, $ver, $pre=null, $dur=0) {
        $key = self::MC_KEY_PRE.$ver;
        $arr = [
            'dur' => $dur,
            'map' => $map,
            'pre' => $pre,
            //'next' => $next,
            'width' => $this->_width,
            'height' => $this->_height,
            'deep' => $this->_deep,
            'count' => $this->_lifeCount,
            'maxLived' => $this->_maxLived,
            'maxLivedDur' => $this->_maxLivedDur,
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
        $this->redirect(['lifegame/showmap', 'ver'=>$ver]);
    }
    public function actionMakenewmap() {
        $width = isset($_GET['width']) ? (int)$_GET['width'] : $this->_width;
        $newver = $this->upVersion();
        if ($width) {
            $this->_width = $this->_height = $width;
        }
        $map = $this->makeMap();
        $this->saveMap($map, $newver);
        $this->redirect(['lifegame/showmap', 'ver'=>$newver]);
    }
    protected function makeMap($width = 0) {
        $width = $width ? $width : $this->_width;
        $height = $width ? $width : $this->_height;
        $deep = $this->_deep;
        $arr = [];
        for ($i=0; $i<$width*$height; ++$i) {
            $arr[$i] = 0;//mt_rand(0, $deep-1);
        }
        /*
            **
           **
            *
        */
        $mWidth = (int)$width/2;
        $mHeight = (int)$height/2;
        $arr[($mHeight-1)*$width+($mWidth-0)] = 1;$arr[($mHeight-1)*$width+($mWidth+1)] = 1;
        $arr[($mHeight+0)*$width+($mWidth-1)] = 1;$arr[($mHeight+0)*$width+($mWidth-0)] = 1;
        $arr[($mHeight+1)*$width+($mWidth-0)] = 1;
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
    // redis test
    public function actionRedis() {
        $key = 'test1';
        Yii::$app->redis->set($key, 1);
        $val = Yii::$app->redis->get($key);
        echo 'val='.$val;
    }

}
