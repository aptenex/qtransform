<?php

namespace Aptenex\QTransform;

class DateUtils
{

    public static function isValidDate($dateString)
    {
        return (bool) strtotime($dateString);
    }

}