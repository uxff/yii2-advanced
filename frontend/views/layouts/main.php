<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'A dot publicserver MapTopo',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'OAuth', 
            'items'=>[
                ['label' => 'Weibo', 'url' => ['weibo/index']],
                ['label' => 'TencentWeibo', 'url' => ['tencent/index']],
                ['label' => 'Wechat', 'url' => ['wechat/index']],
            ]
        ],
        ['label' => 'Dudads', 
            'items' =>[
                ['label' => 'Contact', 'url' => ['/site/contact']],
                ['label' => 'Customer contact', 'url' => ['/customer_contact/index']],
                ['label' => 'Weibo Grasp mining', 'url' => ['/grasp/mining']],
                ['label' => 'Weibo Grasp put', 'url' => ['/grasp/put']],
            ],
        ],
        ['label' => 'MapTopo', 
            'items'=>[
                ['label' => 'MapTopo', 'url' => ['/mctest/index']],
                ['label' => 'Lifegame', 'url' => ['/lifegame/index']],
                ['label' => 'Lifegame2', 'url' => ['/lifegame2/index']],
                ['label' => 'Galaxy Simulate', 'url' => ['/galaxysim/index']],
            ]
        ],
        ['label' => 'About', 'url' => ['/site/about']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $menuItems[] = [
            'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; A dot publicserver <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
