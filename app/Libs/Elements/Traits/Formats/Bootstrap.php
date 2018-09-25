<?php

namespace Laraview\Libs\Elements\Traits\Formats;

trait Bootstrap
{

    /**
     * @param $content
     * @param $for
     * @return string
     */
    protected function label( $content, $for = '' )
    {
        return sprintf( '<label for="%s">%s</label>', $for, $content );
    }

    /**
     * @param $content
     * @return string
     */
    protected function wrapper( $content )
    {
        return sprintf( '<div class="form-group">%s</div>', $content );
    }

}
