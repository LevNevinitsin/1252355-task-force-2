<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use app\models\LoginForm;

class LandingController extends Controller
{
    public $layout = 'landing';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function ($rule, $action) {
                            $this->redirect('/tasks');
                        }
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $loginForm = new LoginForm();

        if (\Yii::$app->request->getIsPost()) {
            $loginForm->load(\Yii::$app->request->post());
            Yii::$app->response->format = Response::FORMAT_JSON;
            $errors = ActiveForm::validate($loginForm);

            if ($errors) {
                return $errors;
            }

            $user = $loginForm->getUser();
            \Yii::$app->user->login($user);
            $this->redirect('/tasks');
        }

        $this->view->params['model'] = $loginForm;
        return $this->render('index');
    }
}
