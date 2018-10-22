<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 12/10/18
 * Time: 16:54
 */

namespace App\Framework\Validator;

/**
 * Class ValidationError
 * @package App\Framework\Validator
 */
class ValidationError
{
    /**
     * @var
     */
    private $key;
    /**
     * @var
     */
    private $rule;

    private $messages = [
        'required' => 'Le champ %s est requis',
        'empty' => 'Le champ %s ne peut être vide.',
        'slug' => 'Le champ %s n\'est pas un slug valide.',
        'minLength' => 'Le champ %s doit contenir plus de %d caractères.',
        'maxLength' => 'Le champ %s doit contenir moins de %d caractères.',
        'betweenLength' => 'Le champ %s doit contenir entre %d et %d caractères.',
        'datetime' => 'Le champ %s doit être une date valide (%s).',
        'exists' => 'Le champ %s n\'existe pas dans la table %s.',
        'unique' => 'Le champ %s doit être unique.',
        'filetype' => 'L\'%s n\'est pas au format valide (formats %s acceptés).',
        'uploaded' => 'Vous devez uploader un fichier.'
    ];

    /**
     * @var array
     */
    private $attributes;

    /**
     * ValidationError constructor.
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    public function __toString()
    {
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return (string)call_user_func_array('sprintf', $params);
    }
}
