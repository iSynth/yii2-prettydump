<?php

    namespace sb\prettydumper;

    class DumperArray implements DumperPlugin
    {
        public static $collapse_level = 6;
        public static $max_level = 3;
        public static $tab = '  ';
        private static $onclick = "var s1=document.getElementById('dump_array_close_%s').style; var s2=document.getElementById('dump_array_open_%s').style; s1.display=s1.display=='none'?'inline':'none'; s2.display=s2.display=='none'?'inline':'none';";

        public static function is($arg)
        {
            return is_array($arg);
        }

        public static function format($arg, $tab = 0)
        {
            static $id;
            if (self::$max_level > $tab)
            {
                if (is_null($id)) $id = 0;
                $id++;
                if (self::$collapse_level < $tab)
                {
                    $open = Dumper::getStyle('display:none;border:1px;');
                    $close = '';
                }
                else
                {
                    $open = '';
                    $close = Dumper::getStyle('display:none;');
                }
                $output = "<span id=\"dump_array_close_$id\"$close>";
                $output .= '<span'.Dumper::getStyle('cursor:pointer;'.Dumper::$style_array).' onclick="'.sprintf(self::$onclick, $id, $id).'">array['.count($arg).']</span> (...)'."\n";
                $output .= '</span>';
                $output .= "<span id=\"dump_array_open_$id\"$open>";
                $output .= '<span'.Dumper::getStyle('cursor:pointer;'.Dumper::$style_array).' onclick="'.sprintf(self::$onclick, $id, $id).'">array['.count($arg).']</span>'."\n";
                $output .= str_repeat(self::$tab, $tab)."(\n";
                $tab++;
                foreach ($arg as $key => $value) $output .= str_repeat(self::$tab, $tab).'<span'.Dumper::getStyle(Dumper::$style_array).'>[<span'.Dumper::getStyle(Dumper::$style_key).'>'.$key.'</span>]</span> => '.Dumper::varDumpExtend($value, $tab)."";
                $tab--;
                $output .= str_repeat(self::$tab, $tab).")\n";
                $tab--;
                $output .= "</span>";
            }
            else $output = "WARNING: max dump level\n";
            return $output;
        }
    }
