<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Smtp */

$this->title = $model->smtp_user;
$this->params['breadcrumbs'][] = ['label' => 'Смтп аккаунты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="smtp-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->smtp_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->smtp_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Уверены, что хотите удалить?',
                'method' => 'post',
            ],
        ]) ?>
				<?= Html::a('Создать новый', ['create', 'id' => $model->smtp_id], ['class' => 'btn btn-success']) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'smtp_id',
            'smtp_user:ntext',
            'smtp_pass',
            'smtp_host',
            'smtp_port',
            'smtp_protocol:ntext',
            'smtp_limit_per_day',
            'is_banned',
            'first_run_date',
            'already_sent',
        ],
    ]) ?>

</div>
