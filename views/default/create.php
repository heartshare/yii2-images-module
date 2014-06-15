<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\modules\images\models\Image $model
 */

$this->title = Yii::t('images', 'Create {modelClass}', ['modelClass' => Yii::t('images', 'image')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('images', 'Images'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
