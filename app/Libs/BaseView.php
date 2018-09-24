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
     * @return mixed
     */
    public function path()
    {
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
            foreach( $region->elements() as $element ) {
                $elements[] = $element;
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
        foreach( $this->regions as $key => $region ) {
            $this->baseView = str_replace( $region->placeholder(), $region->render(), $this->baseView );
        }
        return $this->baseView;
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
