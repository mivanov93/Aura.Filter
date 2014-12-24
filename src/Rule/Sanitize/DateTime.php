<?php
/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @package Aura.Filter
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Filter\Rule\Sanitize;

use DateTime as PhpDateTime;

/**
 *
 * Validate and Sanitize date time
 *
 * @package Aura.Filter
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
class DateTime
{
    /**
     *
     * Sanitize datetime to default format Y-m-d H:i:s
     *
     * @param string $format
     *
     * @return bool True if the value was sanitized, false if not.
     *
     */
    public function sanitize($object, $field, $format = 'Y-m-d H:i:s')
    {
        $value = $object->$field;
        $datetime = $this->newDateTime($value);
        if (! $datetime) {
            return false;
        }
        $object->$field = $datetime->format($format);

        return true;
    }

    /**
     *
     * Returns a new DateTime object.
     *
     * @param mixed $value The incoming date/time value.
     *
     * @return mixed If the value is already a DateTime then it is returned
     *               as-is; if the value is invalid as a date/time then `false` is returned;
     *               otherwise, a new DateTime is constructed from the value and returned.
     *
     */
    protected function newDateTime($value)
    {
        if ($value instanceof PhpDateTime) {
            return $value;
        }

        if (! is_scalar($value)) {
            return false;
        }

        if (trim($value) === '') {
            return false;
        }

        $datetime = date_create($value);

        // generic failure
        if (! $datetime) {
            return false;
        }

        // invalid dates (like 1979-02-29) show up as warnings.
        $errors = PhpDateTime::getLastErrors();
        if ($errors['warnings']) {
            return false;
        }

        // looks OK
        return $datetime;
    }
}
