<?php

namespace Laraview\Libs\Elements;

use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Traits\Formats\PasswordBootstrap;

abstract class Password extends Text implements ElementBlueprint
{

    use PasswordBootstrap;

    /**
     * @return mixed|null|string
     */
    public static function humanReadableName()
    {
        return 'Password Element';
    }

}
