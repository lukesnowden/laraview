<?php

namespace Laraview\Libs;

use Exception;
use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Blueprints\LayoutBlueprint;
use Laraview\Libs\Blueprints\ViewBlueprint;
use Laraview\Libs\Blueprints\RegisterBlueprint;
use Laraview\Libs\Elements\Checkbox;
use Laraview\Libs\Elements\File;
use Laraview\Libs\Elements\Html;
use Laraview\Libs\Elements\Number;
use Laraview\Libs\Elements\Time;
use Laraview\Libs\Elements\Date;
use Laraview\Libs\Elements\Email;
use Laraview\Libs\Elements\MultipleCheckboxes;
use Laraview\Libs\Elements\Password;
use Laraview\Libs\Elements\Radio;
use Laraview\Libs\Elements\Select;
use Laraview\Libs\Elements\Text;
use Laraview\Libs\Elements\Textarea;
use Laraview\Libs\Layouts\Modal;
use Laraview\Libs\Layouts\Table;
use Laraview\Libs\Layouts\Tabs;

class Register implements RegisterBlueprint
{

    /**
     * @var array
     */
    protected $views = [];

    /**
     * @var null
     */
    protected $console = null;

    /**
     * @var array
     */
    protected static $states = [];

    /**
     * @var array
     */
    protected $registeredElements = [
        Text::class,
        Select::class,
        Number::class,
        Email::class,
        Password::class,
        Radio::class,
        Checkbox::class,
        MultipleCheckboxes::class,
        Textarea::class,
        Date::class,
        Time::class,
        File::class,
        Html::class
    ];

    /**
     * @var array
     */
    protected $registeredLayouts = [
        Tabs::class,
        Table::class,
        Modal::class,
    ];

    /**
     * @param $state
     */
    public static function addState( $state )
    {
        self::$states[] = $state;
    }

    /**
     * @return array
     */
    public function states()
    {
        return self::$states;
    }

    /**
     * @return array
     */
    public function registeredElements()
    {
        return $this->registeredElements;
    }

    /**
     * @return array
     */
    public function registeredLayouts()
    {
        return $this->registeredLayouts;
    }

    /**
     * @param $layout
     * @throws Exception
     * @return void
     */
    public function registerLayout( $layout )
    {
        if( ! (new $layout) instanceof LayoutBlueprint ) {
            throw new Exception( "{$layout} is not an instance of " . LayoutBlueprint::class );
        }
        $this->registeredLayouts[] = $layout;
    }

    /**
     * @param $element
     * @throws Exception
     */
    public function registerElement( $element )
    {
        if( ! (new $element) instanceof ElementBlueprint ) {
            throw new Exception( "{$element} is not an instance of " . ElementBlueprint::class );
        }
        $this->registeredElements[] = $element;
    }

    /**
     * @param $object
     * @return $this
     */
    public function console( $object )
    {
        $this->console = $object;
        return $this;
    }

    /**
     * @param ViewBlueprint $view
     * @return $this
     */
    public function attachView( ViewBlueprint $view ) : self
    {
        $this->views[ get_class( $view ) ] = $view;
        return $this;
    }

    /**
     * @param array $views
     * @return Register
     */
    public function attachViews( array $views ) : self
    {
        foreach( $views as $view ) {
            $this->attachView( $view );
        }
        return $this;
    }

    /**
     * @param $view
     * @return ViewBlueprint
     */
    public function getView( $view ) : ViewBlueprint
    {
        return $this->views[ $view ];
    }

    /**
     * @param $state
     */
    protected function setState( $state )
    {
        if( $state[ 'type' ] === 'session' ) {
            $this->setSession( $state[ 'key' ], $state[ 'value' ] );
        }
        event( 'laraview:state-set' );
    }

    /**
     * @param $key
     * @param $value
     */
    protected function setSession( $key, $value )
    {
        session()->put( $key, $value );
    }

    /**
     * @return void
     */
    public function generate()
    {
        if( self::$states ) {
            foreach( self::$states as $state ) {
                $this->setState( $state );
                $this->generateState( $state[ 'prefix' ] ? $state[ 'prefix' ] . '.' : '' );
            }
        } else {
            $this->generateState();
        }
    }

    /**
     * @param string $prefix
     */
    protected function generateState( $prefix = '' )
    {
        $generated = 0;
        foreach( $this->views as $view ) {
            if( ! empty( $this->console->only ) ) {
                if( in_array( get_class( $view ), $this->console->only ) ) {
                    $path = $this->createFileIfNotExists( $view->path( $prefix ) );
                    $this->saveToFile( $view->render(), $path );
                    $this->console->info( "{$path} generated..." );
                    $generated++;
                }
            } else {
                $path = $this->createFileIfNotExists( $view->path( $prefix ) );
                $this->saveToFile( $view->render(), $path );
                $this->console->info( "{$path} generated..." );
                $generated++;
            }
        }
        $message = $generated ? ( $generated > 1 ? $generated . ' views generated' : '1 view generated!' ) : 'no views generated.';
        $this->console->info( $message );
    }

