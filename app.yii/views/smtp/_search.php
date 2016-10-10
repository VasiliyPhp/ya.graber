<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SmtpSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="smtp-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'smtp_id') ?>

    <?= $form->field($model, 'smtp_user') ?>

    <?= $form->field($model, 'smtp_pass') ?>

    <?= $form->field($model, 'smtp_port') ?>

    <?= $form->field($model, 'smtp_protocol') ?>
    
		<?= $form->field($model, 'smtp_host') ?>

    <?= $form->field($model, 'smtp_limit_per_day') ?>

    <?= //$form->field($model, 'first_run_date') ?>

    <?= //$form->field($model, 'already_sent') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
