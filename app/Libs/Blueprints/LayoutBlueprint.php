<?php

namespace Laraview\Libs\Blueprints;

interface LayoutBlueprint
{

    /**
     * @return mixed
     */
    public function elements();

    /**
     * @param RegionBlueprint $region
     */
    public function created( RegionBlueprint $region );

    /**
     * @return mixed
     */
    public function render();



}
