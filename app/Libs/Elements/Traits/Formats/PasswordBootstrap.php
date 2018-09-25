<?php

namespace Laraview\Libs\Elements\Traits\Formats;

trait PasswordBootstrap
{

    /**
     * @return string
     */
    protected function element()
    {
        return sprintf( '<input type="password" name="%s" %s value="%s" />',
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
