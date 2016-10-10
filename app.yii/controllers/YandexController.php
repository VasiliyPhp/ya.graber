<?php

namespace app\controllers;
use Yii;
use app\common\parser;
use app\models\Segment;
use app\models\Title as Title;
use app\models\Email;
use app\models\Passing;
use app\models\QueryForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\data\SqlDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;

class YandexController extends \yii\web\Controller{
	
	private $countFoundEmails = 0,
	        $countViewedPages = 0,
					$countPagesTakenFromYandex = 0,
					$passing;
	 
  public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        // 'actions' => ['*'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
   }

	public function actionIndex(){
      $result = false;
			$statistics = \app\common\helper::getGraberStat();
			$segment = new Segment();
			$segmentItems = ArrayHelper::map(Segment::find()->all(), 'segment_id', 'segment');
       // $passing = new Passing();
      $queryForm = new QueryForm();
			if($queryForm->load(YII::$app->request->post()) && $queryForm->validate()){
				$this->grabbing($queryForm);
				$result = ['emails'=>$this->countFoundEmails,
				           'yandexResult'=>$this->countPagesTakenFromYandex,
				           'viewedPages'=>$this->countViewedPages
									 ];
			}
			return $this->render('index',[
											'segment'=>$segment,
											'queryForm'=>$queryForm,
											'segmentItems'=>$segmentItems,
											'statistics'=>$statistics,
											'result'=>$result
											]);
    }
	public function actionDeleteEmail($email = null){
		$req = yii::$app->request;
		$resp = yii::$app->response;
		$resp->format = \yii\web\Response::FORMAT_JSON;
		if(!$req->isAjax){
			return ['error'=>'is not ajax'];			
		}
		if(!$email){
			return ['error'=>'there is no email'];
		}
		if(Email::deleteItemByEmail($email)){
			return ['response'=>'Емайл удален'];
		}	else {
			return ['response'=>'Емайл не найден'];			
		}
	}
	public function actionImport(){
	  $model = new \app\models\ImportForm();
		if ( $model->load(\yii::$app->request->post()) and $model->validate() )	{
			$success = $model->import() ? true : false;
		}
		
		$collection = new ActiveDataProvider([
				'query' => Segment::stat(),
				'pagination' => [
						'pageSize' => 30,
				],
		]);
		return $this->render('import',compact('success', 'collection', 'model'));
	
	}
	
	public function actionExport() {
		if ( isset(Yii::$app->request->post()['sid'], Yii::$app->request->post()['format']) )	{
			return $this->export(Yii::$app->request->post()['sid'], Yii::$app->request->post()['format']);
		}
		$collection = new ActiveDataProvider([
				'query' => Segment::stat(),
				'pagination' => [
						'pageSize' => 30,
				],
		]);
		return $this->render('export', [
																'collection'=>$collection,
															 ]
											  );
	}
	
	private function export($sid, $format) {
		$emails = Email::find()
							->where(['segment_id'=>$sid])
							->asArray()
							->all();
		$segment = Segment::findOne($sid);
		$segment = $segment->segment;
		$emails = array_map(function ($email) {
															return $email['email'];
														},
													$emails
												);
		$method = $format.'Export';
		if ( method_exists($this, $method) ) {
			$filename = $segment.'.'.date('Y-m-d.H-i');
			return $this->$method($emails, $filename);
		} else {
			throw new \yii\web\BadRequestHttpException('Неверный формат импорта емайл');
		}
	}
	
	private function csvExport($data, $filename) {
			
			if( count($data) ) {
				foreach( $data as $d ) {
					$sheet[][] = $d;
				}
			} else {
				$this->goBack();
			}
			
			$phpExcel = new \alexgx\phpexcel\PhpExcel();
			
			$doc = $phpExcel->create();
			
			$doc->getActiveSheet()->fromArray($sheet, null, 'A1');
			
			$phpExcel->responseFile($doc, $filename.'.csv');
	}
	
	private function xlsExport($data, $filename) {
			
			if( count($data) ) {
				foreach( $data as $d ) {
					$sheet[][] = $d;
				}
			} else {
				$this->goBack();
			}
			$phpExcel = new \alexgx\phpexcel\PhpExcel();
			
			$doc = $phpExcel->create();
			
			$doc->getActiveSheet()->fromArray($sheet, null, 'A1');
			
			$phpExcel->responseFile($doc, $filename.'.xls');
	}
	
	private function txtExport($data, $filename) {
		$content = implode("\r\n", $data);
		$response = new \yii\web\Response;
		return $response->sendContentAsFile($content, $filename.'.txt', 'text/plain');
	}
	
	public function actionDeleteTitle($title_id = null) {
		if($title_id) {
			$this->deleteItem('title', $title_id);
			Yii::$app->session->setFlash('settingSuccess', 'Страница удалена');
		}else {
			Yii::$app->session->setFlash('settingError', 'Не передан id страницы');
		}
		return $this->redirect(Url::toRoute('yandex/settings'));
	}
	public function actionDeleteSegment($segment_id = null) {
		if($segment_id) {
			$this->deleteItem('segment', $segment_id);
			Yii::$app->session->setFlash('settingSuccess', 'Сегмент удалён');
		}else {
			Yii::$app->session->setFlash('settingError', 'Не передан id сегмента');
		}
		return $this->redirect(Url::toRoute('yandex/settings'));
	}
	private function deleteItem($name, $id){
		$item = '\app\models\\' . ucfirst($name);
		$item = (new $item)->findOne($id);
		return $item ? $item->delete() : null;
	}
	public function actionClearSegment($segment_id){
		Email::updateAll(['sended'=>0],['segment_id'=>$segment_id]);
		Yii::$app->session->setFlash('settingSuccess', 'Емайлы обнулены');
		return $this->redirect(Url::toRoute('yandex/settings'));
	}
	public function actionSettings(){
      $segment = new Segment();
      $title = new Title();
			$query = new Query;
			$segmentItems = new ArrayDataProvider([
					'allModels' => $query
					  ->select('segment.segment_id, count(segment.segment_id) c, concat_ws("  ", segment, count(segment.segment_id)) seg')
						->from('segment')
						->innerJoin('email', 'email.segment_id = segment.segment_id')
						->groupBy('segment.segment_id')
						->orderBy('c desc')
						->all(),
					'pagination' => false,
			]);
			$query = new Query;
			$titleItems = new ArrayDataProvider([
					'allModels' => $query->from('title')->all(),
					'pagination' => false,
			]);
			//$titleItems = ArrayHelper::map(Title::find()->all(), 'title_id', 'title');
     
			return $this->render('settings',[
											'segment'=>$segment,
											'segmentItems'=>$segmentItems,
											'title'=>$title,
											'titleItems'=>$titleItems
											]);
    }
	
	public function actionSavesegment(){
		$segment = new Segment();
		if($segment->load(Yii::$app->request->post()) && 
				$segment->validate()){
			$segment->save();
			Yii::$app->session->setFlash('settingSuccess', 'Сегмент '.$segment->segment.' добавлен');
		}
		return $this->redirect(Url::toRoute('yandex/settings'));
	}
	
	public function actionSavetitle(){
		$title = new Title();
		$title->load(Yii::$app->request->post());
		if($title->validate()){
			$title->save();
			Yii::$app->session->setFlash('settingSuccess', 'Страница '.$title->title.' добавлена');
		}
		return $this->redirect(Url::toRoute('yandex/settings'));
	}
		
	private function grabbing($query) {
		ini_set('default_socket_timeout', 7);
		$try = 10;
		$domen = 'yandex.ru';
		$amount = $query->amount;
		$passing = new Passing();
		$passing->date = time();
		$passing->query = $query->query;
		$passing->segment_id = $query->segment;
		$passing->save();
		$this->passing = $passing;
		
		$userAgent = Parser::getMobileUserAgent();
		$ip = null;
		$links = [];
		for($i=0; $i<$amount ; $i++){
			$searchUrl = $this->getSearchUrl($query->query, $i);
			$referer = $this->getSearchUrl($query->query, $i - 1);
			$referer = $referer === $searchUrl ? null : 'http://' . $domen . $referer;
			list($responseHeaders, $searchResult, $httpCode) = Parser::query($domen, false, $searchUrl,
																												$referer, null, $ip, $userAgent);
			if($httpCode != 200) {
				if(!$try){
					Yii::$app->session->setFlash('yandexBan', 'По каким то причинам получен бан от янлекса');
					$this->goHome();
				}
				$userAgent = Parser::changeMobileUserAgent($userAgent);
				$ip = Parser::changeIp();
				$try--;
				$i--;
				continue;
			}
			$try = 10;
			$links = array_merge($links, $this->getLinks($searchResult));
		}
		$query = null;
		foreach($links as $link){
			$this->countPagesTakenFromYandex++;
			$this->grabSite($link);
		}
	}
	
	private function grabSite($link){
		$this->grabPage($link, true);
	}
	
	private function grabPage($page, $reset = false){
		static $viewedPage = [];
		if(in_array($page, $viewedPage)){
			return ;
		}
		// $this->log('мы на '.$page);
		$this->passing->count_page++;
		$this->passing->save();
		$this->countViewedPages++;
		$viewedPage[] = $page;
		$content = @file_get_contents($page);
		if(!$content){
			return false;
		}
		$this->findEmails($content, $page);
		$links = $this->findLinks($content, $page);
		if($links) {
		  // $this->log('найдено ссылок '.count($links));
			foreach($links as $link){
				if(!in_array($link, $viewedPage)){
					$this->grabPage($link);
				}
			}
		}
	}
	
	private function findLinks($content, $page) {
		$neededTitle = Title::find()->all();
		if(!$neededTitle){
			return false;
		}
		foreach($neededTitle as &$nt) {
			$need[] = iconv('utf-8','windows-1251',$nt->title);
			$need[] = $nt->title;
		}
		$links = [];
		$nt = implode('|', $need);
		$regularExpression = '~<a.*href="([^"]+)"[^>]*>('.$nt.')</a>~Usi';
		preg_match_all($regularExpression, $content, $ls);
		if(count($ls[1])) {
			$ls[1] = array_unique($ls[1]);
			foreach($ls[1] as $key=>$l) {
				if(strpos($l,'#') === 0) {
					unset($ls[1][$key]);
				} else {
					$ls[1][$key] = $this->normalizeUrl($ls[1][$key], $page);
				}
			}
		}
		if(count($ls[1])) {
			return $ls[1];
		}
		return false;
	}
	
	private function normalizeUrl($url, $site){
		$parts = parse_url(trim($site));
		$site = $parts['scheme'].'://'.$parts['host'];
		$fullPath = $site.(isset($parts['path']) ? : '/' );
		if ( strpos( $url, '/' ) === 0 ) {
			$url = $site.$url;
		} elseif ( strpos( $url, 'http://' ) !== 0 ) {
			$url = preg_replace('~^(http.*//.*/)\S*$~', '$1'.$url ,$fullPath);
		}
		// $this->log('нормализовано '.$url);
		return $url;
	}
	
	// finding and saving emails
	private function findEmails($content, $page) {
		preg_match_all(Parser::$emailPattern, $content, $emails);
		$emails = array_unique($emails[0]);
		// $this->log('emails : '.count($emails));			
		
		if(count($emails)){
			foreach($emails as $email) {
				if( preg_match('~(rating@mail\.ru|\S+@example\.com|\.jpg|\.jpeg|\.png|\.gif)~i', $email) ){
					continue;
				}
				if($this->saveEmail($email, $page)) {
					$this->countFoundEmails++;
					// $this->log($email.' сохранен');
				} else {
					// $this->log($email.' - уже существует');
				}
			}
		}
	}
	
	private function saveEmail($_email, $page) {
		$email = Email::find()->where(['email'=>$_email])->count();
		if($email) {
			return false;
		}
		$email = new Email();
		$email->segment_id = $this->passing->segment_id;
		$email->email = $_email;
		$email->passing_id = $this->passing->passing_id;
		$email->save();			
		return true;
	}
	
	// getting links from serp
	private function getLinks($searchResult){
		$neededLinks = [];
		$isMobile = true;
		$stopSites = [
					'youtube\.com',
					'yandex\.ru', 
					'google\.com', 
					'mail\.ru',
					'aliexpress\.ru
					'];
		$pattern = $isMobile?
				'~<li\s*>\s*<a.*href="(.+)".*>.*<\/a>.*b-results__text.*>.*<\/span.*<\/li~sU':
				'/class=\"serp\-item(?:\sserp\-item_plain_yes|\sserp\-item_first_yes\sserp\-item_plain_yes).*>.*<h2.*>(.*)<\/h2>.*serp\-item__text.*>(.*)<\/div>/sU';
		
		preg_match_all($pattern, $searchResult, $out);
		foreach ($out[1] as $found) {
			$flag = true;
			foreach ($stopSites as $stopSite) {
				if(preg_match('~.*'.$stopSite.'/~',$found)) {
					$flag = false;
				}
			}
			$flag and ($neededLinks[] = $found);
		}
		return $neededLinks;
	}
	
	private function getSearchUrl($query, $page){
		$page = ($page > 0) ? "p={$page}&" : null;
		$query = '/msearch?'.$page.'text='.rawurlencode($query);
		return $query;
	}
	
	private function log($m){
		$t = microtime(true);
		$t1 = (float) round($t,3);$t2=(int)floor($t);
		$t = substr(round($t1-$t2,3),2);
		echo '<p><pre>'.date('H:i:s.'),$t,' - '.$m.'</pre></p>';
	}
}
