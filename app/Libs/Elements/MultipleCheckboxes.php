<?php

namespace Laraview\Libs\Elements;

use Laraview\Console\Commands\LaraviewGenerateElement;
use Laraview\Libs\BaseElement;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Generate\InputGeneration;

abstract class MultipleCheckboxes extends BaseElement implements ElementBlueprint
{

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @return mixed
     */
    public function render()
    {
        $this->preRender();
        return $this->wrapper(
            $this->notLabel( $this->label ) .
            $this->elements()
        );
    }

    /**
     * @param $content
     * @param $for
     * @return string
     */
    protected function notLabel( $content )
    {
        return sprintf( '<label">%s</label>', $content );
    }

    /**
     * @return string
     */
    protected function elements()
    {
        if( ! preg_match( '/\[\]$/', $this->name ) ) {
            $this->name = $this->name . '[]';
        }
        $html = '';
        foreach( $this->options as $value => $text ) {
            $html .= $this->label( $text . $this->singleElement( $value ) );
        }
        return $html;
    }

    /**
     * @param $value
     * @return string
     */
    protected function singleElement( $value )
    {
        return sprintf( '<input type="checkbox" name="%s" %s value="%s" %s />',
            $this->name,
            "{{ in_array( '{$value}', \${$this->valueKeyName()} ) ? 'checked' : '' }}",
            $value,
            $this->attributes()
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
        return 'Multiple Checkbox Element';
    }

    /**
     * @param $region
     * @param LaraviewGenerateElement $console
     * @return mixed
     */
    public static function generate( $region, LaraviewGenerateElement $console )
    {
        $generator = new InputGeneration( $region, $console );
        $generator->setStubPath( __DIR__ . '/../../../stubs/elements/multiple-checkboxes.stub' );
        return $generator->create();
    }

    protected function element() {}

}
