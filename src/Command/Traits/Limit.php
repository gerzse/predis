<?php

namespace Predis\Command\Traits;

use Predis\Command\Command;
use UnexpectedValueException;

/**
 * @mixin Command
 */
trait Limit
{
    private static $limitModifier = 'LIMIT';

    public function setArguments(array $arguments)
    {
        $argument = $arguments[static::$limitArgumentPositionOffset];
        $argumentsBefore = array_slice($arguments, 0, static::$limitArgumentPositionOffset);

        if (false === $argument) {
            parent::setArguments($argumentsBefore);
            return;
        }

        $argumentsAfter = array_slice($arguments,  static::$limitArgumentPositionOffset + 1);

        if (true === $argument) {
            parent::setArguments(array_merge($argumentsBefore, [self::$limitModifier], $argumentsAfter));
            return;
        }

        if (!is_int($argument)) {
            throw new UnexpectedValueException('Wrong limit argument type');
        }

        parent::setArguments(array_merge($argumentsBefore, [self::$limitModifier], [$argument], $argumentsAfter));
    }
}
