<?php

namespace Laraview\Libs\Blueprints;

use Laraview\Libs\Register;

interface RegisterBlueprint
{

    /**
     * @return array
     */
    public function regions();

    /**
     * @param $region
     * @return mixed
     */
    public function getViewForRegion( $region );

    /**
     * @return void
     */
    public function generate();

    /**
     * @param $view
     * @return ViewBlueprint
     */
    public function getView( $view ) : ViewBlueprint;

    /**
     * @param ViewBlueprint $view
     * @return Register
     */
    public function attachView( ViewBlueprint $view ) : Register;

    /**
     * @param $object
     * @return $this
     */
    public function console( $object );

}
