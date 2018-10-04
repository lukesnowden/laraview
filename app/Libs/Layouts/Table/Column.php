<?php

namespace Laraview\Libs\Layouts\Table;

abstract class Column
{

    /**
     * @var string
     */
    protected $rowKey = '';

    /**
     * @var string
     */
    protected $header = '';

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @return string
     */
    public function rowKey() : string
    {
        return $this->rowKey;
    }

    /**
     * @return string
     */
    public function header() : string
    {
        return $this->header;
    }

    /**
     * @return array
     */
    public function attributes() : array
    {
        return $this->attributes;
    }

    /**
     * @param $row
     * @return mixed
     */
    public function render( $row )
    {
        return $row->getAttribute( $this->rowKey() );
    }

    /**
     * @return string
     */
    public function attributesAsHtml()
    {
        $attributes = '';
        foreach( $this->attributes as $name => $value ) {
            $attributes .= " {$name}=\"{$value}\"";
        }
        return trim( $attributes );
    }

}
