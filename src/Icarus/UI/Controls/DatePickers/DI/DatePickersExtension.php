<?php

namespace Icarus\UI\Controls\DatePickers\DI;


use Icarus\UI\Controls\DatePickers\DatePickers;
use Icarus\UI\DateFilter;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;


class DatePickersExtension extends CompilerExtension
{

    /**
     * Adjusts DI container compiled to PHP class. Intended to be overridden by descendant.
     *
     * @param \Nette\PhpGenerator\ClassType $class class, interface, trait description
     * @return void
     */
    public function afterCompile(ClassType $class)
    {
        parent::afterCompile($class);

        $name = $this->getContainerBuilder()->getByType(DateFilter::class);

        $initialize = $class->methods['initialize'];
        $initialize->addBody(DatePickers::class . '::registerDatePicker($this->getService(?));', [$name]);
        $initialize->addBody(DatePickers::class . '::registerDateTimePicker($this->getService(?));', [$name]);
    }
}