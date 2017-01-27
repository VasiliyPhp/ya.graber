<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
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
		<div class='row'>
		<?= Html::beginForm(null,null,['class'=>'col-md-6']);?>
			<div class="input-group form-group">
			<?= Html::input('number','set_limit_to_all_smtps', null, ['class'=>'form-control']) ?>
				<span class="input-group-btn">
				<?= Html::submitButton('Установить лимит всем СМТП аккаунтам', ["data-confirm"=>"Установить всем аккаунтам одинаковый лимит?",'class'=>'btn btn-default']);?>
				</span>
			</div>
		<?= Html::endForm();?>
		</div>
  	<div class='row'>
		<?php $form = ActiveForm::begin(["options"=>['class'=>'col-md-6']]); ?>
			<?= $form->field($importSmtpForm, 'smtpFile')->fileInput()?>
		  <div class='form-group'>
			  <?= Html::submitButton('Импортировать',['class'=>'btn btn-default'])?>
			</div>
    <?php ActiveForm::end(); ?>
		</div>
		
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
				'summary'=>'Показано с <b>{begin}</b> по <b>{end}</b> из <b>{totalCount}</b>',
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








