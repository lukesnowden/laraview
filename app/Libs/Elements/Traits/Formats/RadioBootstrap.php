<?php

namespace Laraview\Libs\Elements\Traits\Formats;

trait RadioBootstrap
{

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

}
