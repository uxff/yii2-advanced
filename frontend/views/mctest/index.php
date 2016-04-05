<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Mckeytest */
/* @var $form ActiveForm */
?>
<div class="mc-test-index">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'key') ?>
        <?= $form->field($model, 'value') ?>
        <?= $form->field($model, 'flag') ?>
        <?= $form->field($model, 'remark') ?>
        <?= $form->field($model, 'create_time') ?>
        <?= $form->field($model, 'update_time') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- mc-test-index -->
