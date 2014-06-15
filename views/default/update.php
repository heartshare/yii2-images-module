<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\modules\images\models\Image $model
 */

$this->title = Yii::t('images', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('images', 'image'),
]) . ' ' . $model->display_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('images', 'Images'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->display_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('images', 'Update');
?>
<div class="image-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
