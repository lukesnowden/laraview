<?php

namespace Laraview\Libs\Elements\Traits\Formats;

trait SelectBootstrap
{

    /**
     * @return string
     */
    protected function element()
    {
        return sprintf( '<select name="%s" %s>%s</select>',
            $this->name,
            $this->attributes(),
            $this->options()
        );
    }

    /**
     * @return string
     */
    protected function options()
    {
        $html = '';
        foreach( $this->options as $value => $text ) {
            $selected = "{{ \${$this->valueKeyName()} === '{$value}' ? 'selected' : '' }}";
            $html .= sprintf( '<option %s value="%s">%s</option>', $selected, $value, $text ) . "\n";
        }
        return $html;
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
