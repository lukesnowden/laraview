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
     * @var string
     */
    protected $segment = '';

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
     * @param $segment
     * @return bool
     */
    public function isFromSegment( $segment )
    {
        return $segment === $this->segment;
    }

    /**
     * @return bool
     */
    public function isNotAssignedToASegment()
    {
        return ! (boolean) $this->segment;
    }

    /**
     * @return void
     */
    public function displaying() {}

    /**
     * @param RegionBlueprint $region
     */
    abstract public function created( RegionBlueprint $region );

    /**
     * @return mixed
     */
    abstract public function elements();

}
