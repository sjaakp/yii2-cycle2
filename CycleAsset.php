<?php
/**
 * MIT licence
 * Version 0.9.0
 * Sjaak Priester, Amsterdam 10-12-2015.
 *
 * Cycle2 jQuery slide show for Yii 2.0
 * @link http://jquery.malsup.com/cycle2/
 */

namespace sjaakp\cycle;

use yii\web\AssetBundle;

class CycleAsset extends AssetBundle   {
    public $sourcePath = '@bower/jquery-cycle2/build';
    public $js = ['jquery.cycle2.min.js'];
    public $depends = ['yii\web\JqueryAsset'];
}
