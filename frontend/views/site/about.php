<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>This is the About page. You may modify the following file to customize its content:</p>

    <code><?= __FILE__ ?></code>
    <pre>
        last_login=<?=Yii::$app->user->getIsGuest()?'NULL':Yii::$app->user->identity->last_login?><br/>
        login_sess=<?=Yii::$app->user->getIsGuest()?'NULL':Yii::$app->user->identity->login_sess?><br/>
        session=<?php print_r(session_id());?><br/>
        <!--
        session=<?php print_r($_SESSION);?>
        -->
    </pre>
</div>
