<?php
/**
 * Date: 3/24/17
 *
 * @author Volodymyr Rashchepkin <rashepkin@gmail.com>
 */

namespace Youshido\GraphQL\Directive;


class DirectiveLocation
{

    public const QUERY = 'QUERY';
    public const MUTATION = 'MUTATION';
    public const FIELD = 'FIELD';
    public const FIELD_DEFINITION = 'FIELD_DEFINITION';
    public const FRAGMENT_DEFINITION = 'FRAGMENT_DEFINITION';
    public const FRAGMENT_SPREAD = 'FRAGMENT_SPREAD';
    public const INLINE_FRAGMENT = 'INLINE_FRAGMENT';
    public const ENUM_VALUE = 'ENUM_VALUE';
}
