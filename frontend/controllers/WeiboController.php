<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\extensions\weiboauth\WeiboOAuthV2;

/**
 * Customer_contactController implements the CRUD actions for Customer_contact model.
 */
class WeiboController extends Controller
{
    const WB_AKEY = '2213721265';
    const WB_SKEY = '2c472f8aadcdceb22095d97e0a30d688';
    const WB_CALLBACK_URL = 'http://yii2a.lo/oauthcallback.php';
    public $o;
    public $wb_url;
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Customer_contact models.
     * @return mixed
     */
    public function actionIndex()
    {

        $this->o = new WeiboOAuthV2(self::WB_AKEY, self::WB_SKEY);
        $this->wb_url = $this->o->getAuthorizeURL( self::WB_CALLBACK_URL, 'code', 'wb');
        return $this->render('index', [
            'wb_url' => $this->wb_url,
            'info' => $msg,
        ]);
    }

    /**
     * Displays a single Customer_contact model.
     * @param integer $id
     * @return mixed
     */
    public function actionCodetotoken($code)
    {
        $keys = array();
        $keys['code'] = $code;
        $keys['redirect_uri'] = self::WB_CALLBACK_URL;
        try {
            $this->o = new WeiboOAuthV2(self::WB_AKEY, self::WB_SKEY);
            $token = $this->o->getAccessToken('code', $keys);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return $this->render('codetotoken', [
                'wb_url' => $this->wb_url,
                'info' => $token,
                'msg' => $msg,
            ]);
        }
        // 授权成功 成功后需要把授权信息存放在数据库
        
        return $this->redirect(['site/index', 'from'=>'oauth']);
    }

    /**
     * Creates a new Customer_contact model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customer_contact();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Customer_contact model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Customer_contact model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Customer_contact model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer_contact the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customer_contact::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
