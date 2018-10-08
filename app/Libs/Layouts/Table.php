<?php

namespace Laraview\Libs\Layouts;

use Exception;
use Laraview\Console\Commands\LaraviewGenerateLayout;
use Laraview\Libs\BaseLayout;
use Laraview\Libs\Blueprints\ColumnBlueprint;
use Laraview\Libs\Blueprints\LayoutBlueprint;
use Laraview\Libs\Blueprints\RegionBlueprint;
use Laraview\Libs\Layouts\Table\GenerationSupport;
use Laraview\Libs\Traits\ColumnInsertion;
use ReflectionClass;

abstract class Table extends BaseLayout implements LayoutBlueprint
{

    use ColumnInsertion;

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var string
     */
    protected $noResultsMessage = 'No entries available';

    /**
     * Table constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->setUpColumns();
    }

    public function preRender() {}

    /**
     * @return array
     */
    public function rows()
    {
        return $this->rows;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function valueKeyName()
    {
        return lcfirst( ( new ReflectionClass( $this ) )->getShortName() ) . 'Data';
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function name()
    {
        return $this->valueKeyName();
    }

    /**
     * @return $this
     */
    public function value()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function columns() : array
    {
        return $this->columns;
    }

    /**
     * @param $region
     * @param LaraviewGenerateLayout $console
     * @return mixed
     */
    public static function generate( $region, LaraviewGenerateLayout $console )
    {
        $generator = new GenerationSupport( $region, $console );
        return $generator->create();
    }

    /**
     * @param RegionBlueprint $region
     */
    public function created( RegionBlueprint $region )
    {
        $this->region = $region;
    }

    /**
     * @return mixed|string
     * @throws \ReflectionException
     */
    public function render()
    {
        return $this->wrapper(
            $this->header(),
            $this->body(),
            $this->footer()
        );
    }

    /**
     * @throws Exception
     */
    protected function setUpColumns()
    {
        $columns = [];
        foreach( $this->columns as $key => $column ) {
            $columns[ $column ] = new $column;
            if( ! $columns[ $column ] instanceof ColumnBlueprint ) {
                throw new Exception( "{$column} must implement " . ColumnBlueprint::class );
            }
        }
        $this->columns = $columns;
    }

    /**
     * @return array|mixed
     */
    public function elements()
    {
        return [ $this ];
    }

    /**
     * @return string
     */
    protected function header()
    {
        $html = '<tr>';
        foreach( $this->columns as $column ) {
            $html .= sprintf( '<th %s>%s</th>', $column->attributesAsHtml(), $column->header() );
        }
        return $html . '</tr>';
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function body()
    {
        return sprintf(
            file_get_contents( __DIR__ . '/../../../stubs/partials/table_body.stub' ),
            $this->valueKeyName(),
            $this->valueKeyName(),
            $this->valueKeyName(),
            $this->valueKeyName(),
            $this->valueKeyName()
        );
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function footer()
    {
        return sprintf(
            '<tfoot><tr><td colspan="%s">%s</td></tr></tfoot>',
            count( $this->columns ),
            '{!! $' . $this->valueKeyName() . '->tfootContent() !!}'
        );
    }

    /**
     * @return mixed
     */
    public function tfootContent()
    {
        return '';
    }

    /**
     * @param $header
     * @param $body
     * @param $footer
     * @return string
     */
    protected function wrapper( $header, $body, $footer ) : string
    {
        return sprintf( '
            <table class="table table-hover">
                <thead>%s</thead>
                <tbody>%s</tbody>
                %s
            </table>
        ', $header, $body, $footer );
    }

    /**
     * @return array
     */
    public function getValueReliantObjects()
    {
        return $this->elements();
    }

    /**
     * @return string
     */
    public function noRowsMessage()
    {
        return $this->noResultsMessage;
    }

}
