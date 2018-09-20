<?php

namespace Laraview\Libs\Elements;

use Laraview\Libs\BaseElement;
use Laraview\Libs\Blueprints\ElementBlueprint;

abstract class Select extends BaseElement implements ElementBlueprint
{

    /**
     * @var array
     */
    protected $options = [];

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
            $selected = "{{ old('{$this->name}') && old('{$this->name}') === '{$value}' ? 'selected' : '' }}";
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
