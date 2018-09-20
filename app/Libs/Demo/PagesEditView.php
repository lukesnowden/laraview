<?php

namespace Laraview\Libs\Demo;

use Laraview\Libs\Blueprints\ViewBlueprint;
use Laraview\Libs\BaseView;

class PagesEditView extends BaseView implements ViewBlueprint
{

    /**
     * @var string
     */
    protected $path = 'front-pages.edit';

    /**
     * @var string
     */
    protected $baseViewPath = 'view.blade.php';

    /**
     * @var array
     */
    protected $regions = [
        GeneralTabRegion::class
    ];

}
