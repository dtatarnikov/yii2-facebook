<?php
namespace strong2much\facebook\widgets;

use yii\helpers\Html;

/**
 * This is the widget class for FB like box widget.
 *
 * @author   Denis Tatarnikov <tatarnikovda@gmail.com>
 */
class LikeBoxWidget extends Widget
{
    public $url;
    public $width = 300;
    public $height = 63;
    public $showFaces = true;
    public $showStream = false;
    public $showBorder = true;
    public $showHeader = true;
    public $forceWall = false;
    public $colorScheme = self::COLORSCHEME_LIGHT;

    public $htmlOptions = array();

    /**
     * Executes the widget.
     */
    public function run()
    {
        if (!$this->enabled) {
            return;
        }

        $fbOptions = array();
        $fbOptions['class'] = 'fb-like-box';
        $fbOptions['data-href'] = $this->url;
        $fbOptions['data-width'] = $this->width;
        $fbOptions['data-height'] = $this->height;
        $fbOptions['data-show-faces'] = $this->showFaces ? 'true' : 'false';
        $fbOptions['data-show-border'] = $this->showBorder ? 'true' : 'false';
        $fbOptions['data-stream'] = $this->showStream ? 'true' : 'false';
        $fbOptions['data-header'] = $this->showHeader ? 'true' : 'false';
        $fbOptions['data-force-wall'] = $this->forceWall ? 'true' : 'false';
        $fbOptions['data-colorscheme'] = $this->colorScheme;

        echo Html::beginTag('div', $this->htmlOptions);
        echo Html::tag('div', '', $fbOptions);
        echo Html::endTag('div');
    }
}