<?php

namespace classes;

class HTML
{
    public static function tag($tag, $text, array $attributes = [])
    {
        return '<'.$tag .' /'. self::attributesToArray($attributes). '>'.$text.'</$tag>';
    }
    
    public static function minimizedTag($tag, array $attributes = [])
    {
        return "<$tag " .self::attributesToArray($attributes). " />";
    }
    
    public static function attributesToArray(array $attributes = [])
    {
        $a = '';
        
        if (empty($attributes) or !is_array($attributes)){
            return $a;
        }
        
        foreach ($attributes as $k => $v) {
            $a.=  ' '.$k.'="'.$v.'"';
        }
        return $a;
    }
 
    public static function a($link, $text, array $attributes = [])
    {
        $a = ['href' => $link];
    
        foreach ($attributes as $k => $v) 
        {
            $a[$k] = $v;
        }
        return self::tag('a', $text, $a);
    }
    
    public static function img($url, $alt = null)
    {
        $a = ['src' => $url];
    
        if (!is_null($alt))
        {
            if (is_array($alt)) {
                $a = array_merge($a, $alt);
            } else {
                $a['alt'] = $alt;
            }
        }
    
        return self::minimizedTag('img', $a);
    }    
}
