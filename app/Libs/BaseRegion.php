<?php

namespace Laraview\Libs;

use Exception;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Blueprints\RegionBlueprint;
use Laraview\Libs\Blueprints\ViewBlueprint;

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
     * @param $element
     * @return $this
     * @throws Exception
     */
    public function insertElement( $element )
    {
        $this->elements[ $element ] = new $element;
        if( ! $this->elements[ $element ] instanceof ElementBlueprint ) {
            throw new Exception( "Element {$element} must implement " . ElementBlueprint::class );
        }
        return $this;
    }

    /**
     * @param $element
     * @param $targetElement
     */
    public function insertElementBefore( $element, $targetElement )
    {

    }

    /**
     * @param $element
     * @param $targetElement
     */
    public function insertElementAfter( $element, $targetElement )
    {

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
            if( ! $elements[ $element ] instanceof ElementBlueprint ) {
                throw new Exception( "Element {$element} must implement " . ElementBlueprint::class );
            }
            $elements[ $element ]->region( $this );
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

}
