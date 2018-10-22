<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 10/10/18
 * Time: 21:51
 */

namespace Framework\Twig;

class TimeExtension extends \Twig_Extension
{
    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('ago', [$this, 'ago'], [
                'is_safe'=> ['html']]) // c'est sécurisé pour le html (force l'affichage du time ago)
        ];
    }

    public function ago(\DateTime $date, string $format = 'd-m-Y')
    {
        //  $date->format(\DateTime::ISO8601)
        return '<time class="timeago" datetime="' . $date->format(\DateTime::ATOM) . '">'
            . $date->format($format) .
            '</time>';
    }
}
