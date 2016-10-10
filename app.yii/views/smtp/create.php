<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Smtp */

$this->title = 'Добавление смтп аккаунта';
$this->params['breadcrumbs'][] = ['label' => 'Аккаунты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$model->loadDefaultValues();
?>
<div class="smtp-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
