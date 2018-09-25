<?php

namespace Laraview\Libs;

use Laraview\Libs\Blueprints\LayoutBlueprint;
use Laraview\Libs\Blueprints\RegionBlueprint;

abstract class BaseLayout implements LayoutBlueprint
{

    /**
     * @var
     */
    protected $region;

    /**
     * @param RegionBlueprint $region
     */
    public function region( RegionBlueprint $region )
    {
        $this->region = $region;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param RegionBlueprint $region
     */
    abstract public function created( RegionBlueprint $region );

    /**
     * @return mixed
     */
    abstract public function elements();

}
