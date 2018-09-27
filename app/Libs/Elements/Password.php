<?php

namespace Laraview\Libs\Elements;

use Laraview\Console\Commands\LaraviewGenerateElement;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Generate\InputGeneration;
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

    /**
     * @param $region
     * @param LaraviewGenerateElement $console
     * @return mixed
     */
    public static function generate( $region, LaraviewGenerateElement $console )
    {
        $generator = new InputGeneration( $region, $console );
        $generator->setStubPath( __DIR__ . '/../../../stubs/elements/password.stub' );
        return $generator->create();
    }

}
