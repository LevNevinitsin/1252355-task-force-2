<?php
namespace LevNevinitsin\Business\Service;

use yii\helpers\ArrayHelper;

class LocationService
{
    /**
     * Gets city name from GeoObject, which is associative array obtained by json_decoding
     * the response from the Yandex Maps geocoder into an array and further extracting the value
     * using the keys path 'response.GeoObjectCollection.featureMember.0.GeoObject'. '0' is dynamic value
     * and stands for GeoObject index in featureMember array (can be 0, 1, 2, 3 etc).
     *
     * @param array $geoObject Described above
     * @param string $componentsKeysPath Keys path to array with components of the address
     * @param string $componentTypeKey Name of the key containing the address component type (country, province, locality, etc)
     * @param string $cityComponentTypeValue The value that must be in the above key to get the component with the city
     * @return string|null Name of the city
     */
    public static function getCity(
        array $geoObject,
        string $componentsKeysPath = 'metaDataProperty.GeocoderMetaData.Address.Components',
        string $componentTypeKey = 'kind',
        string $cityComponentTypeValue = 'locality'
    ): ?string
    {
        // Get address components (country, province, locality, etc)
        $addressComponents = ArrayHelper::getValue($geoObject, $componentsKeysPath);

        // Get locality (city) component
        $cityNameComponent = ArrayHelper::getValue(
            $addressComponents,
            array_search($cityComponentTypeValue, array_column($addressComponents, $componentTypeKey))
        );

        // Get locality (city) name
        return ArrayHelper::getValue($cityNameComponent, 'name');
    }
}
