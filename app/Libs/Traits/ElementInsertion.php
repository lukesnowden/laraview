<?php

namespace Laraview\Libs\Traits;

use Exception;
use Laraview\Libs\Blueprints\ElementBlueprint;

trait ElementInsertion
{

    /**
     * @param $element
     * @return mixed
     * @throws Exception
     */
    public function getElement( $element )
    {
        if( ! isset( $this->elements[ $element ] ) ) {
            throw new Exception( "Unable to locate element {$element}" );
        }
        return $this->elements[ $element ];
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
        $this->elements[ $element ]->region( $this->region );
        return $this;
    }

    /**
     * @param $element
     * @param $targetElement
     * @throws Exception
     */
    public function insertElementBefore( $element, $targetElement )
    {
        $new = [];
        foreach( $this->elements as $potentialTarget => $val ) {
            if( $potentialTarget === $targetElement ) {
                $new[ $element ] = new $element;
                if( ! $new[ $element ] instanceof ElementBlueprint ) {
                    throw new Exception( "Element {$element} must implement " . ElementBlueprint::class );
                }
                $new[ $element ]->region( $this->region );
            }
            $new[ $potentialTarget ] = $val;
        }
        $this->elements = $new;
    }

    /**
     * @param $element
     * @param $targetElement
     * @throws Exception
     */
    public function insertElementAfter( $element, $targetElement )
    {
        $new = [];
        foreach( $this->elements as $potentialTarget => $val ) {
            $new[ $potentialTarget ] = $val;
            if( $potentialTarget === $targetElement ) {
                $new[ $element ] = new $element;
                if( ! $new[ $element ] instanceof ElementBlueprint ) {
                    throw new Exception( "Element {$element} must implement " . ElementBlueprint::class );
                }
                $new[ $element ]->region( $this->region );
            }
        }
        $this->elements = $new;
    }

}
