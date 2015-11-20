<?php
namespace strong2much\facebook\widgets;

use yii\helpers\Html;

/**
 * This is the widget class for FB comment widget.
 *
 * @author   Denis Tatarnikov <tatarnikovda@gmail.com>
 */
class CommentWidget extends Widget
{
    const ORDER_SOCIAL = 'social';
    const ORDER_REVERSE_TIME = 'reverse_time';
    const ORDER_TIME = 'time';

    public $url;
    public $width = 470;
    public $numPosts = 10;
    public $orderBy = self::ORDER_SOCIAL;
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
        $fbOptions['class'] = 'fb-comments';
        $fbOptions['data-href'] = $this->url;
        $fbOptions['data-width'] = $this->width;
        $fbOptions['data-num_posts'] = $this->numPosts;
        $fbOptions['data-order_by'] = $this->orderBy;
        $fbOptions['data-colorscheme'] = $this->colorScheme;

        echo Html::beginTag('div', $this->htmlOptions);
        echo Html::tag('div', '', $fbOptions);
        echo Html::endTag('div');
    }
}