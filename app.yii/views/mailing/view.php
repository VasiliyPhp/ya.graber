<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SpamLaunches */

$this->title = 'Статистика рассылки '.$model->launch_id.' за ' . $model->launch_date;
$this->params['breadcrumbs'][] = ['label' => 'Статистика рассылок', 'url' => ['stat']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="spam-launches-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Delete', ['delete', 'id' => $model->launch_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'launch_id',
            'launch_date',
            'total_sending',
            'bad_sending',
        ],
    ]) ?>

</div>
