<?php

namespace Laraview\Libs;

use Exception;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Blueprints\LayoutBlueprint;
use Laraview\Libs\Blueprints\RegionBlueprint;
use Laraview\Libs\Blueprints\ViewBlueprint;
use Laraview\Libs\Traits\ElementInsertion;

abstract class BaseRegion implements RegionBlueprint
{

    use ElementInsertion;

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @var string
     */
    protected $placeholder = '';

    /**
     * @var null
     */
    protected $view = null;

    /**
     * Region constructor.
     */
    public function __construct()
    {
        $this->setupElements();
    }

    /**
     * @return string
     */
    public function placeholder()
    {
        return $this->placeholder;
    }

    /**
     * @return string
     */
    public function render()
    {
        $html = '';
        foreach( $this->elements as $element ) {
            $html .= $element->render() . "\n";
        }
        return $html;
    }

    /**
     * @param ViewBlueprint $view
     */
    public function view( ViewBlueprint $view )
    {
        $this->view = $view;
    }

    /**
     * @return null
     */
    public function getView()
    {
        return $this->view;
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
            $elements[ $element ]->region( $this );
            $elements[ $element ]->created( $this );
        }
        $this->elements = $elements;
    }

    /**
     * @return array
     */
    public function elements()
    {
        return $this->elements;
    }

    /**
     * @return array
     */
    public function getValueReliantObjects()
    {
        $objects = [];
        foreach( $this->elements as $element ) {
            if( method_exists( $element, 'getValueReliantObjects' ) ) {
                foreach( $element->getValueReliantObjects() as $object ) {
                    $objects[] = $object;
                }
            }
        }
        return $objects;
    }

}
