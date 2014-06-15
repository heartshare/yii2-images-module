<?php

namespace common\modules\images\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\images\models\Image;

/**
 * ImageQuery represents the model behind the search form about `common\modules\images\models\Image`.
 */
class ImageQuery extends Image
{
    public function rules()
    {
        return [
            [['id', 'original_width', 'original_height'], 'integer'],
            [['name', 'original_name', 'display_name', 'description', 'extension', 'timestamp'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Image::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'original_width' => $this->original_width,
            'original_height' => $this->original_height,
            'timestamp' => $this->timestamp,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'original_name', $this->original_name])
            ->andFilterWhere(['like', 'display_name', $this->display_name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'extension', $this->extension]);

        return $dataProvider;
    }
}
