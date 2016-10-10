<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Smtp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="smtp-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'smtp_user')->textInput() ?>

    <?= $form->field($model, 'smtp_pass')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'smtp_port')->textInput() ?>

    <?= $form->field($model, 'smtp_protocol')->textInput() ?>
  
    <?= $form->field($model, 'smtp_host')->textInput() ?>

    <?= $form->field($model, 'smtp_limit_per_day')->textInput() ?>
   
	 <?= $form->field($model, 'is_banned')->textInput() ?>

	 <?= $form->field($model, 'already_sent')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
