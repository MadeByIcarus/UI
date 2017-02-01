<?php

namespace Icarus\UI;


use Nette\ComponentModel\IContainer;


/**
 * @method addDatePicker($name, $label = NULL, $maxLength = NULL)
 * @method addDateTimePicker($name, $label = NULL, $maxLength = NULL)
 * @method addTbDatePicker($name, $label = NULL, $maxLength = NULL)
 * @method addTbDateTimePicker($name, $label = NULL, $maxLength = NULL)
 */
class Form extends \Nette\Application\UI\Form
{

    public function __construct(IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->monitor(Control::class);
        $this->addProtection();
    }



    protected function attached($object)
    {
        parent::attached($object);
        if ($object instanceof Control) {
            $this->setTranslator($object->getTranslator());
        }
    }
}
