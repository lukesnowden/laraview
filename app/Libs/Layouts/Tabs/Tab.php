<?php

namespace Laraview\Libs\Layouts\Tabs;

use Exception;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Blueprints\LayoutBlueprint;
use Laraview\Libs\Blueprints\RegionBlueprint;
use Laraview\Libs\Blueprints\TabBlueprint;
use Laraview\Libs\Elements\Traits\Formats\TabBootstrap;
use Laraview\Libs\Traits\ElementInsertion;

abstract class Tab implements TabBlueprint
{

    use TabBootstrap, ElementInsertion;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @var null
     */
    protected $tabs = null;

    /**
     * @var null
     */
    protected $region = null;

    /**
     * Tab constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->setupElements();
    }

    /**
     * @param LayoutBlueprint $tabs
     */
    public function tabs( LayoutBlueprint $tabs )
    {
        $this->tabs = $tabs;
    }

    /**
     * @return array
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
     * @throws Exception
     * @return void
     */
    protected function setupElements()
    {
        $elements = [];
        foreach( $this->elements as $key => $element ) {
            $elements[ $element ] = new $element;
            if( ! $elements[ $element ] instanceof ElementBlueprint ) {
                throw new Exception( "{$element} must implement " . ElementBlueprint::class );
            }
        }
        $this->elements = $elements;
    }

    /**
     * @param $region
     */
    public function setElementsRegion( $region )
    {
        foreach( $this->elements as $element ) {
            $element->region( $region );
        }
    }

    /**
     * @param RegionBlueprint $region
     */
    public function region( RegionBlueprint $region )
    {
        $this->region = $region;
        $this->setElementsRegion( $region );
    }

}
