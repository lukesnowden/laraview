<?php

namespace Laraview\Libs\Elements\Traits\Formats;

trait DateBootstrap
{

    /**
     * @return string
     */
    protected function element()
    {
        return sprintf( '<input type="date" name="%s" %s value="%s" />',
            $this->name,
            $this->attributes(),
            "{{ \${$this->valueKeyName()} }}"
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
