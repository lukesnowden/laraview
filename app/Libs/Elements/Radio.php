<?php

namespace Laraview\Libs\Elements;

use Laraview\Libs\BaseElement;
use Laraview\Libs\Blueprints\ElementBlueprint;

abstract class Radio extends BaseElement implements ElementBlueprint
{

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
     * @return string
     */
    protected function element()
    {
        return sprintf( '<input type="radio" name="%s" %s value="%s" %s />',
            $this->name,
            $this->attributes(),
            "{{ \${$this->valueKeyName()} }}",
            "{{ \${$this->statusKeyName()} }}"
        );
    }

    /**
     * @return void
     */
    protected function preRender()
    {
        parent::preRender();
        $this->attributes[ 'class' ] = trim( ( isset( $this->attributes[ 'class' ] ) ? $this->attributes[ 'class' ] : '' ) . ' form-control' );
    }

    /**
     * @return string
     */
    public function statusKeyName()
    {
        return camel_case( $this->name ) . 'Status';
    }

    /**
     * @return bool
     */
    abstract public function status();

}
