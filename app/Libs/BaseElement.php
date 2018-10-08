<?php

namespace Laraview\Libs;

use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Blueprints\RegionBlueprint;
use Laraview\Libs\Elements\Traits\Formats\Bootstrap;

abstract class BaseElement implements ElementBlueprint
{

    use Bootstrap;

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
        return camel_case( preg_replace( '/[^\w\d]/', '', $this->name ) ) . 'Value';
    }

    /**
     * @return mixed
     * @example $this->region->getView()->data() // Use this data to inject...
     */
    public function value()
    {
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
     * @param $model
     * @param $request
     */
    public function receivePayload( $model, $request )
    {
        $model->{$this->name} = $request->input( $this->name );
    }

    /**
     * @param null $dotNotationIndex
     * @return array
     */
    public function data( $dotNotationIndex = null )
    {
        $data = $this->region->getView()->data();
        if( is_null( $dotNotationIndex ) ) {
            return $data;
        }
        return array_get( $data, $dotNotationIndex );
    }

    /**
     * @return array
     */
    public function getValueReliantObjects()
    {
        return [ $this ];
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

    /**
     * @param RegionBlueprint $region
     */
    public function created( RegionBlueprint $region ) {}

    /**
     * @return void
     */
    public function displaying() {}

}