    /**
     * @param $dotNotationPath
     * @return string
     */
    protected function createFileIfNotExists( $dotNotationPath )
    {
        $segments = explode( '.', $dotNotationPath );
        $path = resource_path( "views" );
        foreach( $segments as $key => $segment ) {
            $path .= DIRECTORY_SEPARATOR . $segment;
            if( ! isset( $segments[ $key + 1 ] ) ) {
                $path .= '.blade.php';
                touch( $path );
            } else {
                if( ! file_exists( $path ) ) {
                    mkdir( $path, 0755, 'R' );
                }
            }
        }
        return $path;
    }

    /**
     * @param $contents
     * @param $path
     */
    protected function saveToFile( $contents, $path )
    {
        $beautifier = new BeautifyHtml( [
            'indent_inner_html' => false,
            'indent_char' => "\t",
            'indent_size' => 1,
            'wrap_line_length' => 32786,
            'unformatted' => ['code', 'pre'],
            'preserve_newlines' => false,
            'max_preserve_newlines' => 32786,
            'indent_scripts'	=> 'normal'
        ] );

        file_put_contents( $path, str_replace( [ '@extends(', '@section(', '@endsection' ], [ "\n@extends(", "\n@section(", "\n@endsection" ], $beautifier->beautify( $contents ) ) );

    }

    /**
     * @return array
     */
    public function regions()
    {
        $regions = [];
        foreach( $this->views as $view ) {
            $regions = array_merge( $regions, array_keys( $view->regions() ) );
        }
        return $regions;
    }

    /**
     * @return array
     */
    public function regionElements()
    {
        $elements = [];
        foreach( $this->views as $view ) {
            foreach( $view->regions() as $region ) {
                $elements += $region->elements();
            }
        }
        return $elements;
    }

    /**
     * @return array
     */
    public function views()
    {
        $views = [];
        foreach( $this->views as $view ) {
            $views[] = get_class( $view );
        }
        return $views;
    }

    /**
     * @param $region
     * @return mixed
     */
    public function getViewForRegion( $region )
    {
        foreach( $this->views as $view ) {
            foreach( $view->regions() as $regionName => $regionObject ) {
                if( $regionName === $region ) {
                    return $view;
                }
            }
        }
    }

    /**
     * @return void
     */
    public function viewComposer()
    {
        view()->composer( '*', function( $view ) {
            $dotNotationPath = ltrim( str_replace( '/', '.', preg_replace( '/\.(?:blade\.)php$/', '', str_replace( resource_path( 'views' ), '', $view->getPath() ) ) ), '.' );
            if( $viewObject = app( RegisterBlueprint::class )->getViewByPath( $dotNotationPath ) ) {
                $viewObject->setViewData( $view->getData() );
                $data = [];
                foreach( $viewObject->elements() as $element ) {
                    if( method_exists( $element, 'active' ) ) {
                        if( ! $element->active() ) {
                            continue;
                        }
                    }
                    $element->displaying();
                    $data[ $element->valueKeyName() ] = old( $element->name() ) ? old( $element->name() ) : $element->value();
                    if( method_exists( $element, 'statusKeyName' ) ) {
                        $data[ $element->statusKeyName() ] = old( $element->name() ) ? 'checked' : ( $element->status() ? 'checked' : '' );
                    }
                }
                $view->with( $data );
                event( 'laraview:view-composer', [ compact( 'view', 'viewObject' ) ] );
            }
        });
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getViewByPath( $path )
    {
        foreach( $this->views as $view ) {
            if( $path === $view->path() ) {
                return $view;
            }
        }
    }

    /**
     * @param $viewClass
     * @param $model
     * @param $request
     * @param null $segment
     */
    public function dispatchPayload( $viewClass, $model, $request, $segment = null )
    {
        tap( (new $viewClass), function( BaseView $view ) use( $model, $request, $segment ) {
            $elements = is_null( $segment ) ? $view->elementsNotAssignedToASegment() : $view->elementsFromSegment( $segment );
            foreach( $elements as $element ) {
                if( method_exists( $element, 'active' ) ) {
                    if( ! $element->active() ) {
                        continue;
                    }
                }
                $element->receivePayload( $model, $request );
            }
        });
    }

    /**
     * @param $viewClass
     * @param $request
     * @return array
     */
    public function getValidation( $viewClass, $request )
    {
        $view = new $viewClass;
        $rules = [];
        $messages = [];
        $elements = $request->getSegment() ? $view->elementsFromSegment( $request->getSegment() ) : $view->elementsNotAssignedToASegment();

        foreach( $elements as $element ) {
            if( method_exists( $element, 'active' ) ) {
                if( ! $element->active() ) {
                    continue;
                }
            }
            $element->beforeValidation( $request );
            $data = $element->getValidationData();
            $rules[ $data[ 'name' ] ] = $data[ 'rules' ];
            $messages = array_merge( $messages, $data[ 'messages' ] );
        }
        return compact( 'rules', 'messages' );
    }

}
