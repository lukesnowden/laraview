<?php

namespace Laraview\Libs\Demo;

use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Text;

class ForenameTextElement extends Text implements ElementBlueprint
{

    /**
     * @var string
     */
    protected $name = 'forename';

    /**
     * @var string
     */
    protected $label = 'Forename';

    /**
     * @var array
     */
    protected $attributes = [ 'placeholder' => 'John...' ];


}
