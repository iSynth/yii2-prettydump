<?php

    namespace sb\prettydumper;

    interface DumperPlugin
    {
        static function is($arg);
        static function format($arg, $tab = 0);
    }
