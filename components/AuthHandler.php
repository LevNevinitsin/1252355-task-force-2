<?php
namespace app\components;

use app\models\Auth;
use app\models\User;
use app\models\City;
use app\models\Role;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use LevNevinitsin\Business\Exception\AuthException;
use LevNevinitsin\Business\Service\UserService;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{
    /**
     * @var ClientInterface
     */
    private $client;
    private $roleId;

    public function __construct(ClientInterface $client, int $roleId)
    {
        $this->client = $client;
        $this->roleId = $roleId;
    }

    /**
     * Handles successful authentication via Yii auth component
     *
     * @return void
     */
    public function handle()
    {
        $clientName = $this->client->getTitle();
        $attributes = $this->client->getUserAttributes();
        $id = ArrayHelper::getValue($attributes, 'id');
        $session = Yii::$app->session;

        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $id,
        ])->one();

        if ($auth) {
            $user = $auth->user;
            $userRoleId = $user->role_id;

            if ($this->roleId !== $userRoleId) {
                $rolesNamesMap = [
                    'customer' => 'заказчик',
                    'contractor' => 'исполнитель',
                ];

                $userRoleName = $rolesNamesMap[Role::findOne($userRoleId)->name];
                $session->setFlash('authError', "Вы уже зарегистрированы в роли \"$userRoleName\".");
                return;
            }

            Yii::$app->user->login($user);
            return;
        }

        $email = ArrayHelper::getValue($attributes, 'email');

        if ($email !== null && User::find()->where(['email' => $email])->exists()) {
            $session->setFlash('authError',
                "Пользователь с таким же адресом email, как в аккаунте $clientName, уже зарегистрирован
                на сайте. Войдите стандартным способом."
            );

            return;
        }

        $clientCityName = ArrayHelper::getValue($attributes, 'city.title');

        if (!$city = City::findOne(['name' => $clientCityName])) {
            if (YII_DEBUG) {
                throw new AuthException("There is no city called \"$clientCityName\" in database.");
            }

            $session->setFlash('authError',
                "Не удалось создать пользователя через $clientName. Войдите стандартным способом."
            );

            return;
        };

        $this->createAuthUser($id, $email, $city, $attributes, $clientName);
    }

    /**
     * Creates User and Auth records in database if succesfully validated
     *
     * @param integer $id User id from auth client (vk, fb etc)
     * @param string $email User email from auth client
     * @param City $city User city instance based on city name from auth client
     * @param array $attributes All attributes from auth client
     * @param string $clientName Auth client title
     * @return void
     */
    private function createAuthUser(int $id, string $email, City $city, array $attributes, string $clientName)
    {
        $name = ArrayHelper::getValue($attributes, 'first_name') . ' ' . ArrayHelper::getValue($attributes, 'last_name');
        $birthdate = Yii::$app->formatter->asDate(ArrayHelper::getValue($attributes, 'bdate'), 'php:Y-m-d');
        $photo = ArrayHelper::getValue($attributes, 'photo');
        $password = Yii::$app->security->generateRandomString(6);
        $passwordHash = Yii::$app->security->generatePasswordHash($password);

        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
            'city_id' => $city->id,
            'photo' => $photo,
            'role_id' => $this->roleId,
            'birthdate' => $birthdate
        ]);

        if (!$user->validate()) {
            $this->handleFailedValidation($user, $clientName);
            return;
        }

        $transaction = User::getDb()->beginTransaction();
        $user->save();

        $auth = new Auth([
            'user_id' => $user->id,
            'source' => $this->client->getId(),
            'source_id' => (string) $id,
        ]);

        if (!$auth->validate()) {
            $this->handleFailedValidation($auth, $clientName);
            return;
        }

        $auth->save();
        $transaction->commit();
        UserService::assignRbacRole($user);
        Yii::$app->user->login($user);
    }

    /**
     * Handles failed validation for User or Auth models after external authentication attempt
     *
     * @param ActiveRecord $record User or Auth instance
     * @param string $clientName Authentication client title
     * @return void
     */
    private function handleFailedValidation(ActiveRecord $record, string $clientName)
    {
        $errors = json_encode($record->errors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

        if (YII_DEBUG) {
            throw new AuthException(get_class($record) . " model has not passed validation. Errors: $errors");
        }

        Yii::$app->session->setFlash('authError',
            "Не удалось создать пользователя через $clientName. Войдите стандартным способом."
        );
    }
}
