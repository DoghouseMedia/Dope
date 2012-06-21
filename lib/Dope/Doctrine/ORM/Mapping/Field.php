<?php

namespace Dope\Doctrine\ORM\Mapping;
use \Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Field implements Annotation
{
    /** @var string */
    public $type = 'Text';
    /** @var string */
    public $label;
    /** @var string */
    public $group = '';
    /** @var boolean */
    public $required = false;
    /** @var array<string> */
    public $options = array();
    /** @var array<string> */
    public $params = array();
}
