<?php

namespace Laraview\Libs\Elements;

use Laraview\Libs\BaseElement;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Traits\Formats\RadioBootstrap;

abstract class Radio extends BaseElement implements ElementBlueprint
{

    use RadioBootstrap;

    /**
     * @return mixed
     */
    public function render()
    {
        $this->preRender();
        return $this->wrapper(
            $this->label( $this->label, $this->attributes[ 'id' ] ) .
            $this->element()
        );
    }

    /**
     * @return string
     */
    public function statusKeyName()
    {
        return camel_case( $this->name ) . 'Status';
    }

    /**
     * @return bool
     */
    abstract public function status();

    /**
     * @return mixed|null|string
     */
    public static function humanReadableName()
    {
        return 'Radio Element';
    }

}
