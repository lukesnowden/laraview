<?php

namespace Laraview\Libs\Demo;

use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Select;

class TitleSelectElement extends Select implements ElementBlueprint
{

    /**
     * @var string
     */
    protected $name = 'title';

    /**
     * @var string
     */
    protected $label = 'Title';

    /**
     * @var array
     */
    protected $options = [
        'Mr',
        'Mrs',
        'Ms'
    ];


}
