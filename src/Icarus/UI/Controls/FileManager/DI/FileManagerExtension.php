<?php

namespace Icarus\UI\Controls\DatePickers\DI;


use Icarus\UI\Controls\FileManager\FileManager;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;


class FileManagerExtensionsExtension extends CompilerExtension
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

        $initialize = $class->methods['initialize'];
        $initialize->addBody(FileManager::class . '::register()');
    }
}