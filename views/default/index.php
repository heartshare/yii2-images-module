<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\DataColumn;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\modules\images\models\ImageQuery $searchModel
 */

$this->title = Yii::t('images', 'Images');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('images', 'Create {modelClass}', ['modelClass' => Yii::t('images', 'image')]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'class' => DataColumn::className(),
				'value' => 'thumb',
				'header' => Yii::t('images', 'Image'),
				'format' => 'raw',
			],

            'original_name',
            'display_name',
            'description:ntext',
            // 'original_width',
            // 'original_height',
            // 'extension',
             'timestamp:date',

            ['class' => 'yii\grid\ActionColumn', 'contentOptions' => ['style' => 'white-space: nowrap;']],
        ],
    ]); ?>

</div>
