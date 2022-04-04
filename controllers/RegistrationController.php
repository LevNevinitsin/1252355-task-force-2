<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\City;

class RegistrationController extends Controller
{
    public function actionIndex()
    {
        $user = new User();
        $cities = City::find()->select(['name', 'id'])->indexBy('id')->column();

        if (Yii::$app->request->getIsPost()) {
            $user->load(Yii::$app->request->post());

            if ($user->validate()) {
                $user->password = Yii::$app->security->generatePasswordHash($user->password);
                $user->save(false);
                $this->goHome();
            }
        }

        return $this->render('registration', [
            'userModel' => $user,
            'cities' => $cities,
        ]);
    }
}
