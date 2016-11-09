<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SpamForm */
/* @var $form ActiveForm */
$this->title = 'Тестовый запуск (проверка аккаунтов)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class=" row mailing-index ">
		<?php $form = ActiveForm::begin(); ?>
		<div class=col-xs-3 >
        <?= $form->field($model, 'smtp')->dropDownList($smtp); ?>
        <?= $form->field($model, 'from') ?>
        <?= $form->field($model, 'to'); ?>
    </div>
 		<div class='col-xs-6' >
        <?= $form->field($model, 'theme')?>
        <?= $form->field($model, 'body')->textarea(['rows'=>10]) ?>
		 <div class="form-group">
					<?= Html::submitButton('Начать', ['class' => 'btn btn-primary']) ?>
			</div>
		</div>
	  <div class=col-xs-3 >
		  <?php if($res){?>
			  <div class="alert alert-success">Письмо отправлено</div>
			<?php }elseif($res===false){?>
			  <div class="alert alert-danger">Не отправлено</div>
			<?php }elseif($error){?>
			  <div class="alert alert-danger"><?=$error?></div>
			<?php }?>
		</div>
    <?php ActiveForm::end();?>
</div><!-- mailing-index -->
