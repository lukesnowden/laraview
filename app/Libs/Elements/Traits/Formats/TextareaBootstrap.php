<?php

namespace Laraview\Libs\Elements\Traits\Formats;

trait TextareaBootstrap
{

    /**
     * @return string
     */
    protected function element()
    {
        return sprintf( '<textarea type="text" name="%s" %s>%s</textarea>',
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
