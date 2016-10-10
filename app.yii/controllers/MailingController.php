<?php

namespace app\controllers;

use app\common\spamer;
use app\models\smtp;
use app\models\SpamForm;
use app\models\SpamConfiguration;
use yii\web\controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use app\models\SpamLaunches;

class MailingController extends Controller 
{
	 
  public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['unsubscribe', 'image', 'forward'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
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
	public function actionTest(){
		$mailer = \yii::$app->mailer;
		$transport = [
		         'class' => 'Swift_SmtpTransport',
             'host' => 'smtp.yandex.ru',  // e.g. smtp.mandrillapp.com or smtp.gmail.com
             'username' => 'autosib.rst@yandex.ru',
             'password' => '2146063q',
             'port' => '465', // Port 25 is a very common port too
             'encryption' => 'ssl'
		];
		try{
			$mailer->setTransport($mailer->createTransport($transport));
			echo $mailer->compose()
			->setHtmlBody('my owner body')
			->setReturnPath('autosib.rst@yandex.ru')
			->setSubject('my subject')
			->setTo('mister.sergeew-v@yandex.ru')
			->setFrom('mister.president@obama.gov')
			->send();
		}catch(\Exception $e){
			echo $e->getMessage();
		}
	}
	
	private function createMessageProvider($data) {
		switch( $data->messageSource) {
		case 'yandex' : 
			$message = new \app\common\YandexMessageProvider($data->prefixFile->tempName, $data->rootFile->tempName, $data->messageTemplate); break;
		case 'html' : 
		  $message = new \app\common\HtmlMessageProvider($data->messageSubject, $data->messageTemplate); break;
		default : 
		  $message = null;
		}
		return $message;
	}
	public function actionSpamming(){
		ini_set('ignore_user_abort', 1);
		$confId = 1;
		$flag = file_exists(\yii::getAlias('@runtime') . '/flag');
		$result = $error = null;
		$model = new \app\models\SpamForm;
		if($model->load(\Yii::$app->request->post()) and $model->validate()){		
			$smtp = new \app\models\Smtp;
			$email = \app\models\Email::createProviderBySegmentId($model->segmentSource);
			$message = $this->createMessageProvider($model);
			$loger = new \app\models\SpamLaunches;
			$configTmp = SpamConfiguration::find()->where(['id'=>$confId])->select('send_at_once, interval_between_runs')->one();
			$config['from'] = $model->messageFrom;
			$config['delay'] = $configTmp->interval_between_runs;
			$config['atonce'] = $configTmp->send_at_once;
			$config['subId'] = $model->subId;
			$config['handlerUrl'] = $model->handleUrl;
			try {
				session_write_close();
				$spamer = new Spamer($config);
				$spamer->setEmailProvider($email)
							 ->setMessageProvider($message)
							 ->setLogger($loger)
							 ->setSmtpProvider($smtp);
				if( $spamer->run() ){
					$result = 'spamming is over succefully without errors';
				} else {
					$error = 'spamming is over with errors';
				}
    		$flag = file_exists(\yii::getAlias('@runtime') . '/flag');
				$this->layout = 'menuless';
			} catch ( \app\common\SpamerException $spEx ) {
				$error = $spEx->getMessage();
			}
			if($error){
				$type = 'error';
	     	$msg = $error;
			}	else {
				$type = 'success';
				$msg = $result;
			}
			
			$response = yii::$app->response;
			$response->content = sprintf('type - <b>%s</b>, response - <b>%s</b>. %s', $type, $msg, yii\helpers\Html::a('На главную', ['mailing/index']));
			
			return $response;;
		}
	}
	// Главная страница, на которой показаны результаты рассылок 
	// и интерфейс для запуска рассылки
	public function actionIndex(){
		$flag = file_exists(\yii::getAlias('@runtime') . '/flag');
		$segments = \app\models\Segment::find()
		  ->innerJoin('email', 'segment.segment_id=email.segment_id')
			->select(' concat_ws(" - ", segment, count(*)) as seg, segment.segment_id as seg_id')
			->groupBy('segment.segment_id')
			->indexBy('seg_id')
		  ->asArray()->column();
    // echo '<pre>'; exit(print_r($segments));
		$messageSource = ['yandex'=>'yandex', 'html'=>'html'];
		$model = new \app\models\SpamForm;
				
		return $this->render('index', compact('flag', 'model', 'segments', 'messageSource'));
		
	}
	
	public function actionStop(){
		file_exists(\yii::getAlias('@runtime') . '/flag') and unlink(\yii::getAlias('@runtime') . '/flag');
		return $this->redirect(['index']);
	}
	/**
	 * Displays a single SpamLaunches model.
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
	 * Deletes an existing SpamLaunches model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
			$this->findModel($id)->delete();

			return $this->redirect(['stat']);
	}

	public function actionUnsubscribe($email = null, $date = null, $uid = null, $theme = null, $subid = null){
		call_user_func_array(['\app\models\LaunchItem', 'unsubscribe'], func_get_args());
		$response = new \yii\web\response;
		$response->content = 'Вы успешно отписаны от рассылки';
		return $response;
	}
	
	public function actionForward($email = null, $date = null, $uid = null, $theme = null, $subid = null, $adr = null){
		call_user_func_array(['\app\models\LaunchItem', 'click'], func_get_args());
		if($adr){
			return $this->redirect($adr);
		}
		$response = new \yii\web\response;
		$response->content = 'Some error occured. Have you lost your mind?';
		$response->statusCode = 400;
		return $response;
	}
	
	public function actionImage($email = null, $date = null, $uid = null, $theme = null, $subid = null){
		// j(func_get_args());
		call_user_func_array(['\app\models\LaunchItem', 'open'], func_get_args());
		$response = new \yii\web\response;
		return $response->sendFile('fon3.png', null, ['inline'=>true]); 
	}
	
	// setting spam configuration
	public function actionSpamConfiguration($id = 1){
		$model = SpamConfiguration::findOne($id);
		if(!$model){
			$model = new SpamConfiguration();
			$model->loadDefaultValues();
		}
		// print_r($model);exit;
		if ($model->load(Yii::$app->request->post())) {
				if ($model->validate()) {
						$model->save();
				}
		}

		return $this->render('spam-configuration', [
				'model' => $model,
		]);
	}
	
	// looking Results of spam mailing
	public function actionStat ($by = 'subid', $date = 'today') {
		$ar = \app\models\SpamLaunches::stat($by, $date);
		
		// exit(l($ar));
		$dataProvider = new ActiveDataProvider([
		  'query' => $ar,
			'pagination'=>[
			  'pagesize'=>30,
			],
		]);
		
		
		return $this->render('stat', compact('dataProvider', 'by'));
		
	}

	/**
	 * Finds the SpamLaunches model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return SpamLaunches the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
			if (($model = SpamLaunches::findOne($id)) !== null) {
					return $model;
			} else {
					throw new NotFoundHttpException('The requested page does not exist.');
			}
	}
}

