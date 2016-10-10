<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = 'Email Graber / Экспорт емайлов';

?>
<h1>Выбрать сегмент и формат</h1>
<div class="row">
             <div class="col-lg-12y">						
							<?= Html::beginForm(['yandex/export'], 'post') ?>
							<div class='form-group center-block bg-primary text-center'><div class="lead">Формат выходных данных</div>
							<?= Html::radio('format', true, ['label' => 'txt','value'=>'txt','required'=>'']);?>
							<?= Html::radio('format', false, ['label' => 'csv','value'=>'csv','required'=>'']);?>
							<?= Html::radio('format', false, ['label' => 'xls','value'=>'xls','required'=>'']);?>
							</div>
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
														'content' => function($model) {
																	return Html::radio('sid',false,['value'=>$model['sid'],'required'=>'']) ;
															}
													],
													// [
													// 'class'=>\yii\grid\ActionColumn::className(),
													// 'controller'=>'segment',
													// ]
												]
											]);
							?>
							<div class='form-group'>
							<?= Html::submitButton('Экспортировать',['class'=>'btn btn-primary btn-lg btn-block']);?>
							</div>
							<?= Html::endForm() ?>
            </div>
</div>