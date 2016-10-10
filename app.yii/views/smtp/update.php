<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Smtp */

$this->title = 'Обновить смтп аккаунт: ' . ' ' . $model->smtp_user;
$this->params['breadcrumbs'][] = ['label' => 'Смтп аккаунты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->smtp_user, 'url' => ['view', 'id' => $model->smtp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="smtp-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
