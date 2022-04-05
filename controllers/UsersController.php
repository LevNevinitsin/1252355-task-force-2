<?php
namespace app\controllers;

use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{
    public function actionView($id)
    {
        $contractorsIds = User::find()->select(['id'])->where(['role_id' => 2])->column();

        if (!$id || !in_array($id, $contractorsIds)) {
            throw new NotFoundHttpException();
        }

        $user = User::findOne($id);

        return $this->render('user-profile', [
            'user' => $user,
        ]);
    }

    public function actionLogout() {
        \Yii::$app->user->logout();
        return $this->goHome();
    }
}
