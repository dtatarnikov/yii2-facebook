<?php
namespace strong2much\facebook\widgets;

use yii\helpers\Html;

/**
 * This is the widget class for FB send button widget.
 *
 * @author   Denis Tatarnikov <tatarnikovda@gmail.com>
 */
class SendButtonWidget extends Widget
{
    const FONT_ARIAL = 'arial';
    const FONT_LUCIDA_GRANDE = 'lucida grande';
    const FONT_SEGOE_UI = 'segoe ui';
    const FONT_TAHOMA = 'tahoma';
    const FONT_TREBUCHET_MS = 'trebuchet ms';
    const FONT_VERNADA= 'verdana';

    public $url;
    public $font = self::FONT_ARIAL;
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
        $fbOptions['class'] = 'fb-send';
        $fbOptions['data-href'] = $this->url;
        $fbOptions['data-font'] = $this->font;
        $fbOptions['data-colorscheme'] = $this->colorScheme;

        echo Html::beginTag('div', $this->htmlOptions);
        echo Html::tag('div', '', $fbOptions);
        echo Html::endTag('div');
    }
}
