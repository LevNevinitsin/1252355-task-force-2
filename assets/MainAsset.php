<?php

namespace app\assets;
use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $depends = [
        'app\assets\VendorAsset'
    ];

    public $basePath = '@webroot';

    public $css = [
        'css/style.css',
        'css/style-new.css',
    ];

    public $js = [
        'js/main.js',
        'js/files-upload.js',
        'js/star-rating.js',
    ];
}
