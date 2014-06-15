= Yii2 Images = 

Модуль для управления картинками в Yii2. Позволяет привязывать картинку в виде связи к любой модели.

Автоматически сохраняются

- оригинал картинки;
- копия для отображения маленькой каринки;
- копия для отображения большой картинки;
- копия для отображения иконки.

== Подключение модуля ==

~
    'modules' => [
		'images' => [
            'class' => 'common\modules\images\Module',
			'uploadPath' => '_images/products',
			'absoluteUrl' => 'http://<адрес фронтенда>',
			…
        ],
	],
~

== Константы ==

- `const THUMB_NAME = '_thumb'` – суффикс для иконки;
- `const PREVIEW_NAME = '_preview'` – суффикс для маленькой картинки
- `const BIG_NAME = '_big'` - суффикс для большой картинки
- `const ORIG_NAME = '_original'` - суффикс для оригинальной катринки

== Параметры ==

- `public $i18n = []` – перевод. Можно задать свой с помощью `['basePath' => '<путь до файла перевода>',]`
- `public $thumbWidth = 100` – ширина иконки
- `public $thumbHeight = 100` – высота иконки
- `public $mainWight = 1200` - ширина основной (большой) картинки
- `public $mainHeight = 1200` – высота основной (большой) картинки
- `public $previewWidth = 600` – ширина preview-картинки
- `public $previewHeight = 600` – высота preview-картинки
- `public $uploadPath = false` – относительный путь до директории с файлами. По-умолчанию – `_images`, директория созадется в `/frontend/web` в случае advanced template или в `/web` в случае basic template
- `public $absoluteUrl = false` – абсолютный URL до директории с картинками. Нужен, чтобы отображать картинки в backend в случае advanced template

== Установить связь ==

~
	public function getImage0() {
		return $this->hasOne(\common\modules\images\models\Image::className(), ['id' => 'image']);
	}
~

== В контроллере при сохранении ==

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
