<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "customer_contact".
 *
 * @property integer $id
 * @property string $type
 * @property string $title
 * @property string $description
 * @property string $remark
 * @property integer $listorder
 * @property integer $status
 * @property string $lang
 */
class Customer_contact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'title', 'description', 'remark', 'status'], 'required'],
            [['description', 'remark'], 'string'],
            [['listorder', 'status'], 'integer'],
            [['type', 'lang'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'title' => 'Title',
            'description' => 'Description',
            'remark' => 'Remark',
            'listorder' => 'Listorder',
            'status' => 'Status',
            'lang' => 'Lang',
        ];
    }
}
