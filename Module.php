<?php

namespace common\modules\images;

use Yii;
use yii\helpers\Url;
use yii\base\InvalidConfigException;
use yii\web\UploadedFile;
use yii\imagine\Image;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\images\controllers';
	public $i18n = [];
	public $thumbWidth = 100;				// Thumbnail width
	public $thumbHeight = 100;				// Thumbnail height
	public $mainWight = 1200;				// Big image width
	public $mainHeight = 1200;				// Big image height
	public $previewWidth = 600;				// Preview image width
	public $previewHeight = 600;			// Preview image height
	public $constrainProportions = true;	//
	public $uploadPath = false;				// relative path to upload files (set in config)
	public $absoluteUrl = false;			// Fdsjlute url for show files in any app instance – backend or frontend (set in config)
	private $currentwebpath;				// current web path. If app template id adwanced, set to alias to frontend, if app is basic, set alias to webroot
	private $dir;							// full path to uolod dir (for example '/var/www/yiiapp/frontend/web/images/')

	public function init()
    {
        parent::init();

		Yii::setAlias('imagesmodulepath', dirname(__FILE__));
        if (empty($this->i18n)) {
            $this->i18n = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@imagesmodulepath/messages',
                'forceTranslation' => true
            ];
        }
        Yii::$app->i18n->translations['images'] = $this->i18n;

		$this->checkImagine();
		if(!$this->uploadPath)
			$this->uploadPath = '_images';

		if(!$this->absoluteUrl)
			throw new InvalidConfigException(Yii::t('images', "Please set the absolut frontend url"));

		if(!$this->checkDirectories())
			throw new InvalidConfigException(Yii::t('images', "Checking of images directory is false"));

    }

	/*
	 * Check the images directoryes
	 *
	 * @return boolean
	 */
	private function checkDirectories() {
		if(!Yii::getAlias('@imagesmodule', false))
			$setAlias = $this->setImageModuleAliases ();
		if(!$setAlias)
			return false;

		$rootImages = $this->makeRootImagesDir ();
		
		if(!$rootImages)
			throw new InvalidConfigException(Yii::t('images', "Can`t create a root images dir"));

		if($rootImages)
			return true;
		else
			return false;
	}

	private function setImageModuleAliases() {
		if(Yii::getAlias('@frontend', false))
			$this->currentwebpath = Yii::getAlias('@frontend');
		else
			$this->currentwebpath = Yii::getAlias('@webroot');

		$this->dir = $this->currentwebpath.'/web/'.$this->uploadPath;
		Yii::setAlias('imagesmodule', $this->dir);

		if(Yii::getAlias('@imagesmodule', false))
			return true;
		else
			throw new InvalidConfigException(Yii::t('images', "Alias is not set"));
	}

	private function makeRootImagesDir()
	{
		if(!Yii::getAlias('@imagesmodule'))
			$this->setImageModuleAliases ();

		if(is_dir(Yii::getAlias('@imagesmodule')) !== false)
			return true;
		
		$mkdir = mkdir(Yii::getAlias('@imagesmodule'), 0777, true);
		if($mkdir)
			return true;
		else
			return false;
	}

	private function checkImagine()
	{
		if(class_exists('yii\imagine\Image'))
			return true;
		else
			throw new InvalidConfigException(Yii::t('images', "Imagine not found. Install instructions in http://www.yiiframework.com/doc-2.0/ext-imagine-index.html"));
	}

	public function getWebsubdirs($date)
	{
		return $this->absoluteUrl.'/'.$this->uploadPath.'/'.date("Y", strtotime($date)).'/'.date("m", strtotime($date));
	}
	/*
	 * Make a subdirectoryes whis year and moth names
	 * @return patch
	 */
	public static function getSubdirs($date = null)
	{
		if($date)
			$date = strtotime ($date);
		else
			$date = strtotime ("Now");
		
		$year_subdir = Yii::getAlias('@imagesmodule').'/'.date("Y", $date);
		$month_subdir = Yii::getAlias('@imagesmodule').'/'.date("Y", $date).'/'.date("m", $date);

		if(!is_dir($year_subdir))
			mkdir ($year_subdir, 0777);
		if(!is_dir($month_subdir))
			mkdir ($month_subdir, 0777);

		if(!is_dir($year_subdir) || !is_dir($month_subdir))
		{
			throw new InvalidConfigException(Yii::t('images', "Can`t create a subdirectoryes"));
		}
		return $month_subdir;
	}

	/*
	 * Main function to save images. Call all private save images functions
	 *
	 * @param $model models\Images
	 * @return boolean
	 */
	public function saveImages(models\Image $model)
	{
		if($model == null)
			return false;

		$this->saveOriginal($model);
		$this->saveThumb($model);
		$this->saveBigImage($model);
		$this->savePreviewImage($model);
		return true;
	}

	private function getImagineobj()
	{
		$imagine = new yii\imagine\Image;
		return $imagine->getImagine();
	}


	private function saveOriginal(models\Image $model)
	{
		if(!$model->name)
			$model->name = uniqid();
		
		$model->extension = $model->userImage->getExtension();
		if(!$model->display_name)
			$model->display_name = $model->userImage->getBaseName();

		if($model->userImage->saveAs($this->subdirs.'/'.$model->name.$model::ORIG_NAME.'.'.$model->extension))
		{
			$originalImage = $this->getImagineobj()->open($this->subdirs.'/'.$model->name.$model::ORIG_NAME.'.'.$model->extension);
			$imageBox = $originalImage->getSize();
			$model->original_width = $imageBox->getWidth();
			$model->original_height = $imageBox->getHeight();
			return true;
		}
		else
			throw new InvalidConfigException(Yii::t('images', "Can`t save an original image"));
	}

	private function saveThumb(models\Image $model)
	{
		$originalImage = $this->subdirs.'/'.$model->name.$model::ORIG_NAME.'.'.$model->extension;
		$thumb = yii\imagine\Image::thumbnail($originalImage, $this->thumbHeight, $this->thumbWidth);
		$thumb->save($this->subdirs.'/'.$model->name.$model::THUMB_NAME.'.'.$model->extension);
	}

	private function saveBigImage(models\Image $model)
	{
		$destName = $model->name.$model::BIG_NAME.'.'.$model->extension;
		$originalImage = $this->getImagineobj()->open($this->getSubdirs($model->timestamp).'/'.$model->name.$model::ORIG_NAME.'.'.$model->extension);
		if($model->original_width <= $this->mainWight && $model->original_height <= $this->mainHeight)
		{
			$originalImage->save($this->getSubdirs($model->timestamp).'/'.$destName);
			return true;
		}

		if($model->original_width > $this->mainWight)
		{
			$originalImage->resize($originalImage->getSize()->widen($this->mainWight));
		}
		if($model->original_height > $this->mainHeight)
		{
			$originalImage->resize($originalImage->getSize()->heighten($this->mainHeight));
		}
		$originalImage->save($this->getSubdirs($model->timestamp).'/'.$destName);
		return true;
	}

	private function savePreviewImage(models\Image $model)
	{
		$destName = $model->name.$model::PREVIEW_NAME.'.'.$model->extension;
		$originalImage = $this->getImagineobj()->open($this->getSubdirs($model->timestamp).'/'.$model->name.$model::ORIG_NAME.'.'.$model->extension);
		if($model->original_width <= $this->previewWidth && $model->original_height <= $this->previewHeight)
		{
			$originalImage->save($this->getSubdirs($model->timestamp).'/'.$destName);
			return true;
		}
		if($model->original_width > $this->previewWidth)
		{
			$originalImage->resize($originalImage->getSize()->widen($this->previewWidth));
		}
		if($model->original_height > $this->previewHeight)
		{
			$originalImage->resize($originalImage->getSize()->heighten($this->previewHeight));
		}
		$originalImage->save($this->getSubdirs($model->timestamp).'/'.$destName);
		return true;
	}

	public function deleteImages(models\Image $model)
	{
		$subdirs = $this->getSubdirs($model->timestamp);
		foreach (glob($subdirs.'/'.$model->name.'*') as $filePath)
		{
			unlink($filePath);
		}
		$model->description = implode(" ", glob($subdirs.'/'.$model->name.'*'));
		return true;
	}
}
