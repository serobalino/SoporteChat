<?php

if(function_exists('date_default_timezone_get'))
{
    $timezone = date_default_timezone_get();

    if(empty($timezone))
    {
        date_default_timezone_set('UTC');
    }
}
else
{
    $timezone = ini_get('date.timezone');

    if(empty($timezone))
    {
        ini_set('date.timezone', 'UTC');
    }
}
