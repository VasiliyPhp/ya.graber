<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статистика запусков';
$this->params['breadcrumbs'][] = $this->title;
?>


    <h1><?= Html::encode($this->title) ?></h1>
		<?= GridView::widget([
        'dataProvider' => $dataProvider,
			  'emptyText'=>'Нет данных',
				'summary'=>'Показано с <b>{begin}</b> по <b>{end}</b>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
						'launch_date',
						'segment',
						'total_sending',
						'bad_sending'
						// [
						// 'class' => 'yii\grid\ActionColumn',
						// 'template'=>'{delete} {view}',
						// ],
        ],
    ]); ?>