<?php

namespace Laraview\Libs\Utils;

use Illuminate\View\View;

class ViewDispatcher
{

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var array
     */
    private $data = [];

    /**
     * ViewDispatcher constructor.
     * @param View $view
     */
    public function __construct( View $view )
    {
        $this->data = $view->getData();
        $this->name = $view->getName();
        $this->dispatchEvent();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get( $key )
    {
        if( isset( $this->data[ $key ] ) ) {
            return $this->data[ $key ];
        }
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function attach( $key, $value )
    {
        $this->data[ $key ] = $value;
        return $this;
    }

    /**
     * @param $key
     */
    public function detach( $key )
    {
        if( isset( $this->data[ $key ] ) ) {
            unset( $this->data[ $key ] );
        }
    }

    /**
     * @return void
     */
    private function dispatchEvent()
    {
        event( "view:{$this->name}", [ $this ] );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function view()
    {
        return view( $this->name, $this->data );
    }

    /**
     * @param $path
     */
    public function setPath( $path )
    {
        $this->name = $path;
    }

}
