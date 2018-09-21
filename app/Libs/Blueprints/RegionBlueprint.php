<?php

namespace Laraview\Libs\Blueprints;

interface RegionBlueprint
{

    /**
     * @param $element
     * @return $this
     * @throws \Exception
     */
    public function insertElement( $element );

    /**
     * @param $element
     * @param $targetElement
     */
    public function insertElementBefore( $element, $targetElement );

    /**
     * @param $element
     * @param $targetElement
     */
    public function insertElementAfter( $element, $targetElement );

    /**
     * @return string
     */
    public function placeholder();

    /**
     * @return string
     */
    public function render();


}
