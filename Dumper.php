<?php

    namespace sb\prettydumper;

    class Dumper
    {
        public static $style_block = 'font-size: 11px; border: 1px dashed #ddd; padding: 5px; margin: 2px; text-align: left; background-color: #f9f9f9;';
        public static $style_boolean = 'color: #FF0000;';
        public static $style_integer = 'color: #0000FF;';
        public static $style_float = 'color: #660099;';
        public static $style_callable = 'color: #999900;';
        public static $style_string = 'color: #009900;';
        public static $style_array = 'color: #660000;';
        public static $style_object = 'color: #003399;';
        public static $style_resource = 'color: #DD66FF;';
        public static $style_null = 'color: #000000;';
        public static $style_unknown = 'color: #000000;';
        public static $style_key = 'color: #ce7b00;';

        private static $onclick = "var s1=document.getElementById('error_close_%s').style; var s2=document.getElementById('error_open_%s').style; s1.display=s1.display=='none'?'block':'none'; s2.display=s2.display=='none'?'block':'none';";
        protected static $plugins = array('sb\prettydumper\DumperArray', 'sb\prettydumper\DumperObject', 'sb\prettydumper\DumperImage');

        public static function getStyle($style)
        {
            return strlen($style) ? ' style="' . $style . '"' : '';
        }

        public static function varDumpExtend($arg, $tab = 0)
        {
            if (count(self::$plugins))
            {
                foreach (self::$plugins as $plugin)
                {
                    if (call_user_func(array($plugin, 'is'), $arg))
                    {
                        return call_user_func(array($plugin, 'format'), $arg, $tab);
                    }
                }
            }
            return self::varDump($arg)."\n";
        }

        public static function varDump($arg)
        {
            switch (true)
            {
                case is_bool($arg):
                    $param = $arg == true ? "true" : "false";
                    $type = '<span'.self::getStyle(self::$style_boolean).'>boolean</span>';
                    break;

                case is_int($arg):
                    $param = strval($arg);
                    $type = '<span'.self::getStyle(self::$style_integer).'>integer</span>';
                    break;

                case is_float($arg):
                    $param = strval($arg);
                    $type = '<span'.self::getStyle(self::$style_float).'>float</span>';
                    break;

                case is_string($arg):
                    $param = '"'.htmlspecialchars($arg).'"';
                    $param = preg_replace("/\r\n/", "\\r\\n", $param);
                    $param = preg_replace("/\n/", "\\n", $param);
                    $param = preg_replace("/\r/", "\\r", $param);
                    $param = preg_replace("/\t/", "\\t", $param);
                    $type = '<span'.self::getStyle(self::$style_string).'>string['.strlen($arg).']</span>';
                    break;

                case is_array($arg):
                    $param = print_r($arg, true);
                    $param = preg_replace("/\\r\\n/", " ", $param);
                    $param = preg_replace("/\\n/", " ", $param);
                    $param = preg_replace("/\\t/", " ", $param);
                    $param = preg_replace("'\\s+'i", " ", $param);
                    $param = htmlspecialchars($param);
                    $type = '<span'.self::getStyle(self::$style_array).'>array['.count($arg).']</span>';
                    break;

                case is_object($arg):
                    $param = print_r($arg, 1);
                    $param = preg_replace("/\\r\\n/", " ", $param);
                    $param = preg_replace("/\\n/", " ", $param);
                    $param = preg_replace("/\\t/", " ", $param);
                    $param = preg_replace("'\\s+'i", " ", $param);
                    $param = htmlspecialchars($param);
                    $type = '<span'.self::getStyle(self::$style_object).'>object['.get_class($arg).']</span>';
                    break;

                case is_resource($arg):
                    $param = '"' . get_resource_type($arg) . '"';
                    $type = '<span'.self::getStyle(self::$style_resource).'>resource</span>';
                    break;

                case is_null($arg):
                    $param = '';
                    $type = '<span'.self::getStyle(self::$style_null).'>null</span>';
                    break;

                default:
                    $param = '';
                    $type = '<span'.self::getStyle(self::$style_boolean).'>unknown</span>';
                    break;
            }
            return $type.' '.$param.'';
        }

        public static function Dump($arg)
        {
            return '<pre'.self::getStyle(self::$style_block).'>'.self::varDumpExtend($arg).'</pre>';
        }
    }