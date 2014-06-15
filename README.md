Подключение модуля

~
    'modules' => [
		'images' => [
            'class' => 'common\modules\images\Module',
			'uploadPath' => '_images/products',
			'absoluteUrl' => 'http://<адрес фронтенда>',
        ],
	],
~

Установить связь

~
	public function getImage0() {
		return $this->hasOne(\common\modules\images\models\Image::className(), ['id' => 'image']);
	}
~

В контроллере при сохранении

~
	if ($model->load(Yii::$app->request->post())) {
		$imageModel = new \common\modules\images\models\Image;
		$imageModel->display_name = $model->name;
		if($imageModel->validate() && $imageModel->save())
			$model->image = $imageModel->id;
		else
			$model->addError ('image', $imageModel->getErrors());

		if($model->save())
			return $this->redirect(['view', 'id' => $model->id]);
		else
			return $this->render('create', ['model' => $model]);
~
