<?php

namespace Icarus\UI\Controls\FileManager;

use Nette\Forms\Container;
use Nette\Forms\Controls\TextBase;
use Nette\Utils\Html;
use Icarus\UI\Form;

class FileManager extends TextBase
{
    const TYPE_ALL = 0;
    const TYPE_IMAGE = 1;
    const TYPE_FILE = 2;
    const TYPE_VIDEO = 3;

    const DEFAULT_FOLDER = 'common';

    protected $fileType;

    public static function register($methodName = 'addFilePicker')
    {
        Container::extensionMethod($methodName, function (Container $_this, $name, $label, $type = self::TYPE_ALL, $subfolder = self::DEFAULT_FOLDER) {
            $control = new FileManager($label, $type, $subfolder);
            return $_this[$name] = $control;
        });
    }

    /**
     * @param  string  label
     * @param  int  maximum number of characters the user may enter
     */
    public function __construct($label = null, $type = self::TYPE_ALL, $subfolder = self::DEFAULT_FOLDER)
    {
        parent::__construct($label);
        $this->control->type = 'text';
        $this->fileType = $type;

        if ($subfolder !== null) {
            @setcookie('RF_subfolder', $subfolder . '/', time() + (86400 * 30), "/");
        }
    }


    /**
     * Loads HTTP data.
     * @return void
     */
    public function loadHttpData()
    {
        $this->setValue($this->getHttpData(Form::DATA_LINE));
    }


    /**
     * Generates control's HTML element.
     * @return \Nette\Utils\Html
     */
    public function getControl()
    {
        // code below is taken from the text input class
        $input = parent::getControl();

        foreach ($this->getRules() as $rule) {
            if ($rule->isNegative || $rule->branch) {

            } elseif (in_array($rule->validator, array(Form::MIN, Form::MAX, Form::RANGE), TRUE)
                && in_array($input->type, array('number', 'range', 'datetime-local', 'datetime', 'date', 'month', 'week', 'time'), TRUE)
            ) {
                if ($rule->validator === Form::MIN) {
                    $range = array($rule->arg, NULL);
                } elseif ($rule->validator === Form::MAX) {
                    $range = array(NULL, $rule->arg);
                } else {
                    $range = $rule->arg;
                }
                if (isset($range[0]) && is_scalar($range[0])) {
                    $input->min = isset($input->min) ? max($input->min, $range[0]) : $range[0];
                }
                if (isset($range[1]) && is_scalar($range[1])) {
                    $input->max = isset($input->max) ? min($input->max, $range[1]) : $range[1];
                }

            } elseif ($rule->validator === Form::PATTERN && is_scalar($rule->arg)
                && in_array($input->type, array('text', 'search', 'tel', 'url', 'email', 'password'), TRUE)
            ) {
                $input->pattern = $rule->arg;
            }
        }

        if ($input->type !== 'password' && ($this->rawValue !== '' || $this->emptyValue !== '')) {
            $input->value = $this->rawValue === ''
                ? $this->translate($this->emptyValue)
                : $this->rawValue;
        }

        // code above is taken from the text input class

        $currentClasses = [];

        if (isset($input->attrs['class']))
            foreach ($input->attrs['class'] as $class => $expr)
                if ($expr)
                    $currentClasses[] = $class;

        $currentClasses = implode(" ", $currentClasses);

        $input->addAttributes([
            'class' => 'file-external text ' . $currentClasses,
            'data-type' => $this->fileType,
            'autocomplete' => 'off'
        ]);

        $container = Html::el();

        $preview = Html::el("div")
            ->addAttributes([
                'class' => $this->fileType == self::TYPE_IMAGE ? 'picture-preview' : 'file-preview',
                'style' => "margin-top:10px;",
                'data-id' => $this->getHtmlId()
            ]);

        $container->addHtml($input);
        $container->addHtml($preview);

        return $container;
    }
} 