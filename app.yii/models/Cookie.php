<?php

namespace app\models;

use Yii;
use yii\base\Model;


class Cookie extends Model
{
	const COOKIE_FILE = '.cookie_yandex.txt';
	
    public $cookie;
   
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
    		['cookie', 'required'], 
        ];
    }
		
    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'cookie' => 'Куки яндекса',
        ];
    }
	
    public function save()
    {
        file_put_contents(self::COOKIE_FILE, $this->cookie);
		return true;
    }
	
    public static function get()
    {
		$_cookie = file_exists(self::COOKIE_FILE)? file_get_contents(self::COOKIE_FILE) : '' ;
        $cookie = new static();
		$cookie->cookie = $_cookie;
		return $cookie;
    }

}








