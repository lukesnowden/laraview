<?php

namespace Laraview\Libs;

use Exception;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Blueprints\RegionBlueprint;

abstract class BaseRegion implements RegionBlueprint
{

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @var string
     */
    protected $placeholder = '';

    /**
     * Region constructor.
     */
    public function __construct()
    {
        $this->setupElements();
    }

    /**
     * @param $element
     * @return $this
     * @throws Exception
     */
    public function attachElement( $element )
    {
        $this->elements[ $element ] = new $element;
        if( ! $this->elements[ $element ] instanceof ElementBlueprint ) {
            throw new Exception( "Element {$element} must implement " . ElementBlueprint::class );
        }
        return $this;
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
     * @throws Exception
     * @return void
     */
    protected function setupElements()
    {
        $elements = [];
        foreach( $this->elements as $key => $element ) {
            $elements[ $element ] = new $element;
            if( ! $elements[ $element ] instanceof ElementBlueprint ) {
                throw new Exception( "Element {$element} must implement " . ElementBlueprint::class );
            }
        }
        $this->elements = $elements;
    }

}
