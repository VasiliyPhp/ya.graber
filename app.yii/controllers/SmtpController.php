<?php

namespace app\controllers;

use Yii;
use app\models\Smtp;
use app\models\SmtpSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * SmtpController implements the CRUD actions for Smtp model.
 */
class SmtpController extends Controller
{
    public function behaviors()
    {    
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Smtp models.
     * @return mixed
     */
    public function actionIndex()
    {
		if($limit = Yii::$app->request->post('set_limit_to_all_smtps')){
			Smtp::setLimitToAllSmtps($limit);
		}
		$searchModel = new SmtpSearch();
		$dataProvider = $searchModel->search( Yii::$app->request->queryParams );
		$importSmtpForm = new \app\models\ImportSmtpForm;
		if ( $importSmtpForm->load(Yii::$app->request->post() ) ) {
			$importSmtpForm->import();
			yii::$app->session->addFlash('info', 'SMTP аккаунты успешно добавлены');
		} elseif (Yii::$app->request->get('export_smtp')){
			return Yii::$app->response->sendContentAsFile($importSmtpForm->export(), 'smtp.txt');
		}
		return $this->render('index', [
			'importSmtpForm' => $importSmtpForm,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
    }

    /**
     * Displays a single Smtp model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Smtp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Smtp();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->smtp_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Smtp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->smtp_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Smtp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
		
		public function actionBann(){
			
			Smtp::bann();
			
			$this->redirect('index');
			
		}
		
		public function actionUnbann(){
			
			Smtp::unbann();
			
			$this->redirect('index');
			
		}
		
		public function actionUnlimit(){
			
			Smtp::unlimit();
			
			$this->redirect('index');
			
		}
    /**
     * Finds the Smtp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Smtp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Smtp::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
