<?php

namespace Laraview\Libs\Layouts;

use Exception;
use Laraview\Libs\BaseLayout;
use Laraview\Libs\Blueprints\LayoutBlueprint;
use Laraview\Libs\Blueprints\RegionBlueprint;
use Laraview\Libs\Blueprints\TabBlueprint;
use Laraview\Libs\Elements\Traits\Formats\TabsBootstrap;
use Laraview\Libs\Traits\TabInsertion;

abstract class Tabs extends BaseLayout implements LayoutBlueprint
{

    use TabsBootstrap, TabInsertion;

    /**
     * @var array
     */
    protected $tabs = [];

    /**
     * Tabs constructor.
     * @throws Exception
     * @return void
     */
    public function __construct()
    {
        $this->setupTabs();
    }

    /**
     * @return array|mixed
     */
    public function elements()
    {
        $tabs = [];
        foreach( $this->tabs as $tab ) {
            $tabs = $tabs + $tab->elements();
        }
        return $tabs;
    }

    /**
     * @throws Exception
     * @return void
     */
    private function setupTabs()
    {
        $tabs = [];
        foreach( $this->tabs as $key => $tab ) {
            $tabs[ $tab ] = new $tab;
            if( ! $tabs[ $tab ] instanceof TabBlueprint ) {
                throw new Exception( "{$tab} must implement " . TabBlueprint::class );
            }
            $tabs[ $tab ]->tabs( $this );
        }
        $this->tabs = $tabs;
    }

    /**
     * @param RegionBlueprint $region
     */
    public function created( RegionBlueprint $region )
    {
        foreach( $this->tabs as $tab ) {
            $tab->region( $region );
        }
    }

}
