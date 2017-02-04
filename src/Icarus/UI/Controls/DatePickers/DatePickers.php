<?php

namespace Icarus\UI\Controls\DatePickers;


use App\UI\Latte\Filters\DateFilter;
use Nette\Forms\Container;
use RadekDostal\NetteComponents\DateTimePicker\DatePicker;
use RadekDostal\NetteComponents\DateTimePicker\DateTimePicker;


class DatePickers
{

    /**
     * @param DateFilter $dateFilter
     */
    public static function registerDatePicker(DateFilter $dateFilter)
    {
        DatePicker::register($dateFilter->getDateFormat());
    }



    /**
     * @param DateFilter $dateFilter
     */
    public static function registerDateTimePicker(DateFilter $dateFilter)
    {
        DateTimePicker::register($dateFilter->getDateTimeFormat());
    }
}