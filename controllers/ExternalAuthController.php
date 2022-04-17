<?php
namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use app\components\AuthHandler;

class ExternalAuthController extends Controller
{
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
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'customer-auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onCustomerAuthSuccess'],
            ],
            'contractor-auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onContractorAuthSuccess'],
            ],
            'customer-auth-registration' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onCustomerAuthSuccess'],
                'successUrl' => '/registration',
            ],
            'contractor-auth-registration' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onContractorAuthSuccess'],
                'successUrl' => '/registration',
            ],
        ];
    }

    public function onCustomerAuthSuccess($client)
    {
        $roleId = 1;
        (new AuthHandler($client, $roleId))->handle();
    }

    public function onContractorAuthSuccess($client)
    {
        $roleId = 2;
        (new AuthHandler($client, $roleId))->handle();
    }
}
