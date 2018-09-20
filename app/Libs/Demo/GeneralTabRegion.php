<?php

namespace Laraview\Libs\Demo;

use Laraview\Libs\Blueprints\RegionBlueprint;
use Laraview\Libs\BaseRegion;

class GeneralTabRegion extends BaseRegion implements RegionBlueprint
{

    /**
     * @var string
     */
    protected $placeholder = '[GENERAL_TAB_REGION]';

    /**
     * @var array
     */
    protected $elements = [
        ForenameTextElement::class,
        TitleSelectElement::class
    ];

}
