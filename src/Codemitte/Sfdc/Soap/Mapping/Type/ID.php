<?php
namespace Codemitte\Sfdc\Soap\Mapping\Type;

use Codemitte\Soap\Mapping\Type\GenericType;

/**
 * ID
 */
class ID extends GenericType
{
    /**
     * Create the 18 char ID from a 15 char ID
     *
     * @see JavaScript Version here:
     * @link http://boards.developerforce.com/t5/General-Development/display-18-character-ID-in-Page-Layout/td-p/49900
     *
     * @param $shortId
     * @param string $shortId
     *
     * @return string
     */
    public static function fixSforceId($shortId)
    {
        if (strlen($shortId) !== 15)
        {
            return $shortId;
        }

        $suffix = '';

        for ($i = 0; $i < 3; $i++)
        {
            $flags = 0;

            for ($j = 0; $j < 5; $j++)
            {
                $c = substr($shortId, $i * 5 + $j, 1);

                if (false !== strpos('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $c))
                {
                    $flags += (1 << $j);
                }
            }

            if ($flags <= 25)
            {
                $suffix .= substr('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $flags, 1);
            }
            else
            {
                $suffix .= substr('012345', $flags - 26, 1);
            }
        }

        return new self($shortId . $suffix);
    }

    /**
     * The target namespace of the type.
     *
     * @return string
     */
    public static function getURI()
    {
        return 'urn:enterprise.soap.sforce.com';
    }
}
