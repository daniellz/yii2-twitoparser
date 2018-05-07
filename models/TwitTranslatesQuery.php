<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TwitTranslates]].
 *
 * @see TwitTranslates
 */
class TwitTranslatesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TwitTranslates[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TwitTranslates|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
