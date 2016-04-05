<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[Mckeytest]].
 *
 * @see Mckeytest
 */
class MckeytestQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Mckeytest[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Mckeytest|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}