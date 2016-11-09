<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
		<link rel="shortcut icon" type="image/png" href="<?=yii::$app->homeUrl;?>favicon.png" >
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    if(!Yii::$app->user->isGuest){
			NavBar::begin([
					'brandLabel' => 'Email graber',
					'brandUrl' => Yii::$app->homeUrl,
					'options' => [
							'class' => 'navbar-inverse navbar-fixed-top',
					],
			]);
			echo Nav::widget([
					'options' => ['class' => 'navbar-nav navbar-right'],
					'items' => [
							['label' => 'Главная', 'url' => ['/yandex/index']],
							['label' => 'Настройки', 'url' => ['/yandex/settings']],
							['label' => 'База эмайл адресов', 
							   'items' => [
								   ['label' =>'Import', 'url' => ['/yandex/import']],
								   ['label' =>'Export', 'url' => ['/yandex/export']],
									],
							],
							['label' => 'Рассылки', 
								'items' => [
									 ['label' => 'Запустить компанию', 'url' => ['/mailing/index']],
									 ['label' => 'Статистика рассылок', 'url' => ['/mailing/stat']],
									 ['label' => 'Статистика запусков', 'url' => ['/mailing/sended-stat']],
									 ['label' => 'СМТП сервера', 'url' => ['/smtp/index']],
									 ['label' => 'Настойки рассылок', 'url' => ['/mailing/spam-configuration']],
									 ['label' => 'Проверить аккаунт', 'url' => ['/mailing/test']],
									], 
							],[
									'label'=>'Выйти',
									'url' => ['site/logout'],
									'linkOptions'=>[
										'data' => [
												'confirm' => 'Уверены, что хотите выйти?',
												'method' => 'post',
										],
									],
								]
					],
			]);
			NavBar::end();
    }
    ?>

    <div class="container">
				<?= Breadcrumbs::widget([
				    'homeLink' => [
							 'label' => 'Ya.Grab',  // required
							 'url' => Yii::$app->homeUrl,      // optional, will be processed by Url::to()
							 // 'template' => 'own template of the item', // optional, if not set $this->itemTemplate will be used
							],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
