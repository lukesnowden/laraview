<?php

namespace Laraview\Libs\Blueprints;

interface ElementBlueprint
{

    /**
     * @return mixed
     */
    public function render();

    /**
     * @return mixed
     */
    public static function humanReadableName();

    /**
     * @return mixed
     */
    public function name();

    /**
     * @param $value
     * @return mixed
     */
    public function setValue( $value );

    /**
     * @return mixed
     */
    public function value();

    /**
     * @return mixed
     */
    public function valueKeyName();

    /**
     * @param RegionBlueprint $region
     * @return mixed
     */
    public function region( RegionBlueprint $region );

}
