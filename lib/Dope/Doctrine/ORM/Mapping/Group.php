<?php

namespace Dope\Doctrine\ORM\Mapping;
use \Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Group implements Annotation
{
    /** @var string */
    public $name;
    /** @var string */
    public $label;
}
