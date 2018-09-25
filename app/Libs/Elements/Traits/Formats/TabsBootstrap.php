<?php

namespace Laraview\Libs\Elements\Traits\Formats;

trait TabsBootstrap
{

    /**
     * @return mixed
     */
    public function render()
    {
        return $this->headerWrapper(
                $this->headers()
            ) .
            $this->bodyWrapper(
                $this->bodies()
            );
    }

    /**
     * @param $content
     * @return string
     */
    protected function headerWrapper( $content )
    {
        return sprintf( '<ul class="nav nav-tabs" id="myTab" role="tablist">%s</ul>', $content );
    }

    /**
     * @param $content
     * @return string
     */
    protected function bodyWrapper( $content )
    {
        return sprintf( '<div class="tab-content" id="myTabContent">%s</div>', $content );
    }

    /**
     * @return string
     */
    protected function headers()
    {
        $html = '';
        $x = 0;
        foreach( $this->tabs as $key => $tab ) {
            $html .= sprintf( '<li class="nav-item">
                <a class="nav-link %s" id="%s" data-toggle="tab" href="#%s" role="tab" aria-controls="home" aria-selected="true">%s</a>
            </li>',
                ! $x ? 'active' : '',
                str_slug( $tab->name() ) . '-tab-trigger',
                str_slug( $tab->name() ) . '-tab',
                $tab->name()
            );
            $x++;
        }
        return $html;
    }

    /**
     * @return string
     */
    protected function bodies()
    {
        $html = '';
        $x = 0;
        foreach( $this->tabs as $key => $tab ) {
            $html .= sprintf( '<div class="tab-pane pt-5 %s" id="%s" role="tabpanel" aria-labelledby="%s">%s</div>',
                ! $x ? 'show active' : '',
                str_slug( $tab->name() ) . '-tab',
                str_slug( $tab->name() ),
                $tab->render()
            );
            $x++;
        }
        return $html;
    }

}
