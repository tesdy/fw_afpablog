<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 10/10/18
 * Time: 15:59
 */

namespace Framework\Twig;

/**
 * Série d'extension concernant les textes
 * Class TextExtensions
 * @package Framework\Twig
 */
class TextExtension extends \Twig_Extension
{

    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        $callable = [$this, 'excerpt'];
        $twig_SimpleFilter = new \Twig_SimpleFilter('excerpt', $callable);
        return
        [
            $twig_SimpleFilter
        ];
    }

    /**
     * Renvoie un extrait du texte
     * @param string $content
     * @param int $maxLentgth
     * @return string
     */
    public function excerpt(?string $content, int $maxLentgth = 250): string
    {
        if ($content === null) {
            return '';
        }
        if (mb_strlen($content) > $maxLentgth) {
            $excerpt = mb_substr($content, 0, $maxLentgth);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }
}
