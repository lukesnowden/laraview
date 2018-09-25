<?php

namespace Laraview\Libs\Blueprints;

interface TabBlueprint
{

    /**
     * @param LayoutBlueprint $tabs
     */
    public function tabs( LayoutBlueprint $tabs );

    /**
     * @return array
     */
    public function elements();

    /**
     * @return string
     */
    public function name();

    /**
     * @return mixed
     */
    public function render();

    /**
     * @param $region
     */
    public function setElementsRegion( $region );

}
