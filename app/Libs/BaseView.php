<?php

namespace Laraview\Libs;

use Exception;
use ReflectionClass;
use Laraview\Libs\Blueprints\ViewBlueprint;
use Laraview\Libs\Blueprints\RegionBlueprint;

abstract class BaseView implements ViewBlueprint
{

    /**
     * @var
     */
    protected $path;

    /**
     * @var
     */
    protected $origPath = '';

    /**
     * @var array
     */
    protected $regions = [];

    /**
     * @var string
     */
    protected $baseView = '';

    /**
     * @var string
     */
    protected $baseViewPath = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * ViewBase constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->setupRegions();
        $this->setupBaseView();
    }

    /**
     * @param null $path
     * @return mixed|string
     */
    public function path( $path = null )
    {
        if( ! is_null( $path ) ) {
            if( ! $this->origPath ) {
                $this->origPath = $this->path;
            }
            return $this->path = $path . $this->origPath;
        }
        return $this->path;
    }

    /**
     * @param string $region
     * @return mixed
     * @throws \Exception
     */
    public function retrieveRegion( string $region )
    {
        if( ! isset( $this->regions[ $region ] ) ) {
            throw new Exception( "Region {$region} does not exist." );
        }
        return $this->regions[ $region ];
    }

    /**
     * @return array
     */
    public function regions()
    {
        return $this->regions;
    }

    /**
     * @throws Exception
     * @return void
     */
    protected function setupRegions()
    {
        $regions = [];
        foreach( $this->regions as $key => $region ) {
            $regions[ $region ] = new $region;
            if( ! $regions[ $region ] instanceof RegionBlueprint ) {
                throw new Exception( "Region {$region} must implement " . RegionBlueprint::class );
            }
            $regions[ $region ]->view( $this );
            event( $region  . '.attached', [ $regions[ $region ] ] );
        }
        $this->regions = $regions;
    }

    /**
     * @return array
     */
    public function elements()
    {
        $elements = [];
        foreach( $this->regions as $region ) {
            foreach( $region->getValueReliantObjects() as $element ) {
                $elements[] = $element;
            }
        }
        return $elements;
    }

    /**
     * @return array
     */
    public function elementsNotAssignedToASegment()
    {
        $elements = [];
        foreach( $this->regions as $region ) {
            foreach( $region->getValueReliantObjects() as $element ) {
                if( $element->isNotAssignedToASegment() ) {
                    $elements[] = $element;
                }
            }
        }
        return $elements;
    }

    /**
     * @param $segment
     * @return array
     */
    public function elementsFromSegment( $segment )
    {
        $elements = [];
        foreach( $this->regions as $region ) {
            foreach( $region->getValueReliantObjects() as $element ) {
                if( $element->isFromSegment( $segment ) ) {
                    $elements[] = $element;
                }
            }
        }
        return $elements;
    }

    /**
     * @param array $data
     */
    public function setViewData( array $data )
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function render()
    {
        $render = $this->baseView;
        foreach( $this->regions as $key => $region ) {
            $render = str_replace( $region->placeholder(), $region->render(), $render );
        }
        return $render;
    }

    /**
     * @throws \ReflectionException
     */
    protected function setupBaseView()
    {
        if( $this->baseViewPath && file_exists( $this->getDir( $this->baseViewPath ) ) ) {
            $this->baseView = file_get_contents( $this->getDir( $this->baseViewPath ) );
        }
    }

    /**
     * @param string $extended
     * @return string
     * @throws \ReflectionException
     */
    protected function getDir( $extended = '' )
    {
        $rc = new ReflectionClass( get_class( $this ) );
        return rtrim( dirname( $rc->getFileName() ), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $extended;
    }

}
