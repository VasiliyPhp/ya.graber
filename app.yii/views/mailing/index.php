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
        <?= $form->field($model, 'setListUnsuscribe')->checkbox(); ?>
	</div>
	<div class='col-xs-3  switched-for' id='for-yandex' >
        <?= $form->field($model, 'prefixFile')->fileInput(['accept'=>'.txt']) ?>
        <?= $form->field($model, 'rootFile')->fileInput(['accept'=>'.txt']) ?>
	</div>
	<div class='col-xs-3  switched-for' id='for-html' >
        <?= $form->field($model, 'messageSubject') ?>
	</div>
	<div class='col-xs-6' >
        <?= $form->field($model, 'messageTemplate')->textarea(['cols'=>20]); 
		?>
		<div class=help-block >
			<code>%a%</code> будет заменен на емайл адрес пользователя<br/>
			<code>%unsubscribe%</code> будет заменен на ссылку отписаться. Полный синтаксис <code>%unsubscribe-текст ссылки-css стили%</code>
			;
		</div>
		<div class="form-group">
			<?= Html::submitButton('Начать', ['class' => 'btn btn-primary']) ?>
		</div>
	</div>
    <?php ActiveForm::end();?>
</div><!-- mailing-index -->
<?php ob_start() ?>
(function($){
$('.btn').click(function(){
$('#spamform-messagetemplate').val(CKEDITOR.instances['spamform-messagetemplate'].getData())
})
CKEDITOR.replace( 'spamform-messagetemplate' );
$('#<?=strtolower($model->formName().'-messagetemplate');?>')[0]
switchSource.call($('#<?=strtolower($model->formName().'-messagesource');?>')[0], '', 0);
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
	$this->registerJsFile(Yii::$app->homeUrl . 'js/ckeditor/ckeditor.js', ['position'=>static::POS_HEAD]);
?>