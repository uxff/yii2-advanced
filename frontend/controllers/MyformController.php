<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use frontend\models\EntryForm;
use frontend\models\Country;


class MyformController extends Controller
{
    // ...其它代码...
    /**
     * Say
     *
     * @param string $message
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionSay($message = 'Hello')
    {
        return $this->render('say', ['message' => $message]);
    }

    public function actionEntry()
    {
        $model = new EntryForm;

        // 获取 country 表的所有行并以 name 排序
        $countries = Country::find()->orderBy('name')->all();
        //print_r($countries);

        // 获取主键为 “US” 的行
        $country = Country::findOne('US');

        // 输出 “United States”
        //echo $country->name;

        // 修改 name 为 “U.S.A.” 并在数据库中保存更改
        //$country->name = 'U.S.A.';
        //$country->save();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // 验证 $model 收到的数据

            // 做些有意义的事 ...


            return $this->render('entry-confirm', ['model' => $model, 'countries'=>$countries]);
        } else {
            // 无论是初始化显示还是数据验证错误
            //$this->renderFile('@app/views/myform/myform_header.php');
            //echo $this->render('myformheader');

            return $this->render('entry', ['model' => $model, 'countries'=>$countries]);
        }
    }
    public function actionCountryShow()
    {
        $query = Country::find();

        $pagination = new Pagination([
            'defaultPageSize' => 5,
            'totalCount' => $query->count(),
        ]);

        $countries = $query->orderBy('name')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();


        return $this->render('country-by-page', [
            'countries' => $countries,
            'pagination' => $pagination,
        ]);
    }
}