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

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class Cycle extends Widget {

    /**
     * @var BaseDataProvider
     * Required
     */
    public $dataProvider;

    /**
     * @var string|callable - Image attribute, required.
     * - string: name of the attribute that holds the URL for the image. Note that this could be a virtual attribute.
     * - callable: function($model, $widget) that returns the URL for the image.
     */
    public $imgAttribute;

    /**
     * @var string|callable - Attribute containing the link URL of the slide. If not set, the slide images are not embedded in links.
     * Like imgAttribute, tis can be a string or a callable with the signature function($model, $widget).
     */
    public $urlAttribute;


    /**
     * @var string|callable - Attribute containing the text or HTML of the slide tooltip.
     * Like imgAttribute, tis can be a string or a callable with the signature function($model, $widget).
     */
    public $tooltipAttribute;

    /**
     * @var array The attributes displayed in the caption or the overlay
     * Members can be:
     * - string name of the attribute
     * - <key> => <value> where <key> is a name and <value> is either the name of an attribute or a callable
     *          with the signature function($model, $widget)
     */
    public $captionAttributes = [];
    public $overlayAttributes = [];

    /**
     * @var array Extra elements, apart from the images, caption, and overlay
     * Could be filled with elements like '<div class="cycle-pager"></div>'
     */
    public $content = [];

    /**
     * @var array Extra plugins
     * Most plugins are added automatically
     */
    public $plugins = [];

    /**
     * @var array
     * Client options for the jQuery Cycle2 widget.
     * @link http://jquery.malsup.com/cycle2/
     */
    public $options = [];

    /**
     * @var array
     * HTML options of the Cycle container.
     * Use this if you want to explicitly set the ID.
     */
    public $htmlOptions = [];

    /**
     * @var array
     * HTML options for each slide
     * One use would be to set this to: [ 'target' => '_blank' ]
     */
    public $slideOptions = [];

    public function run()   {
        $fxPlugins = [
            'carousel' => 'carousel',
            'flipHorz' => 'flip',
            'flipVert' => 'flip',
            'scrollVert' => 'scrollVert',
            'shuffle' => 'shuffle',
            'tileBlind' => 'tile',
            'tileSlide' => 'tile',
        ];

        if (is_null($this->dataProvider) || is_null($this->imgAttribute)) {
            throw new InvalidConfigException('The "dataProvider" and "imgAttribute" properties must be set.');
        }

        $view = $this->getView();
        CycleAsset::register($view);

        if (isset($this->htmlOptions['id'])) {
            $this->setId($this->htmlOptions['id']);
        }
        else $this->htmlOptions['id'] = $this->getId();

        $options = $this->options;
        $plugins = $this->plugins;
        if (isset($options['fx']))    {
            $fx = $options['fx'];
            if (isset($fxPlugins[$fx])) $plugins[] = $fxPlugins[$fx];
        }
        if (isset($options['captionPlugin'])) $plugins[] = $options['captionPlugin'];
        if (isset($options['centerHorz']) || isset($options['centerVert'])) $plugins[] = 'center';
        if (isset($options['youtube'])) $plugins[] = 'video';

        $pluginBase = Yii::$app->assetManager->getPublishedUrl('@bower/jquery-cycle2/build') . '/plugin/jquery.cycle2.';
        foreach ($plugins as $plugin) $view->registerJsFile($pluginBase . $plugin. '.min.js', [ 'depends' => CycleAsset::class]);

        $allAttributes = [];

        $content = $this->content;

        foreach ([ 'caption', 'overlay' ] as $element) {
            $attribs = $this->{$element . 'Attributes'};
            if (is_string($attribs)) $attribs = [$attribs];
            $allAttributes = array_merge($allAttributes, $attribs);

            $tags = [];
            foreach ($attribs as $key => $attr)   {
                if (is_numeric($key)) $key = $attr;
                $tags[] = "<span class=\"slide-$key\">{{{$key}}}</span>";
            }

            if (! empty($tags)) $content[] = "<div class=\"cycle-$element\"></div>";
            $options[$element . 'Template'] = implode(' ', $tags);
        }
        if ($this->urlAttribute) $options['slides'] = '> a';

        $startingSlide = 0;
        if (isset($this->options['startingSlide'])) $startingSlide = $this->options['startingSlide'];

        $i = 0;
        foreach ($this->dataProvider->models as $model) {
            $data = [];

            foreach ($allAttributes as $key => $attr)   {
                if (is_numeric($key)) $data[$attr] = ArrayHelper::getValue($model, $attr);
                else $data[$key] = $this->getAttrValue($model, $attr);
            }

            $slideOptions = $this->slideOptions;
            $slideOptions['data'] = $data;
            if ($i != $startingSlide) $slideOptions['style'] = 'display:none;';     /** no FOUC  @link http://jquery.malsup.com/cycle2/faq/#fouc */

            if ($this->tooltipAttribute)    {
                $slideOptions['title'] = $this->getAttrValue($model, $this->tooltipAttribute);
            }

            $src = $this->getAttrValue($model, $this->imgAttribute);
            if ($this->urlAttribute)    {
                $url = $this->getAttrValue($model, $this->urlAttribute);
                $content[] = Html::a(Html::img($src), $url, $slideOptions);
            }
            else $content[] = Html::img($src, $slideOptions);

            $i++;
        }

        $id = $this->getId();
        $opts = empty($options) ? '{}' : Json::encode($options);

        $view->registerJs("jQuery('#$id').cycle($opts);");

        echo Html::tag('div', implode('', $content), $this->htmlOptions);
    }

    protected function getAttrValue($model, $attr)   {
        return is_callable($attr) ? call_user_func($attr, $model, $this)
            : ArrayHelper::getValue($model, $attr);
    }
}
