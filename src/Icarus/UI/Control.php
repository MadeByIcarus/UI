<?php

namespace Icarus\UI;


use Nette\Bridges\ApplicationLatte\Template;
use Nette\Http\IRequest;
use Nette\Localization\ITranslator;


abstract class Control extends \Nette\Application\UI\Control
{

    /** @var  ITranslator */
    private $translator = null;

    /** @var  IRequest */
    private $httpRequest;

    /** @var bool|null */
    private $isAjax = null;



    /**
     * @return ITranslator
     */
    public function getTranslator()
    {
        return $this->translator;
    }



    /**
     * @param ITranslator $translator
     */
    public function setTranslator(ITranslator $translator)
    {
        $this->translator = $translator;
    }



    /**
     * @return IRequest
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }



    /**
     * @param IRequest $httpRequest
     */
    public function setHttpRequest(IRequest $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }



    /**
     * @return Template
     */
    protected function createTemplate()
    {
        /** @var Template $template */
        $template = parent::createTemplate();
        $template->setTranslator($this->translator);
        //$this->registerFilters($template);
        return $template;
    }



    public function render()
    {
        $template = $this->getTemplate();

        $hasFormWithErrors = false;
        foreach ($this->getComponents() as $component) {
            if ($component instanceof Form) {
                $hasFormWithErrors = true;
                break;
            }
        }

        $template->hasFormWithErrors = $hasFormWithErrors;

        $reflection = new \ReflectionClass($this);
        $template->render(sprintf('%s/%s.latte', dirname($reflection->getFileName()), $reflection->getShortName()));
    }



    public function isAjax()
    {
        if ($this->isAjax === null) {
            $this->isAjax = $this->httpRequest && $this->httpRequest->isAjax();
        }
        return $this->isAjax;
    }

    /*protected function prefix($key, $glue = '.')
    {
        $prefix = Utils::trimNamespace(static::class);

        return $prefix . $glue . $key;
    }



    protected function registerFilters(Template $template)
    {
        $template->addFilter(
            'prefix',
            function ($s) {
                return $this->prefix($s);
            }
        );
    }*/
}
