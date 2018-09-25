<?php

namespace Laraview\Libs;

use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Blueprints\RegionBlueprint;
use ReflectionClass;

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
     * @var null
     */
    protected $region = null;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var null
     */
    protected $value = null;

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
        return sprintf( '<div class="form-group">%s</div>', $content );
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
     * @param RegionBlueprint $region
     */
    public function region( RegionBlueprint $region )
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function valueKeyName()
    {
        return camel_case( $this->name ) . 'Value';
    }

    /**
     * @return mixed
     */
    public function value()
    {
        /**
         * $this->region->getView()->data() // Use this data to inject...
         */
        return $this->value;
    }

    /**
     * @param $value
     */
    public function setValue( $value )
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    abstract public static function humanReadableName();

    /**
     * @return string
     */
    abstract protected function element();

    /**
     * @return mixed
     */
    abstract public function render();

}
