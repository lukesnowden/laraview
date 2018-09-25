<?php

namespace Laraview\Libs\Elements;

use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Traits\Formats\EmailBootstrap;

abstract class Email extends Text implements ElementBlueprint
{

    use EmailBootstrap;

    /**
     * @return mixed|null|string
     */
    public static function humanReadableName()
    {
        return 'Email Element';
    }

}
