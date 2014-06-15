<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;

/**
 * @var yii\web\View $this
 * @var common\modules\images\models\ImageQuery $model
 * @var yii\widgets\ActiveForm $form
 */
$this->registerCss(".image-search form {background: #eee; padding: 1em; margin: 1.5em 0; border-radius: .3em;}\n.image-search form {display: none};\n");
$this->registerJs("$('.image-search').on('click', 'a.toggleForm', function(e){ e.preventDefault(); $('.image-search form').toggle(); });", $this::POS_READY);
?>

<div class="image-search">
	<a class="toggleForm" href="#">Поиск</a>
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
		'type' => ActiveForm::TYPE_HORIZONTAL,
		'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'original_name') ?>

	<?= $form->field($model, 'display_name') ?>

    <?php echo $form->field($model, 'extension') ?>

    <?php echo $form->field($model, 'timestamp')->widget(DatePicker::classname(),
			[
				'options' => ['placeholder' => Yii::t('images', 'Timestamp')],
				'pluginOptions' => [
					'autoclose'=>true,
					'language' => 'ru',
					'todayHighlight' => true,
					'todayBtn' => true,
					 'format' => 'dd-mm-yyyy',
					],
			]); ?>

    <div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
        <?= Html::submitButton(Yii::t('images', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('images', 'Reset'), ['class' => 'btn btn-default']) ?>
		</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
