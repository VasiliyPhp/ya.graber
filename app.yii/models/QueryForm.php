<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class QueryForm extends Model
{
    public $segment;
    public $query;
    public $amount;
		
    public function rules()
    {
        return [
            [['segment', 'query', 'amount'], 'required'],
						[['amount'],'integer']
						];
    }
}
