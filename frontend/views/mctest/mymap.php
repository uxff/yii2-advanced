<div style="position:absolute">
<i class="fa fa-cubes"></i>
</div>
<a href="index.php?r=mctest/passivatemap&ver=<?php echo $ver;?>&rad=1&px=<?php echo $px;?>">passivate(radius=1)</a>&nbsp;
<a href="index.php?r=mctest/passivatemap&ver=<?php echo $ver;?>&rad=2&px=<?php echo $px;?>">passivate(radius=2)</a>&nbsp;
<a href="index.php?r=mctest/passivatemap&ver=<?php echo $ver;?>&rad=2&union=1&px=<?php echo $px;?>">passivate(radius=1& 2)</a>&nbsp;
<a href="index.php?r=mctest/sharpmymap&ver=<?php echo $ver;?>">sharp</a>&nbsp;
<a href="index.php?r=mctest/refine&ver=<?php echo $ver;?>&zoomTimes=2">refine 2</a>&nbsp;
<a href="index.php?r=mctest/refine&ver=<?php echo $ver;?>&zoomTimes=3">refine 3</a>&nbsp;
<a href="index.php?r=mctest/refine&ver=<?php echo $ver;?>&zoomTimes=4">refine 4</a>&nbsp;
<a href="index.php?r=mctest/mymap&ver=<?php echo $pre;?>">undo</a>&nbsp;
&nbsp;&nbsp;
<a href="index.php?r=mctest/mymap&ver=<?php echo $ver;?>&px=<?php echo (int)($px+1);?>">zoom +</a>&nbsp;
<a href="index.php?r=mctest/mymap&ver=<?php echo $ver;?>&px=<?php echo (int)($px-1);?>">zoom -</a>&nbsp;
&nbsp;&nbsp;
width:<input type="text" name="width" id="new_width" value="<?php echo $width;?>" style="width:30px"/>
<a href="javascript:;" id="btn_new" onclick="gotonew()">new</a>&nbsp;
<a href="index.php?r=mctest/clearmap&ver=<?php echo $ver;?>">clear</a>&nbsp;
<a href="index.php?r=mctest/clearall">clearall</a>&nbsp;
<pre>
<?php
echo $mapStr;
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
