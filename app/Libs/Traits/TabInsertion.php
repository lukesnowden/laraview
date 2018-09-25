<?php

namespace Laraview\Libs\Traits;

use Exception;
use Laraview\Libs\Blueprints\TabBlueprint;

trait TabInsertion
{

    /**
     * @param $tab
     * @return mixed
     * @throws Exception
     */
    public function getTab( $tab )
    {
        if( ! isset( $this->tabs[ $tab ] ) ) {
            throw new Exception( "Unable to locate tab {$tab}" );
        }
        return $this->tabs[ $tab ];
    }

    /**
     * @param $tab
     * @return $this
     * @throws Exception
     */
    public function insertTab( $tab )
    {
        $this->tabs[ $tab ] = new $tab;
        if( ! $this->tabs[ $tab ] instanceof TabBlueprint ) {
            throw new Exception( "Element {$tab} must implement " . TabBlueprint::class );
        }
        $this->tabs[ $tab ]->region( $this->region );
        return $this;
    }

    /**
     * @param $tab
     * @param $targetElement
     * @return $this
     * @throws Exception
     */
    public function insertTabBefore( $tab, $targetElement )
    {
        $new = [];
        foreach( $this->tabs as $potentialTarget => $val ) {
            if( $potentialTarget === $targetElement ) {
                $new[ $tab ] = new $tab;
                if( ! $new[ $tab ] instanceof TabBlueprint ) {
                    throw new Exception( "Tab {$tab} must implement " . TabBlueprint::class );
                }
                $new[ $tab ]->region( $this->region );
            }
            $new[ $potentialTarget ] = $val;
        }
        $this->tabs = $new;
        return $this;
    }

    /**
     * @param $tab
     * @param $targetElement
     * @return $this
     * @throws Exception
     */
    public function insertTabAfter( $tab, $targetElement )
    {
        $new = [];
        foreach( $this->tabs as $potentialTarget => $val ) {
            $new[ $potentialTarget ] = $val;
            if( $potentialTarget === $targetElement ) {
                $new[ $tab ] = new $tab;
                if( ! $new[ $tab ] instanceof TabBlueprint ) {
                    throw new Exception( "Tab {$tab} must implement " . TabBlueprint::class );
                }
                $new[ $tab ]->region( $this->region );
            }
        }
        $this->tabs = $new;
        return $this;
    }

}
