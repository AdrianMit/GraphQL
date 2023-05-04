<?php

namespace Dreamlabs\GraphQL\Field;

use Dreamlabs\GraphQL\Type\AbstractType;

/**
 * Class Field
 *
 * @package Dreamlabs\GraphQL\Type\Field
 *
 */
final class Field extends AbstractField
{
    protected bool $isFinal = true;
    protected mixed $_typeCache = null;
    protected mixed $_nameCache = null;
    
    public function getType(): AbstractType
    {
        return $this->_typeCache ?: ($this->_typeCache = $this->getConfigValue('type'));
    }
    
    public function getName()
    {
        return $this->_nameCache ?: ($this->_nameCache = $this->getConfigValue('name'));
    }
    
}
