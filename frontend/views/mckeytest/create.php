<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Mckeytest */

$this->title = 'Create Mckeytest';
$this->params['breadcrumbs'][] = ['label' => 'Mckeytests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mckeytest-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
