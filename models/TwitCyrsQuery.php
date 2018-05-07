<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TwitCyrs]].
 *
 * @see TwitCyrs
 */
class TwitCyrsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TwitCyrs[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TwitCyrs|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
