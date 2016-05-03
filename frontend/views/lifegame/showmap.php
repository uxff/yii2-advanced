<div style="position:absolute">
<i class="fa fa-cubes"></i>
</div>
<a href="index.php?r=lifegame/makelife&ver=<?php echo $ver;?>&px=<?php echo $px;?>">生存</a>&nbsp;
<a href="index.php?r=lifegame/showmap&ver=<?php echo $pre;?>&px=<?php echo $px;?>">撤销</a>&nbsp;
&nbsp;&nbsp;
<a href="index.php?r=lifegame/showmap&ver=<?php echo $ver;?>&px=<?php echo (int)($px+1);?>">放大 +</a>&nbsp;
<a href="index.php?r=lifegame/showmap&ver=<?php echo $ver;?>&px=<?php echo (int)($px-1);?>">缩小 -</a>&nbsp;
&nbsp;&nbsp;
边长:<input type="text" name="width" id="new_width" value="<?php echo $width;?>" style="width:40px"/>
<a href="javascript:;" id="btn_new" onclick="gotonew()">新建图像</a>&nbsp;
<a href="index.php?r=lifegame/clearmap&ver=<?php echo $ver;?>">清除图像</a>&nbsp;
<!--
<a href="index.php?r=lifegame/clearall">clearall</a>&nbsp;
-->
<pre>
<?php
if ($showtd) {
    echo $mapStr;
} else {
?>
    <img src="index.php?r=lifegame/gdmap&ver=<?php echo $ver;?>&px=<?php echo $px;?>"/>
<?php
}

?>

</pre>
<span>width=<?php echo $width;?></span>
<span>height=<?php echo $height;?></span>
<span>deep=<?php echo $deep;?></span>
<span>i=<?php echo $i;?></span>
<span>dur=<?php echo $dur;?></span>
<span>count=<?php echo $count;?></span>
<script>
var gotonew = function () {
    window.location = 'index.php?r=lifegame/makenewmap'+'&width='+document.getElementById('new_width').value;
}
</script>
