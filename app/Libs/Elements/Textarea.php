<?php

namespace Laraview\Libs\Elements;

use Laraview\Libs\Blueprints\ElementBlueprint;

abstract class Textarea extends Text implements ElementBlueprint
{

    /**
     * @return string
     */
    protected function element()
    {
        return sprintf( '<textarea type="text" name="%s" %s>%s</textarea>',
            $this->name,
            $this->attributes(),
            "{{ \${$this->valueKeyName()} }}"
        );
    }

    /**
     * @return mixed|null|string
     */
    public static function humanReadableName()
    {
        return 'Textarea Element';
    }

}
