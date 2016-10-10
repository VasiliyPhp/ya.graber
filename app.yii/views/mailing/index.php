<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SpamForm */
/* @var $form ActiveForm */
$this->title = 'Запустить компанию';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class=" row mailing-index ">
  <?php $flag and print Html::a('Остановить', ['stop'], ['class' => 'form-control btn btn-danger']);?>
    <h1>Установки для начала рассылки</h1>
		<?php $form = ActiveForm::begin(['action'=>['mailing/get/spamming'], 'options' => ['enctype' => 'multipart/form-data']]); ?>
		<div class=col-xs-3 >
        <?= $form->field($model, 'segmentSource')->dropDownList($segments); ?>
        <?= $form->field($model, 'messageSource')->dropDownList($messageSource); ?>
        <?= $form->field($model, 'messageFrom'); ?>
        <?= $form->field($model, 'handleUrl'); ?>
        <?= $form->field($model, 'subId'); ?>
    </div>
 		<div class='col-xs-3  switched-for' id='for-yandex' >
        <?= $form->field($model, 'prefixFile')->fileInput() ?>
        <?= $form->field($model, 'rootFile')->fileInput() ?>
		</div>
 		<div class='col-xs-3  switched-for' id='for-html' >
        <?= $form->field($model, 'messageSubject') ?>
		</div>
 		<div class='col-xs-6' >
        <?= $form->field($model, 'messageTemplate')->textarea(['rows'=>10]) ?>
		  <div class=help-block >Допустимо использование тега <code>%@%</code>, который будет заменен на емайл адрес пользователя</div>
       <div class="form-group">
            <?= Html::submitButton('Начать', ['class' => 'btn btn-primary']) ?>
        </div>
		</div>
    <?php ActiveForm::end();?>
</div><!-- mailing-index -->
<?php ob_start() ?>
  (function($){
		switchSource.call($('#<?=strtolower($model->formName().'-messagesource');?>')[0], '', 0)
		$('#<?=strtolower($model->formName().'-messagesource');?>').change(switchSource);
		function switchSource(ev, delay){
			('undefined' == typeof delay) && (delay = 300)
			var suf = this.value;
			$('.switched-for:visible').fadeOut(delay, function() {
				$('#for-' + suf).fadeIn(400);
			})
		}
	})(jQuery)
<?php 
$js = ob_get_contents();
ob_end_clean();
$this->registerJs($js, 5);
?>