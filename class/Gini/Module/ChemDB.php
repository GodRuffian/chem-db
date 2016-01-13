<?php

namespace Gini\Module;

class ChemDB
{
    public static function setup()
    {
        date_default_timezone_set(\Gini\Config::get('system.timezone') ?: 'Asia/Shanghai');

        class_exists('\Gini\Those');
        class_exists('\Gini\ThoseIndexed');

        isset($_GET['locale']) and $_SESSION['locale'] = $_GET['locale'];
        isset($_SESSION['locale']) and \Gini\Config::set('system.locale', $_SESSION['locale']);
        \Gini\I18N::setup();

        setlocale(LC_MONETARY, (\Gini\Config::get('system.locale') ?: 'zh_CN').'.UTF-8');
    }

    public static function diagnose()
    {
    }
}
