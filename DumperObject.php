<?php

    namespace sb\prettydumper;

    class DumperObject implements DumperPlugin
    {
        public static $collapse_level = 6;
        public static $max_level = 20;
        public static $tab = '  ';
        private static $onclick = "var s1=document.getElementById('dump_object_close_%s').style; var s2=document.getElementById('dump_object_open_%s').style; s1.display=s1.display=='none'?'inline':'none'; s2.display=s2.display=='none'?'inline':'none';";

        public static function is($arg)
        {
            return is_object($arg);
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
                $class = new \ReflectionObject($arg);
                $cparam = $class->isInternal() ? 'internal ' : 'user ';
                $cparam .= $class->isFinal() ? 'final ' : '';
                $cparam .= $class->isAbstract() ? 'abstract ' : '';
                $cparam .= $class->isInterface() ? 'interface' : 'class';
                $extends = self::getReflectionExtends($class->getParentClass());
                $interfaces = $class->getInterfaces();
                $extends_out = '';
                if (count($extends) > 0)
                {
                    foreach ($extends as $ename) $extends_out .= ' << ' . $ename;
                    $extends_out .= '';
                }
                $interfaces_out = '';
                if (count($interfaces) > 0)
                {
                    foreach ($interfaces as $interface) $interfaces_out .= ', '.$interface->getName();
                    $interfaces_out = ' : '.substr($interfaces_out, 2);
                }
                $output = "<span id=\"dump_object_close_$id\"$close>";
                $output .= '<span'.Dumper::getStyle('cursor:pointer;'.Dumper::$style_object).' onclick="'.sprintf(self::$onclick, $id, $id).'">object['.get_class($arg).']</span> (...)'."\n";
                $output .= '</span>';
                $output .= "<span id=\"dump_object_open_$id\"$open>";
                $output .= '<span'.Dumper::getStyle('cursor:pointer;'.Dumper::$style_object).' onclick="'.sprintf(self::$onclick, $id, $id).'">object['.$cparam.' '.get_class($arg).']</span>'.$extends_out.$interfaces_out."\n";
                //$tab++;
                $output .= str_repeat(self::$tab, $tab)."(\n";
                $tab++;
                $properties = $class->getProperties();
                foreach ($properties as $prop)
                {
                    $pparam = $prop->isPublic() ? 'public ' : '';
                    $pparam .= $prop->isPrivate() ? 'private ' : '';
                    $pparam .= $prop->isProtected() ? 'protected ' : '';
                    $pparam .= $prop->isStatic() ? 'static ' : '';
                    if ($prop->isPublic())
                    {
                        $value = $prop->getValue($arg);
                        $error = false;
                    }
                    else $error = true;
                    if (!$error) $output .= str_repeat(self::$tab, $tab).'<span'.Dumper::getStyle(Dumper::$style_object).'>'.$pparam.'property '.$prop->getName().'</span> -> '.Dumper::varDumpExtend($value, $tab);
                    else $output .= str_repeat(self::$tab, $tab).'<span'.Dumper::getStyle(Dumper::$style_object).'>'.$pparam.'property '.$prop->getName()."</span> -> WARNING! value is hidden\n";
                }
                if (count($extends) > 0)
                {
                    foreach ($extends as $ename)
                    {
                        $eclass = new \ReflectionClass($ename);
                        $eproperties = $eclass->getProperties();
                        foreach ($eproperties as $eprop)
                        {
                            if ($eprop->isPrivate())
                            {
                                $epparam = $eprop->isStatic() ? 'static ' : '';
                                try
                                {
                                    $evalue = $eprop->getValue($arg);
                                }
                                catch (Exception $e)
                                {
                                    $evalue = null;
                                }
                                $output .= str_repeat(self::$tab, $tab).'<span'.Dumper::getStyle(Dumper::$style_object).'>private '.$epparam.'property '.$eclass->getName().'::'.$eprop->getName().'</span> -> '.Dumper::varDumpExtend($evalue, $tab);
                            }
                        }
                    }
                }
                $metods = $class->getMethods();
                foreach ($metods as $method)
                {
                    $mparam = $method->isAbstract() ? 'abstract ' : '';
                    $mparam .= $method->isFinal() ? 'final ' : '';
                    $mparam .= $method->isPublic() ? 'public ' : '';
                    $mparam .= $method->isPrivate() ? 'private ' : '';
                    $mparam .= $method->isProtected() ? 'protected ' : '';
                    $mparam .= $method->isStatic() ? 'static ' : '';
                    $mparam .= $method->isConstructor() ? 'constructor' : 'method';
                    $param_out = '';
                    $opion = 0;
                    foreach ($method->getParameters() as $i => $param)
                    {
                        $pr = $param->getClass();
                        if ($param->isOptional())
                        {
                            $param_out .= '[';
                            $opion++;
                        }
                        $param_out .= (is_object($pr) ? $pr->getName().' ' : '').'$'.$param->getName().', ';
                    }
                    $param_out = substr($param_out, 0, -2);
                    $param_out .= str_repeat(']', $opion);
                    $output .= str_repeat(self::$tab, $tab).'<span'.Dumper::getStyle(Dumper::$style_object).'>'.$mparam.' '.$method->getName().'('.$param_out . ')</span>'."\n";
                }
                $tab--;
                $output .= str_repeat(self::$tab, $tab).")\n";
                $tab--;
                $output .= "</span>";
            }
            else $output = "WARNING: max dump level\n";
            return $output;
        }

        public static function getReflectionExtends($ref_class, $arr = array())
        {
            if (is_object($ref_class))
            {
                $arr[] = $ref_class->getName();
                $extends = $ref_class->getParentClass();
                if ($extends) $arr = self::getReflectionExtends($extends, $arr);
            }
            return $arr;
        }

        public static function getReflectionInterfaces($ref_class, $arr = array())
        {
            if (is_object($ref_class))
            {
                $arr[] = $ref_class->getName();
                $extends = $ref_class->getInterfaces();
                if ($extends) $arr = self::getReflectionInterfaces($extends, $arr);
            }
            return $arr;
        }
    }
