<?php

namespace Laraview\Libs\Blueprints;

interface ViewBlueprint
{

    /**
     * @param $region
     * @return mixed
     */
    public function retrieveRegion( string $region );

    /**
     * @return mixed
     */
    public function path();

    /**
     * @return string
     */
    public function render();

}
