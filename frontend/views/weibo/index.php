<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Weibo Auth';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><a href="<?=$wb_url?>">here login</a></p>

    <code><?= __FILE__ ?></code>
    <pre>
    <?=json_encode($info);?>
    </pre>
</div>
