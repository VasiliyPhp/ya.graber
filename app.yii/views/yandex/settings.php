<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\grid\GridView;
use app\components\grid\ActionColumn;

$this->title = 'Настройки';
$session = Yii::$app->session;
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
?>
<h1></h1>
 <?php if($session->hasFlash('settingSuccess')){?>
 <div class="alert alert-success">
   <span class="glyphicon glyphicon-check"></span>
   <?= $session->getFlash('settingSuccess'); ?>
 </div>
<?php }?>
<?php if($session->hasFlash('settingError')){?>
 <div class="alert alert-error">
 <?= $session->getFlash('settingError'); ?>
 </div>
<?php }?>

<div class="row">
	 <div class="col-lg-4">
		<h1>Настройки</h1>
		<?php $form = ActiveForm::begin(['id' => 'add-segment', 'action'=>Url::toRoute('yandex/savesegment')]); ?>

		<?= $form->field($segment, 'segment')->label('Добавить новый сегмент:') ?>

		<div class="form-group">
		<?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'addsegment-button']) ?>
		</div>

		<?php ActiveForm::end(); ?>
		
		
		<?php $form = ActiveForm::begin(['id' => 'add-title', 'action'=>Url::toRoute('yandex/savetitle')]); ?>

		<?= $form->field($title, 'title')->label('Добавить новую страницу:') ?>

		<div class="form-group">
		<?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'addtitle-button']) ?>
		</div>

		<?php ActiveForm::end(); ?>
		
		
		<?php $form = ActiveForm::begin(['id' => 'add-title', 'action'=>Url::toRoute('yandex/save-cookie')]); ?>

			<?= $form->field($cookie, 'cookie')->textarea()->label('Куки яндекса') ?>

			<div class="form-group">
			<?= Html::submitButton('Сохранить куки', ['class' => 'btn btn-primary']) ?>
			</div>

		<?php ActiveForm::end(); ?>
		
	</div>
	<div class="col-lg-4">
	<h1>Страницы</h1>
	<?php echo GridView::widget([
					 'dataProvider' => $titleItems,
					 'layout'=>"{items}\n{pager}",
					 'columns' => [
									['class'=>yii\grid\SerialColumn::className()],
									[
										'label'=>'НАЗВАНИЕ',
										'attribute'=>'title',
									],
									[
										'class'=>yii\grid\ActionColumn::className(),
										'template'=>'{delete}',
										'urlCreator'=>function($action, $model){
											return Url::toRoute(['yandex/delete-title','title_id'=>$model['title_id']]);
										}
									],
							]
						]);
	?>
	</div>
	<div class="col-lg-4">
	<h1>Сегменты</h1>
	<?php echo GridView::widget([
					 'dataProvider' => $segmentItems,
					 'layout'=>"{items}\n{pager}",
					 'columns' => [
										['class'=>yii\grid\SerialColumn::className()],
										[
											'label'=>'НАЗВАНИЕ',
											'attribute'=>'seg'
										],
										[
											'class'=>yii\grid\ActionColumn::className(),
											'template'=>'{delete} {clear}',
											'buttons'=>[
											  'clear'=>function($url, $model){
												return Html::a('<span class="glyphicon glyphicon-erase"></span>', ['yandex/clear-segment', 'segment_id'=>$model['segment_id']],['title'=>'Сбросить отправленные емайлы']);
												},
											],
											'urlCreator'=>function($action, $model){
												return Url::toRoute(['yandex/delete-segment','segment_id'=>$model['segment_id']]);
											},
										],
									],
					 ]);
	?></div>
</div>