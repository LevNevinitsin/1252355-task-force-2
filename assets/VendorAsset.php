<?php

namespace app\assets;
use yii\web\AssetBundle;

class VendorAsset extends AssetBundle
{
    public $sourcePath = '@npm/dropzone/dist/min';

    public $js = [
        'dropzone.min.js',
    ];

    public $css = [
        'dropzone.min.css',
    ];
}
