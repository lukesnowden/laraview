<?php

namespace Laraview\Libs\Layouts;

use Exception;
use Laraview\Console\Commands\LaraviewGenerateLayout;
use Laraview\Libs\BaseLayout;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Blueprints\LayoutBlueprint;
use Laraview\Libs\Blueprints\RegionBlueprint;
use Laraview\Libs\Elements\Traits\Formats\ModalBootstrap;
use Laraview\Libs\Layouts\Modal\GenerationSupport;
use Laraview\Libs\Traits\ElementInsertion;

abstract class Modal extends BaseLayout implements LayoutBlueprint
{

    use ModalBootstrap,
        ElementInsertion;

    /**
     * @var string
     */
    protected $action = '';

    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @var string
     */
    protected $closeButtonText = 'Close';

    /**
     * @var string
     */
    protected $submitButtonText = 'Submit';

    /**
     * @var string
     */
    protected $size = '';

    /**
     * @return string
     */
    public function action()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * Tabs constructor.
     * @throws Exception
     * @return void
     */
    public function __construct()
    {
        $this->setupElements();
    }

    /**
     * @return array|mixed
     */
    public function elements()
    {
        return $this->elements;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param $region
     * @param LaraviewGenerateLayout $console
     * @return mixed
     */
    public static function generate( $region, LaraviewGenerateLayout $console )
    {
        $generator = new GenerationSupport( $region, $console );
        return $generator->create();
    }

    /**
     * @return string
     */
    protected function renderElements()
    {
        $html = '';
        foreach( $this->elements as $element ) {
            $html .= $element->render() . "\n";
        }
        return $html;
    }

    /**
     * @throws Exception
     * @return void
     */
    protected function setupElements()
    {
        $elements = [];
        foreach( $this->elements as $key => $element ) {
            $elements[ $element ] = new $element;
            if( ! $elements[ $element ] instanceof ElementBlueprint && ! $elements[ $element ] instanceof LayoutBlueprint ) {
                throw new Exception( "{$element} must implement " . ElementBlueprint::class . ' or ' . LayoutBlueprint::class );
            }
        }
        $this->elements = $elements;
    }

    /**
     * @param RegionBlueprint $region
     */
    public function created( RegionBlueprint $region )
    {
        $this->region = $region;
        foreach( $this->elements as $element ) {
            $element->region( $region );
        }
    }

    /**
     * @return array
     */
    public function getValueReliantObjects()
    {
        $objects = [];
        foreach( $this->elements as $element ) {
            $objects[] = $element;
        }
        return $objects;
    }

    /**
     * @return void
     */
    public function preRender() {}

}
