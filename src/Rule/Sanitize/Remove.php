<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Filter\Rule\Sanitize;

/**
 *
 * Removes the field from the subject with unset().
 *
 * @package Aura.Filter
 *
 */
class Remove
{
    /**
     *
     * Removes the field from the subject with unset().
     *
     * @return bool Always true.
     *
     */
    public function __invoke($subject, $field)
    {
        unset($subject->$field);
        return true;
    }
}
