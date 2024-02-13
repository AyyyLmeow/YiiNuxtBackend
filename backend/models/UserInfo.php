<?php

namespace backend\models;

use Yii;
use common\models\User;
use yii\helpers\Url;
// use yii\web\UploadedFile;

/**
 * This is the model class for table "UserInfo".
 *
 * @property int $id
 * @property string $surname
 * @property string $name
 * @property string $patronymic
 * @property string|null $IIN
 * @property string $birth_data
 * @property string|null $photo_url
 * @property int|null $auth_id
 *
 * @property User $auth
 */
class UserInfo extends \yii\db\ActiveRecord
{
    public $eventImage;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userInfo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_id'], 'default', 'value' => null],
            [['auth_id'], 'integer'],
            [['surname', 'name', 'patronymic', 'birth_data', 'photo_url'], 'string', 'max' => 255],
            [['iin'], 'integer'],
            [['iin'], 'string', 'max' => 12 ],
            [['iin'], 'string', 'min' => 12 ],
            [['iin'], 'unique' ],
            [['birth_data'], 'required'],
            [['auth_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['auth_id' => 'id']],
            [['photo_url'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'surname' => 'Surname',
            'name' => 'Name',
            'patronymic' => 'Patronymic',
            'iin' => 'Iin',
            'birth_data' => 'Birth Data',
            'photo_url' => 'Photo',
            'auth_id' => 'Auth ID',
        ];
    }

    /**
     * Gets query for [[Auth]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuth()
    {
        return $this->hasOne(User::class, ['id' => 'auth_id']);
    }

    public function upload()
    {

        if (true) {
            $path = $this->uploadPath() . $this->id . "." . $this->eventImage->extension;
            $this->eventImage->saveAs($path);
            $this->photo_url = $this->id . "." .$this->eventImage->extension;
            $this->save(false);
            return true;
        } else {
            return false;
        }
    }

    public function uploadPath()
    {
        return Url::to('@backend/web/images/');
    }
}
