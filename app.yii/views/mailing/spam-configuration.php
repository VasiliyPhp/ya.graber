<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SpamConfiguration */
/* @var $form ActiveForm */

$this->title = 'Конфигурация рассылки';
?>
<div class="spam-configuration row">
  <div class="col-md-6 col-md-offset-3">
		<h1>Настройки спам-рассылки</h1>
    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'interval_between_runs') ?>
        <?= $form->field($model, 'send_at_once') ?>
        <?= $form->field($model, 'atempt_count_before_stop') ?>
        <?= $form->field($model, 'enable_yandex_message_source')->checkbox() ?>
    
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

	</div>
</div><!-- spam-configuration -->
