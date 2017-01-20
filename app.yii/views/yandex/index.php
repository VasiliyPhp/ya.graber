<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = 'Email Graber';
$session = Yii::$app->session;

$this->registerJs("
$('#delete_email_btn').click(function(){
	var val = $('#delete_email_input').val();
	if(!val){
		retufn ;
	}
	$.ajax({
	  data : {email:val},
		url : '" . Url::to(['yandex/delete-email']) . "',
		beforeSend : function(){
			$('body').css('cursor','waiter');
		},
		complete : function(rs){
			console.log(rs);
			$('body').css('cursor','initial');
		},
		error : function(){
			alert('some error coccured');
		},
		success : function(ev){
			alert(ev.response || ev.error);
		}
	})
})
"
, yii\web\View::POS_LOAD, 'delete_email');
?>
<h1><?= $this->title ?></h1>

 <?php if($session->hasFlash('yandexBan')){?>
 <div class="alert alert-error">
 <?= $session->getFlash('yandexBan'); ?>
 </div>
<?php }?>
						  
<div class="row">
           <?php $form = ActiveForm::begin(['id' => 'query-form']); ?>
           <div class="col-lg-4">
                    <?= $form->field($queryForm, 'segment')->dropDownList($segmentItems) ?>
             </div>
           <div class="col-lg-4">
                     <?= $form->field($queryForm, 'query')->textarea(['style'=>'resize:none']) ?>
             </div>
           <div class="col-lg-4">
                    <?= $form->field($queryForm, 'amount')->textInput(['type'=>'number'])?>
            </div>
</div>
<div class="row">
					<div class="form-group col-lg-12">
							<?= Html::submitButton('Начать', ['class' => 'btn btn-primary', 'name' => 'query-button']) ?>
					</div>
<?php ActiveForm::end(); ?>
 </div>
<?php if($result) { extract($result); ?>
<div class="row">
            <div class="col-lg-12">
						<h2>Результаты текущего запроса</h2>
						<p>Просканировано страниц из выдачи яндекса - <b><?=$yandexResult?></b></p>
						<p>Дополнительно найдено страниц - <b><?=($viewedPages - $yandexResult)?></b> (всего - <?=$viewedPages;?>)</p>
						<p>Найдено уникальных емайлов - <b><?=$emails?></b></p>
            </div>
</div>
<?php } ?>
<div class="row">
  <div class="col-md-offset-3 col-md-6" >
	  <div class='form-group'>
		  <input type=email class=form-control id="delete_email_input">
		</div>
	  <div class='form-group'>
		  <button class='btn btn-warning ' id="delete_email_btn">Удалить эмайл</button>
		</div>
	</div>
</div>
<div class="row">
             <div class="col-lg-12 bg-info">						
							<h2>Статистика</h2>
							<?php
							echo GridView::widget([
										 'dataProvider' => $statistics,
										 'layout'=>"{items}\n{pager}",
										 'columns' => [
														'd:text:Время',
														'seg:text:Сегмент',
														'q:text:Поисковый запрос',
														'c:integer:Собрано емайлов',
														'count_page:integer:Просканировано страниц',
														]
											]);
							?>
            </div>
</div>