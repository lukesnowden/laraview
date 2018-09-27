<?php

namespace Laraview\Libs\Elements;

use Laraview\Console\Commands\LaraviewGenerateElement;
use Laraview\Libs\BaseElement;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Generate\InputWithOptionGeneration;
use Laraview\Libs\Elements\Traits\Formats\SelectBootstrap;

abstract class Select extends BaseElement implements ElementBlueprint
{

    use SelectBootstrap;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var null
     */
    protected $value = null;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @return mixed
     */
    public function render()
    {
        $this->preRender();
        return $this->wrapper(
            $this->label( $this->label, $this->attributes[ 'id' ] ) .
            $this->element()
        );
    }

    /**
     * @return mixed|null|string
     */
    public static function humanReadableName()
    {
        return 'Select Element';
    }

    /**
     * @param $region
     * @param LaraviewGenerateElement $console
     * @return mixed
     */
    public static function generate( $region, LaraviewGenerateElement $console )
    {
        $generator = new InputWithOptionGeneration( $region, $console );
        $generator->setStubPath( __DIR__ . '/../../../stubs/elements/select.stub' );
        return $generator->create();
    }

}
