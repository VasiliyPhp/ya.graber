<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Smtp]].
 *
 * @see Smtp
 */
class SmtpQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Smtp[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Smtp|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}