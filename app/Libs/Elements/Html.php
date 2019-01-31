<?php

namespace Laraview\Libs\Elements;

use Laraview\Console\Commands\LaraviewGenerateElement;
use Laraview\Libs\BaseElement;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Generate\InputGeneration;
use Laraview\Libs\Elements\Traits\Formats\HtmlBootstrap;

abstract class Html extends BaseElement implements ElementBlueprint
{

    use HtmlBootstrap;

    /**
     * Label text
     * @var string
     */
    protected $label = '';

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
        return 'HTML Element';
    }

    /**
     * @param $region
     * @param LaraviewGenerateElement $console
     * @return mixed
     */
    public static function generate( $region, LaraviewGenerateElement $console )
    {
        $generator = new InputGeneration( $region, $console );
        $generator->setStubPath( __DIR__ . '/../../../stubs/elements/html.stub' );
        return $generator->create();
    }

    /**
     * @return mixed
     */
    public function value() {}

    /**
     * @param $model
     * @param $request
     */
    public function receivePayload( $model, $request ) {}

}
