<?php

namespace Laraview\Libs\Elements;

use Laraview\Console\Commands\LaraviewGenerateElement;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Generate\InputGeneration;
use Laraview\Libs\Elements\Traits\Formats\FileBootstrap;

abstract class File extends Text implements ElementBlueprint
{

    use FileBootstrap;

    /**
     * @return mixed|null|string
     */
    public static function humanReadableName()
    {
        return 'File Element';
    }

    /**
     * @param $region
     * @param LaraviewGenerateElement $console
     * @return mixed
     */
    public static function generate( $region, LaraviewGenerateElement $console )
    {
        $generator = new InputGeneration( $region, $console );
        $generator->setStubPath( __DIR__ . '/../../../stubs/elements/file.stub' );
        return $generator->create();
    }

}
