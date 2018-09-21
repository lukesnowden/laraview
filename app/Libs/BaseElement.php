<?php

namespace Laraview\Libs;

use Laraview\Libs\Blueprints\ElementBlueprint;

abstract class BaseElement implements ElementBlueprint
{

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param $content
     * @param $for
     * @return string
     */
    protected function label( $content, $for = '' )
    {
        return sprintf( '<label for="%s">%s</label>', $for, $content );
    }

    /**
     * @param $content
     * @return string
     */
    protected function wrapper( $content )
    {
        return sprintf( '<div>%s</div>', $content );
    }

    /**
     * @return string
     */
    protected function attributes()
    {
        $attributes = '';
        foreach( $this->attributes as $name => $value ) {
            $attributes .= "{$name}=\"{$value}\" ";
        }
        return trim( $attributes );
    }

    /**
     * @return void
     */
    protected function preRender()
    {
        if( ! isset( $this->attributes[ 'id' ] ) ) {
            $this->attributes[ 'id' ] = $this->name;
        }
    }

    /**
     * @return string
     */
    abstract protected function element();

    /**
     * @return mixed
     */
    abstract public function render();

}
