<?php
// src/Helpers/FrontHelper.php

namespace App\Helpers;

class FrontHelper
{
    /**
     * Item 15: Fechas Relativas
     */
    public static function time_elapsed_string($datetime, $full = false)
    {
        $now = new \DateTime;
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'año',
            'm' => 'mes',
            'w' => 'semana',
            'd' => 'día',
            'h' => 'hora',
            'i' => 'minuto',
            's' => 'segundo',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? 'hace ' . implode(', ', $string) : 'justo ahora';
    }

    /**
     * Item 14: Shortcodes de Diseño
     * [alerta]Texto[/alerta] -> Box HTML
     * [boton url="..."]Texto[/boton] -> Btn HTML
     */
    public static function parseShortcodes($content)
    {
        // [alerta]...[/alerta]
        $content = preg_replace(
            '/\[alerta\](.*?)\[\/alerta\]/s',
            '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 my-4 mb-4 text-yellow-700 font-medium rounded-r shadow-sm">$1</div>',
            $content
        );

        // [boton url="X"]...[/boton]
        $content = preg_replace_callback(
            '/\[boton url="(.*?)"\](.*?)\[\/boton\]/s',
            function ($matches) {
                $url = $matches[1];
                $text = $matches[2];
                return '<a href="' . $url . '" class="inline-block bg-primary text-white font-bold py-2 px-6 rounded-full shadow-lg hover:bg-orange-600 hover:scale-105 transition transform my-2 decoration-0">' . $text . '</a>';
            },
            $content
        );

        // [video]ID[/video] (Youtube simple)
        $content = preg_replace(
            '/\[video\](.*?)\[\/video\]/s',
            '<div class="aspect-w-16 aspect-h-9 my-6 rounded-xl overflow-hidden shadow-lg"><iframe src="https://www.youtube.com/embed/$1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe></div>',
            $content
        );

        return $content;
    }

    /**
     * Item 11: Breadcrumbs
     * Generador simple basado en URL
     */
    public static function breadcrumbs($title = '')
    {
        $html = '<nav class="text-xs text-gray-500 mb-2 flex items-center gap-1">';
        $html .= '<a href="/" class="hover:text-primary">Inicio</a> <span class="text-gray-300">/</span>';

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = array_filter(explode('/', $path));

        // Custom logic for Tours
        if (strpos($path, '/tour/') !== false) {
            $html .= ' <a href="/#tours" class="hover:text-primary">Tours</a> <span class="text-gray-300">/</span>';
        }

        $html .= ' <span class="text-gray-800 font-semibold">' . htmlspecialchars($title) . '</span>';
        $html .= '</nav>';
        return $html;
    }
}
