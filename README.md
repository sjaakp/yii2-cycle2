yii2-cycle2
============

#### Cycle2 jQuery slide show for Yii 2.0 ####

**yii2-cycle2** is a widget to render the excellent [Cycle2](http://jquery.malsup.com/cycle2/) jQuery slideshow widget in the [Yii 2.0](http://www.yiiframework.com/ "Yii") PHP Framework. Like a [GridView](http://www.yiiframework.com/doc-2.0/yii-grid-gridview.html "Yii"), its data is fed from an [ActiveDataProvider](http://www.yiiframework.com/doc-2.0/yii-data-activedataprovider.html "Yii") (or, more generally, from a class derived from [BaseDataProvider](http://www.yiiframework.com/doc-2.0/yii-data-basedataprovider.html "Yii")).  

A demonstration of **Yii2-cycle2** is [here](http://www.sjaakpriester.nl/software/cycle).

## Installation ##

Install **yii2-cycle2** with [Composer](https://getcomposer.org/). Either add the following to the require section of your `composer.json` file:

`"sjaakp/yii2-cycle2": "*"` 

Or run:

`composer require sjaakp/yii2-cycle2 "*"` 

You can manually install **yii2-cycle2** by [downloading the source in ZIP-format](https://github.com/sjaakp/yii2-cycle2/archive/master.zip).

## Using yii2-cycle2 ##

**yii2-cycle2** implements a widget of the class `Cycle`. It gets its data from an `ActiveDataProvider`, `ArrayDataProvider`, or other class derived from [`BaseDataProvider`](http://www.yiiframework.com/doc-2.0/yii-data-basedataprovider.html "Yii") Using it is not unlike using a [GridView](http://www.yiiframework.com/doc-2.0/yii-grid-gridview.html "Yii Framework"). For instance, in the Controller you might have something like:

	<?php
	// ...
	public function actionPie()	{
		$dataProvider = new ActiveDataProvider([
			'query' => NicePlace::find(),
		    'pagination' => false
		]);
		
		return $this->render('cycle', [
			'dataProvider' => $dataProvider
		]);
	}
	// ...
	?>

To render a `Cycle` in the `View` we could use:

	<?php
	use sjaakp\cycle\Cycle;
	?>
	...
    <?= Cycle::widget([
        'dataProvider' => $dataProvider,
		'imgAttribute' => 'photo',
        'captionAttributes' => [
            'name',
            'country'
        ],
        'overlayAttributes' => [
            'description'
        ],
        'options' => [
            'speed' => 2000
        ],
    ]) ?>
	...

## Options ##

`Cycle` has the following options:

### dataProvider ###

The data provider for the widget. This property is required. In most cases, it will be an `ActiveDataProvider` or an `ArrayDataProvider`.

### imgAttribute ###

Image attribute, required.

- `string`: name of the attribute that holds the source URL for the slide image. Note that this could be a virtual attribute.
- `callable`: a callable with signature `function($model, $widget)` that returns the source URL for the slide image.
 

### urlAttribute ###

Link URL attribute, optional.

- `null`: the slides are not embedded in links (default).
- `string`: name of the attribute that holds the link URL for the slide. Note that this could be a virtual attribute.
- `callable`: a callable with signature `function($model, $widget)` that returns the link URL for the slide.
 

### tooltipAttribute ###

Tooltip attribute, optional.

- `null`: the slides don't have tooltips (default).
- `string`: name of the attribute that holds the text or HTML for the slide tooltip. Note that this could be a virtual attribute.
- `callable`: a callable with signature `function($model, $widget)` that returns the text or HTML for the slide tooltip.

### captionAttributes, overlayAttributes ###

The attributes displayed in the caption or the overlay. See the [Cycle2 documentation](http://jquery.malsup.com/cycle2/demo/caption.php "Cycle2").

Members can be:

- `string` name of the attribute holding the text or HTML for the caption or overlay element;
- `<key> => <value>` where `<key>` is a name and `<value>` is 
	- the name of the attribute;
	- or a callable with the signature `function($model, $widget)` returning the text or HTML.

The attribute values will be rendered inside Cycle's caption or overlay `div`, each inside its own `span` element with class `"slide-<key>"`.

### content ###

Extra elements in the Cycle container, apart from the slides, caption, and overlay. Could be filled with elements like `'<div class="cycle-pager"></div>'` (see: [Cycle2 pagers](http://jquery.malsup.com/cycle2/demo/pager.php "Cycle2")).

### plugins ###

Plugins for the Cycle2 jQuery widget. **Notice** that **yii2-cycle2** attempts to load the required plugins automatically.

### options ###

Client options for the underlying [Cycle2 jQuery](http://jquery.malsup.com/cycle2/api/#options "Cycle2") widget. **Notice** that the option names must be camelCased.

### htmlOptions ###

HTML options of the `Cycle` container. Use this if you want to explicitly set the `ID`.

### slideOptions ###

HTML options for each slide. One use would be to set this to: `[ 'target' => '_blank' ]`
 