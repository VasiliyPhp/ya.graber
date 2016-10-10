<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;

$this->title = 'Email Graber / Импорт емайлов';
?>
<div class="row">
             <div class="col-lg-12">
						 <?php if(isset($success) and $success){ ?>
						 <div class="alert alert-success">Емайлы загружены</div>
						 <?php } ?>
             <?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]);?>					 
               <h1>Выбрать сегмент и файл с эмайл <small>(txt format only with each value on new string)</small></h1>
							<?= $form->field($model, 'emailsFile')->fileInput();?>
							<?php
							echo GridView::widget([
										 'dataProvider' => $collection,
										 'layout'=>"{items}\n{pager}",
										 'columns' => [
													'segment:text:Сегмент',
													[
														'format'=>'html',
														'label'=>'Количество email',
														'value'=>function($model){
															return $model['c'] ?:0;
														}
													],
													[
														'format'=>'html',
														'content' => function($item) {
																	return Html::radio((new \app\models\ImportForm)->formName() . '[segment_id]', null,  ['value'=>$item['sid']] );
															}
													],
												]
											]);
							?>
							<div class='form-group'>
							<?= Html::submitButton('Импортировать',['class'=>'btn btn-primary btn-lg btn-block']);?>
							</div>
							<?php ActiveForm::end() ?>
            </div>
						
						
						
						
						
						
						
						
						
