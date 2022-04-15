<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;
use LevNevinitsin\Business\Service\LocationService;

class LocationController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['geocode'],
                        'roles' => ['customer'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param [type] $location Input value
     * @param [type] $userCityName User city name for filtering results
     * @param [type] $userCityCoordinates Coordinates to limit the geocoder search area
     * @return void
     */
    public function actionGeocode($location, $userCityName, $userCityCoordinates)
    {
        $apiKey = 'e666f398-c983-4bde-8f14-e3fec900592a';
        $apiUri = 'https://geocode-maps.yandex.ru/';
        $resultsQuantity = 100;
        $maxLatitudeDifference = 0.6;
        $maxLongitudeDifference = 1;

        $client = new Client([
            'base_uri' => $apiUri,
        ]);

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $response = $client->request('GET', '1.x', [
                'query' => [
                    'geocode' => $location,
                    'apikey' => $apiKey,
                    'rspn' => 1,
                    'll' => $userCityCoordinates,
                    'spn' => "$maxLongitudeDifference, $maxLatitudeDifference",
                    'format' => 'json',
                    'results' => $resultsQuantity
                ],
            ]);

            $content = $response->getBody()->getContents();
            $responseData = json_decode($content, true);
            $geoObjectCollection = ArrayHelper::getValue($responseData, 'response.GeoObjectCollection.featureMember');

            $locations = array_map(function ($geoObject) {
                $geoObject = ArrayHelper::getValue($geoObject, 'GeoObject');
                $fullAddress = ArrayHelper::getValue($geoObject, 'metaDataProperty.GeocoderMetaData.text');
                $cityName = LocationService::getCity($geoObject);
                $coordinates = StringHelper::explode(ArrayHelper::getValue($geoObject, 'Point.pos'), ' ');
                $latitude = $coordinates[1];
                $longitude = $coordinates[0];

                return [
                    'fullAddress' => $fullAddress,
                    'cityName' => $cityName,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ];
            }, $geoObjectCollection);

            $locations = array_filter($locations, function ($result) use ($userCityName) {
                return $result['cityName'] === $userCityName;
            });

            return array_values($locations);
        } catch (ConnectException $e) {
            return [];
        } catch (ClientException $e) {
            $responseData = json_decode($e->getResponse()->getBody()->getContents(), true);
            $error = ArrayHelper::getValue($responseData, 'error');
            $message = ArrayHelper::getValue($responseData, 'message');
            return "API error: $error. $message.";
        }
    }
}
