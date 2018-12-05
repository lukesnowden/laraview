<?php

namespace Laraview\Libs\Traits;

use Exception;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Blueprints\LayoutBlueprint;

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
    public function removeElement( $element )
    {
        if( ! isset( $this->elements[ $element ] ) ) {
            throw new Exception( "Unable to locate element {$element}" );
        }
        unset( $this->elements[ $element ] );
        return $this;
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
        if( property_exists( $this, 'region' ) ) {
            $this->elements[ $element ]->region( $this->region );
        } else {
            $this->elements[ $element ]->region( $this );
        }
        return $this;
    }

    /**
     * @param $layout
     * @return $this
     * @throws Exception
     */
    public function insertLayout( $layout )
    {
        $this->elements[ $layout ] = new $layout;
        if( ! $this->elements[ $layout ] instanceof LayoutBlueprint ) {
            throw new Exception( "Layout {$layout} must implement " . LayoutBlueprint::class );
        }
        if( property_exists( $this, 'region' ) ) {
            $this->elements[ $layout ]->region( $this->region );
        } else {
            $this->elements[ $layout ]->region( $this );
        }
        return $this;
    }

    /**
     * @param $element
     * @param $targetElement
     * @return $this
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
                if( property_exists( $this, 'region' ) ) {
                    $new[ $element ]->region( $this->region );
                } else {
                    $new[ $element ]->region( $this );
                }
            }
            $new[ $potentialTarget ] = $val;
        }
        $this->elements = $new;
        return $this;
    }

    /**
     * @param $element
     * @param $targetElement
     * @return $this
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
                if( property_exists( $this, 'region' ) ) {
                    $new[ $element ]->region( $this->region );
                } else {
                    $new[ $element ]->region( $this );
                }
            }
        }
        $this->elements = $new;
        return $this;
    }

}
