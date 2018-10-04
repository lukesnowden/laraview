<?php

namespace Laraview\Libs\Traits;

use Exception;
use Laraview\Libs\Blueprints\ColumnBlueprint;

trait ColumnInsertion
{

    /**
     * @param $column
     * @return mixed
     * @throws Exception
     */
    public function getColumn( $column )
    {
        if( ! isset( $this->columns[ $column ] ) ) {
            throw new Exception( "Unable to locate column {$column}" );
        }
        return $this->columns[ $column ];
    }

    /**
     * @param $column
     * @return $this
     * @throws Exception
     */
    public function removeColumn( $column )
    {
        if( ! isset( $this->columns[ $column ] ) ) {
            throw new Exception( "Unable to locate tab {$column}" );
        }
        unset( $this->columns[ $column ] );
        return $this;
    }

    /**
     * @param $column
     * @return $this
     * @throws Exception
     */
    public function insertColumn( $column )
    {
        $this->columns[ $column ] = new $column;
        if( ! $this->columns[ $column ] instanceof ColumnBlueprint ) {
            throw new Exception( "Column {$column} must implement " . ColumnBlueprint::class );
        }
        return $this;
    }

    /**
     * @param $column
     * @param $targetColumn
     * @return $this
     * @throws Exception
     */
    public function insertColumnBefore( $column, $targetColumn )
    {
        $new = [];
        foreach( $this->columns as $potentialTarget => $val ) {
            if( $potentialTarget === $targetColumn ) {
                $new[ $column ] = new $column;
                if( ! $new[ $column ] instanceof ColumnBlueprint ) {
                    throw new Exception( "Column {$column} must implement " . ColumnBlueprint::class );
                }
            }
            $new[ $potentialTarget ] = $val;
        }
        $this->columns = $new;
        return $this;
    }

    /**
     * @param $column
     * @param $targetColumn
     * @return $this
     * @throws Exception
     */
    public function insertColumnAfter( $column, $targetColumn )
    {
        $new = [];
        foreach( $this->columns as $potentialTarget => $val ) {
            $new[ $potentialTarget ] = $val;
            if( $potentialTarget === $targetColumn ) {
                $new[ $column ] = new $column;
                if( ! $new[ $column ] instanceof ColumnBlueprint ) {
                    throw new Exception( "Column {$column} must implement " . ColumnBlueprint::class );
                }
            }
        }
        $this->columns = $new;
        return $this;
    }

}
