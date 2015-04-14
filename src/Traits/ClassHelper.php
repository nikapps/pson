<?php
namespace Nikapps\Pson\Traits;

trait ClassHelper
{

    /**
     * get class name
     *
     * @return string
     */
    public static function getClass()
    {
        return get_called_class();
    }
} 