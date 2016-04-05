<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
//\Yii::$app->view->renderFile('@app/views/myform/myform_header.php');
//$this->renderFile('@app/views/myform/myform_header.php');
?>
<?php $this->beginContent('@app/views/myform/myformheader.php'); ?>
<!--这么纠结的代码-->
<?php $this->endContent(); ?>
<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'email') ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
<pre ><?php print_r($countries);?></pre>