<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;

/**
 * @var yii\web\View $this
 * @var common\modules\images\models\Image $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="image-form">
    <?php $form = ActiveForm::begin([
		'options' => ['enctype'=>'multipart/form-data'],
	]); ?>

	<?= $form->field($model, 'display_name')->textInput(['maxlength' => 255]) ?>
	<?= $form->field($model, 'description')->textarea();?>
	<?= $form->field($model, 'userImage')->widget(FileInput::classname(), [
		'options' => ['multiple' => false, 'accept' => 'image/*'],
		'pluginOptions' => [
			'showUpload' => false,
			'showRemove' => false,
			'previewFileType' => 'image',
			'initialPreview' => (!$model->isNewRecord) ? [$model->thumb] : [],
		]
	]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('images', 'Create') : Yii::t('images', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
