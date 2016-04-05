<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\Customer_contact_search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customer Contacts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-contact-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Customer Contact', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'type',
            'title',
            'description:ntext',
            'remark:ntext',
            // 'listorder',
            // 'status',
            // 'lang',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
