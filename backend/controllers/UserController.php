<?php

namespace backend\controllers;

use app\models\ImageUpload;
use Yii;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\UserInfo;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
            'sort' => ['attributes' => ['id','username']]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new User();
        $userInfo = new UserInfo();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $userInfo->load($this->request->post()) && $this->hashAndSave($model) && $this->giveAuthIdAndSave($model,$userInfo) /*$model->save()*/) {
                return $this->redirect(['view', 'id' => $model->id]);
                Yii::$app->session->setFlash('success', 'User successfully created');
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'userInfo' => $userInfo,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->userInfo->load($this->request->post()) && $model->UserInfo->save() && $this->hashOnlyPassAndSave($model)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    protected function hashAndSave($model)
    {
        $model->setPassword($model->password_hash);
        $model->generateAuthKey();
        $model->generateEmailVerificationToken();
        $model->generatePasswordResetToken();

        return $model->save();
    }
    protected function hashOnlyPassAndSave($model)
    {   
        $oldpass = $model->getOldAttribute('password_hash');
        if(!$oldpass == $model->password_hash) {
        $model->setPassword($model->password_hash);
        }

        return $model->save();
    }
    protected function giveAuthIdAndSave($User,$userInfo){

        $userInfo->auth_id = $User->id;
        return $userInfo->save();
    }
    public function actionUpload($id)
    {
    $model = $this->findModel($id);
    
     if (Yii::$app->request->isPost) {
         $model->userInfo->eventImage = UploadedFile::getInstance($model->userInfo, 'photo_url');
         if ($model->userInfo->upload()) {
             return $this->redirect(['view', 'id' => $model->id]);
    
         }
     }
    
     return $this->render('upload', ['model' => $model]);
    }

}
