<?php

namespace Icarus\UI;


use Nette\InvalidStateException;
use Nette\Localization\ITranslator;
use Nette\SmartObject;
use Nette\Utils\Strings;


class DateFilter
{

    use SmartObject;

    const DEFAULT_LANGUAGE = 'en';

    const FORMAT_DATE = 'date';
    const FORMAT_DATETIME = 'datetime';
    const FORMAT_DATETIME_WITH_SECONDS = 'datetime_complete';

    /** @var */
    private $dateFormats;

    /** @var array */
    private $dateFormat;

    /** @var */
    private $translator;



    public function __construct(array $dateFormats, $defaultLanguage = null, ITranslator $translator)
    {
        $this->dateFormats = $dateFormats;
        $this->translator = $translator;

        $this->validateDateFormats();

        $language = null;

        if (method_exists($this->translator, 'getLang')) {
            $language = $this->translator->getLang();
        }

        if (!$language && $defaultLanguage) {
            $language = $defaultLanguage;
        }

        if (!$language || !isset($this->dateFormats[$language])) {
            $language = self::DEFAULT_LANGUAGE;
        }

        $this->dateFormat = $this->dateFormats[$language];
    }



    public function date($value, $format = null)
    {
        $date = $this->prepareValue($value);

        if (!$date) {
            return "";
        }

        return $date->format($format ?: $this->getDateFormat());
    }



    public function datetime($value, $includeSeconds = false)
    {
        $date = $this->prepareValue($value);

        if (!$date) {
            return "";
        }

        return $date->format($this->getDateTimeFormat($includeSeconds));
    }



    public function getFormat($formatName)
    {
        switch ($formatName) {
            case DateFilter::FORMAT_DATETIME:
                return $this->getDateTimeFormat();
            case DateFilter::FORMAT_DATETIME_WITH_SECONDS:
                return $this->getDateTimeFormat(true);
            default:
                return $this->getDateFormat();
        }
    }



    public function getJsFormat($formatName)
    {
        switch ($formatName) {
            case DateFilter::FORMAT_DATETIME:
                return $this->getJsDateTimeFormat();
            case DateFilter::FORMAT_DATETIME_WITH_SECONDS:
                return $this->getJsDateTimeFormat(true);
            default:
                return $this->getJsDateFormat();
        }
    }



    public function getDateFormat()
    {
        return $this->dateFormat[self::FORMAT_DATE][0];
    }



    public function getDateTimeFormat($includeSeconds = false)
    {
        return $this->dateFormat[$includeSeconds ? self::FORMAT_DATETIME_WITH_SECONDS : self::FORMAT_DATETIME][0];
    }



    public function getJsDateFormat()
    {
        return $this->dateFormat[self::FORMAT_DATE][1];
    }



    public function getJsDateTimeFormat($includeSeconds = false)
    {
        return $this->dateFormat[$includeSeconds ? self::FORMAT_DATETIME_WITH_SECONDS : self::FORMAT_DATETIME][1];
    }



    /**
     * @param $value
     * @return \DateTime
     */
    private function prepareValue($value)
    {
        if (!$value) {
            return null;
        }
        if (!$value instanceof \DateTime) {
            $value = new \DateTime($value);
        }

        return $value;
    }



    private function validateDateFormats()
    {
        $constants = [];

        foreach ($this->getReflection()->getConstants() as $name => $value) {
            if (Strings::startsWith($name, 'FORMAT')) {
                $constants[$name] = $value;
            }
        }

        $constantValues = array_values($constants);

        if (!isset($this->dateFormats[self::DEFAULT_LANGUAGE])) {
            throw new InvalidStateException(
                "DateFilter: missing formats for default language '" . self::DEFAULT_LANGUAGE . "'. Check your configuration file."
            );
        }

        foreach ($this->dateFormats as $language => $formats) {
            if (array_keys($formats) != $constantValues) {
                foreach ($constants as $value) {
                    if (!isset($formats[$value])) {
                        throw new InvalidStateException(
                            "DateFilter: missing format '$value' for language '$language'. Check your configuration file."
                        );
                    } else if (!is_array($formats[$value]) || count($formats[$value]) != 2) {
                        throw new InvalidStateException(
                            "DateFilter: the value of format '$value' for language '$language' should be an array containing 2 formats at indices '0' and '1' (the first for PHP, the second for JavaScript). Check your configuration file."
                        );
                    }
                }
            }
        }
    }
}