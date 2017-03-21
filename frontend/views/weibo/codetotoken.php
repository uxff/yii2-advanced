<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Weibo Auth code to token';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>this page should not be refresh, and do not request again</p>
    <p>msg = <?=$msg?></p>

    <code><?= __FILE__ ?></code>
    <pre>
    <?=json_encode($info);?>
    </pre>
</div>
