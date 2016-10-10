<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Smtp;

/**
 * SmtpSearch represents the model behind the search form about `app\models\Smtp`.
 */
class SmtpSearch extends Smtp
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['smtp_id', 'is_banned', 'smtp_port', 'smtp_limit_per_day', 'first_run_date', 'already_sent'], 'integer'],
            [['smtp_user', 'ban_reason', 'smtp_pass', 'smtp_host', 'smtp_protocol'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Smtp::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
						'pagination'=>[
						  'pageSize'=>100,
						],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'smtp_id' => $this->smtp_id,
            'smtp_port' => $this->smtp_port,
            'smtp_limit_per_day' => $this->smtp_limit_per_day,
            'first_run_date' => $this->first_run_date,
            'already_sent' => $this->already_sent,
            'is_banned' => $this->is_banned,
        ]);

        $query->andFilterWhere(['like', 'smtp_user', $this->smtp_user])
            ->andFilterWhere(['like', 'smtp_pass', $this->smtp_pass])
            ->andFilterWhere(['like', 'ban_reason', $this->ban_reason])
            ->andFilterWhere(['like', 'smtp_host', $this->smtp_host])
            ->andFilterWhere(['like', 'smtp_protocol', $this->smtp_protocol]);

        return $dataProvider;
    }
}







