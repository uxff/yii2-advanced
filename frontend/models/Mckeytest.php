<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "mckeytest".
 *
 * @property integer $id
 * @property string $key
 * @property string $value
 * @property integer $flag
 * @property string $remark
 * @property string $create_time
 * @property string $update_time
 */
class Mckeytest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mckeytest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'value', 'flag', 'remark'], 'required'],
            [['flag'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['key', 'remark'], 'string', 'max' => 256],
            [['value'], 'string', 'max' => 4096]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
            'flag' => 'Flag',
            'remark' => 'Remark',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * @inheritdoc
     * @return MckeytestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MckeytestQuery(get_called_class());
    }
}
