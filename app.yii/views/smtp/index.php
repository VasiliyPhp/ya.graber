<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SmtpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'СМТП сервера';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="smtp-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать аккаунт смтп', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Разбанить все', ['unbann'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Забанить все', ['bann'], ['class' => 'btn btn-danger']) ?>
        <?= Html::a('Сбросить лимиты', ['unlimit'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            // 'smtp_id',
            'smtp_user:ntext',
            'smtp_pass',
            'smtp_port',
            'smtp_host',
            'smtp_protocol:ntext',
            'smtp_limit_per_day',
            'already_sent',
            'is_banned',
            'ban_reason',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>








