<?php

namespace Laraview\Libs\Demo\Event;

use Laraview\Libs\Blueprints\ElementBlueprint;
use Laraview\Libs\Elements\Text;

class SurnameTextElement extends Text implements ElementBlueprint
{

    /**
     * @var string
     */
    protected $name = 'surname';

    /**
     * @var string
     */
    protected $label = 'Surname';

    /**
     * @var array
     */
    protected $attributes = [ 'placeholder' => 'Doe...' ];

}
