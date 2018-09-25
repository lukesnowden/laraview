<?php

namespace Laraview\Libs\Elements\Traits\Formats;

trait TabBootstrap
{

    /**
     * @return mixed
     */
    public function render()
    {
        $html = '';
        foreach( $this->elements as $element ) {
            $html .= $element->render() . "\n";
        }
        return $html;
    }

}
