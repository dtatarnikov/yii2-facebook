<?php
namespace strong2much\facebook\widgets;

use Yii;
use yii\web\View;

/**
 * Base widget class
 *
 * @author   Denis Tatarnikov <tatarnikovda@gmail.com>
 */
class Widget extends \yii\base\Widget
{
    const COLORSCHEME_LIGHT = 'light';
    const COLORSCHEME_DARK = 'dark';

    /**
     * @var bool is widget enabled
     */
    public $enabled = true;
    /**
     * @var string facebook application id
     */
    public $appId;

    /**
     * @var bool is script registered
     */
    protected $_scriptRegistered = false;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        if (!$this->enabled) {
            return;
        }

        $this->registerApi();

        parent::init();
    }

    /**
     * Register FB Api
     */
    public function registerApi()
    {
        if(!$this->_scriptRegistered) {
            $view = $this->view;
            $view->registerJsFile(
                "//connect.facebook.net/ru_RU/all.js#appId=".(isset($this->appId)?$this->appId:'')."&xfbml=1&status=1&cookie=1",
                [
                    'async'=>'async',
                    'position'=>View::POS_END,
                ]
            );

            $this->_scriptRegistered = true;
        }
    }
}