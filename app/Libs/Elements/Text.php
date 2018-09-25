<?php

namespace Laraview\Libs\Elements;

use Laraview\Libs\BaseElement;
use Laraview\Libs\Blueprints\ElementBlueprint;

abstract class Text extends BaseElement implements ElementBlueprint
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
        return sprintf( '<input type="text" name="%s" %s value="%s" />',
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

    /**
     * @return mixed|null|string
     */
    public static function humanReadableName()
    {
        return 'Text Element';
    }

}
