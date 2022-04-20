<?php
namespace app\controllers;

use app\models\User;
use Yii;
use yii\web\NotFoundHttpException;

class UsersController extends SecuredController
{
    public function actionView($id)
    {
        $contractorsIds = User::find()->select(['id'])->where(['role_id' => 2])->column();

        if (!$id || !in_array($id, $contractorsIds)) {
            throw new NotFoundHttpException();
        }

        $currentUser = User::findOne(Yii::$app->user->id);
        $user = User::findOne($id);

        return $this->render('user-profile', [
            'currentUser' => $currentUser,
            'user' => $user,
        ]);
    }

    public function actionLogout() {
        \Yii::$app->user->logout();
        return $this->goHome();
    }
}
