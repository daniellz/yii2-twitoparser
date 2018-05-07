<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Cyrs]].
 *
 * @see Cyrs
 */
class CyrsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Cyrs[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Cyrs|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
