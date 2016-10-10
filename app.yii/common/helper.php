<?php

namespace app\common;

class Sender{
	
}

class Helper {
	static function getGraberStat(){
		// $sql = 'select count_page, count(*) c, query q, segment seg, from_unixtime(date, "%d-%m-%Y %H:%i") d from '
		             // . \app\models\Passing::tableName().' p inner join '
		             // . \app\models\Email::tableName().' e using(passing_id) inner join '
								 // . \app\models\Segment::tableName().' s using(segment_id) group by p.passing_id order by date desc';
		$sql = 'select count_page, c, query q, segment seg , from_unixtime(date, "%d-%m-%Y %H:%i") d from'
		   . ' (select  count(*) c, passing_id from email group by passing_id) tmp'
			 . ' inner join passing using(passing_id) inner join segment using(segment_id)  order by date desc';
		$count = \Yii::$app->db->createCommand(
		             'SELECT COUNT(*) FROM ('.$sql.') as c'
						)->queryScalar();

		$dataProvider = new \yii\data\SqlDataProvider([
				'sql' => $sql,
				'totalCount' => $count,
				'pagination' => [
						'pageSize' => 20,
				],
		]);
		return $dataProvider;
	}
	public static function l($m){
		$t = microtime(true);
		if(is_array($m) or is_object($m)){
			$m = print_r($m, true);
		}
		$t1=(float) round($t,3);$t2=(int)floor($t);
		$t =substr(round($t1-$t2,3),2);
		echo '<pre>'.date('H:i:s.'),$t,' - '.$m.'</pre>';
		flush();
		ob_flush();
	}
}