<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статистика рассылок';
$this->params['breadcrumbs'][] = $this->title;
?>


    <h1><?= Html::encode($this->title) ?></h1>
		<div class=row >
			<div class=col-md-4 >
			<?= Nav::widget([
				'options'=>[
					'class'=>'nav nav-pills',
				],
				'items'=>[
					[
						'label'=>'Тема письма',
						'url'=>array_merge(['mailing/stat'], yii::$app->request->get(), ['by'=>'theme']),
					],
					[
						'label'=>'SubId',
						'url'=>array_merge(['mailing/stat'], yii::$app->request->get(), ['by'=>'subid']),
						'active'=>!yii::$app->request->get('by') || yii::$app->request->get('by') == 'subid',
					]
				],
			]);
			?>
			</div>
			<div class=col-md-4 >
			<?= Nav::widget([
				'options'=>[
					'class'=>'nav nav-pills ',
				],
				'items'=>[
					[
						'label'=>'За этот месяц',
						'url'=>array_merge(['mailing/stat'], yii::$app->request->get(), ['date'=>'month']),
						'active'=>yii::$app->request->get('date') == 'month',
					],
					[
						'label'=>'За вчера',
						'url'=>array_merge(['mailing/stat'], yii::$app->request->get(), ['date'=>'yestarday']),
						'active'=>yii::$app->request->get('date') == 'yestarday',
					],
					[
						'label'=>'За сегодня',
						'url'=>array_merge(['mailing/stat'], yii::$app->request->get(), ['date'=>'today']),
						'active'=>yii::$app->request->get('date') == 'today' || !yii::$app->request->get('date'),
					]
				],
			]);
			?>
			</div>
		</div>
		<?= GridView::widget([
        'dataProvider' => $dataProvider,
			  'emptyText'=>'Нет данных',
				'summary'=>'Показано с <b>{begin}</b> по <b>{end}</b>',
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
						$by,
						'c_opened',
				    'c_clicked',
						'c_unsubscribed',
            [
						'class' => 'yii\grid\ActionColumn',
						'template'=>'{delete} {view}',
						],
        ],
    ]); ?>