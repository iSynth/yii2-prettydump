<?php

    namespace sb\prettydumper;

    class DumperImage implements DumperPlugin
    {
        public static function is($arg)
        {
            return is_resource($arg) && get_resource_type($arg) == 'gd';
        }

        public static function format($arg, $tab = 0)
        {
            ob_start();
            imagepng($arg);
            $image = ob_get_contents();
            ob_end_clean();
            return '<span'.Dumper::getStyle(Dumper::$style_resource).'>resource</span> gd image <img src="data:image/png;base64,'.base64_encode($image).'" style="vertical-align:top;" alt="" />'."\n";
        }
    }
