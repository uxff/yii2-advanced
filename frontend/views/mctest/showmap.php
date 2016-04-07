<div style="position:absolute">
<i class="fa fa-cubes"></i>
</div>
<a href="index.php?r=mctest/passivatemap&ver=<?php echo $ver;?>&rad=1&px=<?php echo $px;?>">模糊(radius=1)</a>&nbsp;
<a href="index.php?r=mctest/passivatemap&ver=<?php echo $ver;?>&rad=2&px=<?php echo $px;?>">模糊(radius=2)</a>&nbsp;
<a href="index.php?r=mctest/passivatemap&ver=<?php echo $ver;?>&rad=2&union=1&px=<?php echo $px;?>">模糊(radius=1& 2)</a>&nbsp;
<a href="index.php?r=mctest/sharpmymap&ver=<?php echo $ver;?>&px=<?php echo $px;?>">两极分化</a>&nbsp;
<a href="index.php?r=mctest/refine&ver=<?php echo $ver;?>&zoomTimes=2&px=<?php echo $px;?>">细化 x2</a>&nbsp;
<a href="index.php?r=mctest/refine&ver=<?php echo $ver;?>&zoomTimes=3&px=<?php echo $px;?>">细化 x3</a>&nbsp;
<a href="index.php?r=mctest/refine&ver=<?php echo $ver;?>&zoomTimes=4&px=<?php echo $px;?>">细化 x4</a>&nbsp;
<a href="index.php?r=mctest/showmap&ver=<?php echo $pre;?>&px=<?php echo $px;?>">撤销</a>&nbsp;
&nbsp;&nbsp;
<a href="index.php?r=mctest/showmap&ver=<?php echo $ver;?>&px=<?php echo (int)($px+1);?>">放大 +</a>&nbsp;
<a href="index.php?r=mctest/showmap&ver=<?php echo $ver;?>&px=<?php echo (int)($px-1);?>">缩小 -</a>&nbsp;
&nbsp;&nbsp;
边长:<input type="text" name="width" id="new_width" value="<?php echo $width;?>" style="width:40px"/>
<a href="javascript:;" id="btn_new" onclick="gotonew()">新建图像</a>&nbsp;
<a href="index.php?r=mctest/clearmap&ver=<?php echo $ver;?>">清除图像</a>&nbsp;
<!--
<a href="index.php?r=mctest/clearall">clearall</a>&nbsp;
-->
<pre>
<?php
if ($showtd) {
    echo $mapStr;
} else {
?>
    <img src="index.php?r=mctest/gdmap&ver=<?php echo $ver;?>&px=<?php echo $px;?>"/>
<?php
}

?>

</pre>
<span>width=<?php echo $width;?></span>
<span>height=<?php echo $height;?></span>
<span>deep=<?php echo $deep;?></span>
<span>i=<?php echo $i;?></span>
<span>count=<?php echo $count;?></span>
<script>
var gotonew = function () {
    window.location = 'index.php?r=mctest/makenewmap'+'&width='+document.getElementById('new_width').value;
}
</script>
