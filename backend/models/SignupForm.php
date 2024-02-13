<?php

namespace backend\models;

use backend\models\UserInfo;
use Yii;
use yii\base\Model;
use common\models\User;
use yii\web\UploadedFile;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $eventImage;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $userInfo = new userInfo();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $userInfo->eventImage = $this->eventImage;
        $auth = Yii::$app->authManager;
        $user->save();
        $userRole = $auth->getRole('baseUser');
        $auth->assign($userRole,$user->getId());
        return $this->setAuthIdPhotoUrlAndSave($user, $userInfo) && $this->sendEmail($user);
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }

    protected function setAuthIdPhotoUrlAndSave($user, $userInfo){

        $userInfo->auth_id = $user->id;
        $userInfo->surname = '';
        $userInfo->name = '';
        $userInfo->patronymic = '';
        $userInfo->birth_data = '';
        $userInfo->photo_url = '';
//        $userInfo->unique = '';
        if (isset($userInfo->eventImage)) {
            $path = $userInfo->uploadPath() . $userInfo->auth_id . "." . $this->eventImage->extension;
            $userInfo->eventImage->saveAs($path);
            $userInfo->photo_url = $userInfo->auth_id . "." . $userInfo->eventImage->extension;
            return $userInfo->save(false);
        }
        else{
//            $test =
            $userInfo->photo_url = "default.jpg" ;
            return $userInfo->save(false);
        }
    }
}
