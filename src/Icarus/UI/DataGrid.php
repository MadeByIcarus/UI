<?php

namespace App\UI;


use App\UI\Latte\Filters\DateFilter;
use Nette\Bridges\ApplicationLatte\Template;
use Ublaboo\DataGrid\Column\ColumnDateTime;
use Ublaboo\DataGrid\Filter\FilterDate;
use Ublaboo\DataGrid\Filter\FilterDateRange;
use Nette;
use Ublaboo\DataGrid\Localization\SimpleTranslator;


abstract class DataGrid extends \Ublaboo\DataGrid\DataGrid
{

    private $translations = [
        'cs' => [
            'ublaboo_datagrid.no_item_found_reset' => 'Žádné položky nenalezeny. Filtr můžete vynulovat',
            'ublaboo_datagrid.no_item_found' => 'Žádné položky nenalezeny.',
            'ublaboo_datagrid.here' => 'zde',
            'ublaboo_datagrid.items' => 'Položky',
            'ublaboo_datagrid.all' => 'všechny',
            'ublaboo_datagrid.from' => 'z',
            'ublaboo_datagrid.reset_filter' => 'Resetovat filtr',
            'ublaboo_datagrid.group_actions' => 'Hromadné akce',
            'ublaboo_datagrid.show_all_columns' => 'Zobrazit všechny sloupce',
            'ublaboo_datagrid.show_default_columns' => 'Zobrazit výchozí sloupce',
            'ublaboo_datagrid.hide_column' => 'Skrýt sloupec',
            'ublaboo_datagrid.action' => 'Akce',
            'ublaboo_datagrid.previous' => 'Předchozí',
            'ublaboo_datagrid.next' => 'Další',
            'ublaboo_datagrid.choose' => 'Vyberte',
            'ublaboo_datagrid.execute' => 'Provést'
        ]
    ];

    /** @var DateFilter */
    protected $dateFilter;

    /** @var array */
    protected $registeredDateTimeColumns = [];

    /** @var array */
    protected $registeredDateTimeFilters = [];



    public function __construct(Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);

        $reflection = new \ReflectionClass(static::class);
        $this->setTemplateFile(sprintf('%s/%s.latte', dirname($reflection->getFileName()), $reflection->getShortName()));

        $this->onRender[] = function () {
            /* workaround for registerFilters for ublaboo datagrid paginator component */
            $template = $this['paginator']->getTemplate();
            $this->registerFilters($template);
        };
    }



    public function useSimpleTranslator($language = "cs")
    {
        if (!isset($this->translations[$language])) {
            throw new \InvalidArgumentException("Translation '$language' not found.");
        }

        $translations = $this->translations[$language];

        $translator = new SimpleTranslator($translations);

        $this->setTranslator($translator);
    }



    public function addColumnDateTime($key, $name, $column = null, $formatName = DateFilter::FORMAT_DATE)
    {
        $column = parent::addColumnDateTime($key, $name, $column);

        if ($this->dateFilter !== null) {
            $column->setFormat($this->dateFilter->getFormat($formatName));
        } else {
            $this->registeredDateTimeColumns[$key] = [$column, $formatName];
        }

        return $column;
    }



    protected function createTemplate()
    {
        /** @var Template $template */
        $template = parent::createTemplate();
        $this->registerFilters($template);
        return $template;
    }



    protected function prefix($key, $glue = '.')
    {
        //return sprintf('%s%s%s', Utils::trimNamespace(static::class), $glue, $key);
    }



    protected function registerFilters(Template $template)
    {
        /* $template->addFilter(
             'prefix',
             function ($s) {
                 return $this->prefix($s);
             }
         );*/
    }



    public function setDateFilter(DateFilter $dateFilter)
    {
        $this->dateFilter = $dateFilter;

        foreach ($this->registeredDateTimeColumns as $key => $values) {
            /** @var ColumnDateTime $column */
            list($column, $formatName) = $values;

            $php_format = $this->dateFilter->getFormat($formatName);
            $js_format = $this->dateFilter->getJsFormat($formatName);

            $column->setFormat($php_format);

            if (Nette\Utils\Strings::contains($js_format, 'hh')) {
                $minView = 0;
            } else {
                $minView = 2;
            }

            if (isset($this->filters[$key])) {
                $filter = $this->filters[$key];

                if ($filter instanceof FilterDate) {
                    $filter->setFormat($php_format, $js_format);
                    $filter->setAttribute('data-min-view', $minView);
                } else if ($filter instanceof FilterDateRange) {
                    $filter->setFormat($php_format, $js_format);
                    $filter->setAttribute('data-min-view', $minView);
                }
            }

            unset($this->registeredDateTimeColumns[$key]);
        }
    }
}
