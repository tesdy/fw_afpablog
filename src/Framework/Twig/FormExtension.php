<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 13/10/18
 * Time: 13:21
 */

namespace Framework\Twig;

use function DI\string;
use function GuzzleHttp\Psr7\str;

/**
 * Class FormExtension
 * @package Framework\Twig
 */
class FormExtension extends \Twig_Extension
{
    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

    /**
     * Construction de base des champs
     * @param array $context
     * @param string $key
     * @param $value
     * @param string $label
     * @param array $options
     * @return string
     */
    public function field(array $context, string $key, $value, string $label, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $error = $this->getErrorHtml($context, $key);
        $class = 'form-group';
        $value = $this->convertValue($value);
        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
            'id' => $key,
            'name' => $key
        ];

        if ($error) {
            $attributes['class'] .= ' form-control-danger';
            $attributes['class'] .= ' is-invalid';
        }
        if ($type === 'textarea') {
            $rows = $options['rows'] ?? null;
            $input = $this->textarea($value, $attributes, $rows);
        } elseif ($type === 'file') {
            $input = $this->file($attributes);
        } elseif (array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }
        return '<div class="' . $class . "\">
                <label for=\"name\">{$label}</label>
                {$input}
                {$error}
                </div>";
    }

    /**
     * @param $context
     * @param $key
     * @return string
     */
    private function getErrorHtml($context, $key): string
    {
        $error = $context['errors'][$key] ?? false;
        if ($error) {
            $errors = "<div class=\"invalid-feedback\">{$error}</div>";
            return $errors;
        }
        return '';
    }

    /**
     * Mise en forme des champs input
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return '<input type="text" ' . $this->getHtmlFromArray($attributes) . " value=\"{$value}\">";
    }

    private function file(array $attributes)
    {
        return '<input type="file" ' . $this->getHtmlFromArray($attributes) . '>';
    }


    /**
     * Mise en forme des champs Textarea
     * @param null|string $value
     * @param array $attributes
     * @param int $rows
     * @return string
     */
    private function textarea(?string $value, array $attributes, ?int $rows): string
    {
        if ($rows) {
            return '<textarea ' . $this->getHtmlFromArray($attributes) . " rows={$rows}>{$value}</textarea>";
        }
        return '<textarea ' . $this->getHtmlFromArray($attributes) . ">{$value}</textarea>";
    }

    /**
     * Génère un champ de type Select
     * @param null|string $value
     * @param array $options
     * @param array $attributes
     * @return string
     */
    protected function select(?string $value, array $options, array $attributes): string
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . trim($this->getHtmlFromArray($params)) . '>' . $options[$key] . '</option>';
        }, '');
        return '<select ' . $this->getHtmlFromArray($attributes) . '>' . $htmlOptions . '</select>';
    }

    /**
     * @param $value
     * @return string
     */
    private function convertValue($value): string
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string)$value;
    }

    /**
     * @param array $attributes
     * @return string
     */
    private function getHtmlFromArray(array $attributes): string
    {
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParts[] = (string) $key;
            } elseif ($value !== false) {
                $htmlParts[] = "$key=\"$value\"";
            }
        }
        return implode(' ', $htmlParts);
    }
}
