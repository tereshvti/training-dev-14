<?php

namespace listing\assets;

use yii\web\AssetBundle;

class ListingAssets extends AssetBundle {

    /**
     * @var string
     */
    public $sourcePath = '@app/modules/listing/assets';

    /**
     * @var string[]
     */
    public $css = [
        'css/bootstrap.min.css',
        'css/custom.css'
    ];

    /**
     * @var string[]
     */
    public $js = [
        'js/jquery.min.js',
        'js/bootstrap.min.js'
    ];
}