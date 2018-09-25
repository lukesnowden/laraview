<?php

namespace Laraview\Libs\Elements;

use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Traits\Formats\TextareaBootstrap;

abstract class Textarea extends Text implements ElementBlueprint
{

    use TextareaBootstrap;

    /**
     * @return mixed|null|string
     */
    public static function humanReadableName()
    {
        return 'Textarea Element';
    }

}
