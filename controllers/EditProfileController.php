<?php
namespace app\controllers;

use app\models\EditProfileForm;
use app\models\Category;
use app\models\ChosenCategory;
use app\models\SecurityForm;
use app\models\User;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;

class EditProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'security'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = User::findOne(Yii::$app->user->id);
        $profileData = new EditProfileForm();

        if (Yii::$app->request->getIsPost()) {
            $profileData->load(Yii::$app->request->post());

            if ($profileData->validate()) {
                if ($avatar = UploadedFile::getInstance($profileData, 'avatar')) {
                    $fileExtension = $avatar->getExtension();
                    $filename = uniqid('upload_') . '.' . $fileExtension;
                    $avatarWebPath = '/upload/avatars/' . $filename;
                    $avatarFullPath = '@webroot' . $avatarWebPath;
                    $avatar->saveAs($avatarFullPath);
                    $user->photo = $avatarWebPath;
                }

                $user->attributes = $profileData->attributes;
                $user->save();
                ChosenCategory::deleteAll(['user_id' => $user->id]);

                $chosenCategories = array_map(function ($categoryId) use ($user) {
                    return [$user->id, $categoryId];
                }, $profileData->chosenCategoriesIds ?? []);

                if ($chosenCategories) {
                    Yii::$app->db->createCommand()
                        ->batchInsert('chosen_category', ['user_id', 'category_id'], $chosenCategories)
                        ->execute();
                }

                if ($user->role_id === 1) {
                    return $this->redirect('/edit-profile');
                }

                return $this->redirect(['/users/view', 'id' => $user->id]);
            }
        }

        $this->view->params['mainContentLeft'] = 'main-content--left';
        $profileData->attributes = $user->attributes;
        $categories = Category::find()->select(['name'])->orderBy(['id' => SORT_ASC])->indexBy('id')->column();
        $selectedCategories = $user->chosenCategories;

        $selectedCategoriesIds = array_map(function ($selectedCategory) {
            return $selectedCategory->category_id;
        }, $selectedCategories);

        return $this->render('edit-profile', [
            'user' => $user,
            'model' => $profileData,
            'categories' => $categories,
            'selectedCategories' => $selectedCategoriesIds,
        ]);
    }

    public function actionSecurity()
    {
        $user = User::findOne(Yii::$app->user->id);
        $securityModel = new SecurityForm();
        $this->view->params['mainContentLeft'] = 'main-content--left';

        if (Yii::$app->request->getIsPost()) {
            $securityModel->load(Yii::$app->request->post());

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($securityModel);
            }

            if ($securityModel->validate()) {
                if ($newPassword = $securityModel->newPassword) {
                    $user->password = Yii::$app->security->generatePasswordHash($newPassword);
                }

                $user->hide_contacts = $securityModel->hideContacts;
                $user->save(false);

                if ($user->role_id === 1) {
                    return $this->redirect('/edit-profile');
                }

                return $this->redirect(['/users/view', 'id' => $user->id]);
            }
        }

        return $this->render('security', [
            'model' => $securityModel,
            'user' => $user,
        ]);
    }
}
