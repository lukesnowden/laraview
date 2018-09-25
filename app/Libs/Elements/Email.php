<?php

namespace Laraview\Libs\Elements;

use Laraview\Libs\Blueprints\ElementBlueprint;

abstract class Email extends Text implements ElementBlueprint
{

    /**
     * @return string
     */
    protected function element()
    {
        return sprintf( '<input type="email" name="%s" %s value="%s" />',
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
        return 'Email Element';
    }

}
