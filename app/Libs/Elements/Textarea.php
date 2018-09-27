<?php

namespace Laraview\Libs\Elements;

use Laraview\Console\Commands\LaraviewGenerateElement;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Generate\InputGeneration;
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

    /**
     * @param $region
     * @param LaraviewGenerateElement $console
     * @return mixed
     */
    public static function generate( $region, LaraviewGenerateElement $console )
    {
        $generator = new InputGeneration( $region, $console );
        $generator->setStubPath( __DIR__ . '/../../../stubs/elements/textarea.stub' );
        return $generator->create();
    }

}
