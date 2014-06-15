<?php

namespace common\modules\images\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\Html;

/**
 * This is the model class for table "model_images".
 *
 * @property integer $id
 * @property string $name
 * @property string $original_name
 * @property string $display_name
 * @property string $description
 * @property integer $original_width
 * @property integer $original_height
 * @property string $extension
 * @property string $timestamp
 * @property string $userImage
 */
class Image extends \yii\db\ActiveRecord
{
	public $userImage;

	const THUMB_NAME = '_thumb';
	const PREVIEW_NAME = '_preview';
	const BIG_NAME = '_big';
	const ORIG_NAME = '_original';

	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%images}}';
    }

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios['update'] = ['!userImage'];
		return $scenarios;
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['name', 'original_name', 'extension', 'timestamp'], 'required'],
            [['description'], 'string'],
			[['userImage'], 'image',  'types' => 'png, jpg, gif', 'skipOnEmpty' => false, 'on' => [self::SCENARIO_DEFAULT]],
            [['original_width', 'original_height'], 'integer'],
            [['timestamp'], 'safe'],
            [['name', 'original_name', 'display_name'], 'string', 'max' => 255],
            [['extension'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
//            'id' => Yii::t('images', 'ID'),
//            'name' => Yii::t('images', 'Name'),
//            'original_name' => Yii::t('images', 'Original Name'),
//            'display_name' => Yii::t('images', 'Display Name'),
//            'description' => Yii::t('images', 'Description'),
//            'original_width' => Yii::t('images', 'Original Width'),
//            'original_height' => Yii::t('images', 'Original Height'),
//            'extension' => Yii::t('images', 'Extension'),
//            'timestamp' => Yii::t('images', 'Timestamp'),
//			'userImage' => Yii::t('images', 'Image'),
        ];
    }
	public function beforeValidate() {
		$postFiles = $_FILES;
		foreach ($postFiles as $modelName => $fArr)
			foreach ($fArr as $attrName => $attrArr)
				foreach($attrArr as $val)
					$_FILES['Image'][$attrName] = [userImage => $val];

		if(UploadedFile::getInstance($this, 'userImage')->size != 0)
		{
			$this->userImage = UploadedFile::getInstance($this, 'userImage');
			$this->original_name = UploadedFile::getInstance($this, 'userImage')->name;
		}
		elseif(!$this->isNewRecord)
		{
			$this->scenario = 'update';
		}
		parent::beforeValidate();
		return true;
	}

	public function beforeSave($insert) {
		if(parent::beforeSave($insert))
		{
			if(UploadedFile::getInstance($this, 'userImage')->size != 0)
			{
				$module = \Yii::$app->getModule('images');
				if($module->saveImages($this))
					return true;
				else
					return false;
			}
			return true;
		}
		else
			return false;
	}

	public function afterDelete() {
		if (parent::beforeDelete())
		{
			$module = \Yii::$app->getModule('images');
			$module->deleteImages($this);
			return true;
		}
		else
			return false;
	}

	private function imageByType($type = self::BIG_NAME)
	{
		return Html::img(\Yii::$app->getModule('images')->getWebsubdirs($this->timestamp).'/'.$this->name.$type.'.'.$this->extension, ['alt' => Html::encode($this->description), 'title' => Html::encode($this->description)]);
	}

	private function imageStringByType($type = self::BIG_NAME)
	{
		return \Yii::$app->getModule('images')->getWebsubdirs($this->timestamp).'/'.$this->name.$type.'.'.$this->extension;
	}

	public function getThumb()
	{
		return $this->imageByType(self::THUMB_NAME);
	}
	public function getPreview()
	{
		return $this->imageByType(self::PREVIEW_NAME);
	}
	public function getBig()
	{
		return $this->imageByType(self::BIG_NAME);
	}
	public function getOriginal()
	{
		return $this->imageByType(self::ORIG_NAME);
	}

	public function getStringThumb()
	{
		return $this->imageStringByType(self::THUMB_NAME);
	}
	public function getStringPreview()
	{
		return $this->imageStringByType(self::PREVIEW_NAME);
	}
	public function getStringBig()
	{
		return $this->imageStringByType(self::BIG_NAME);
	}
	public function getStringOriginal()
	{
		return $this->imageStringByType(self::ORIG_NAME);
	}
}
