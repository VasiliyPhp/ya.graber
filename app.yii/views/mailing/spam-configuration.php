<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SpamConfiguration */
/* @var $form ActiveForm */

$this->title = 'Конфигурация рассылки';
?>
<div class="spam-configuration">
  <h1>Настройки спам-рассылки</h1>
    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'interval_between_runs') ?>
        <?= $form->field($model, 'send_at_once') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- spam-configuration -->
