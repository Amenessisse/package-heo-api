<?php

namespace Amenessisse\PackageHeoAPI;

use Exception;

/** Exception levée lorsque la longueur maximale d'une URL est dépassée */
class UrlLengthExceededException extends Exception
{
    /**
     * @param int $actualLength La longueur actuelle de l'URL
     * @param int $maxLength La longueur maximale autorisée
     */
    public function __construct(int $actualLength, int $maxLength)
    {
        $message = sprintf(
            'La longueur de l\'URL (%d caractères) dépasse la limite maximale autorisée de %d caractères.',
            $actualLength,
            $maxLength
        );

        parent::__construct($message);
    }
}