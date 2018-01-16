<?php

namespace frontend\controllers;
use Yii;


class GalaxysimulateController extends \yii\web\Controller
{
    public function actionIndex() {
        return $this->render('index', []);
    }
}
