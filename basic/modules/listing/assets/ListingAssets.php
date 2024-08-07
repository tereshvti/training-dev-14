<?php

namespace app\modules\listing\assets;

use yii\web\AssetBundle;

class ListingAssets extends AssetBundle {

    public $sourcePath = '@app/modules/listing/assets';

    public $css = [
        'css/bootstrap.min.css',
        'css/custom.css'
    ];

    public $js = [
        'js/jquery.min.js',
        'js/bootstrap.min.js'
    ];
}