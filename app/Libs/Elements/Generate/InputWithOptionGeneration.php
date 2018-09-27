<?php

namespace Laraview\Libs\Elements\Generate;

class InputWithOptionGeneration extends InputGeneration
{

    /**
     * @return mixed
     */
    public function create()
    {
        $name = $this->askForNameOfElement();
        $label = $this->askForElementsLabelText();
        $attributes = $this->askForElementsAttributes();
        $options = $this->askForOptions();

        $element = $this->createTempElement( $name );
        $contents = $this->getContents( $element, $label, $attributes, $options );
        $this->createFolder( $element->folder );

        file_put_contents( $element->fileName, $contents );

        $this->importElementToRegion( $element );

        return $element->fileName;

    }

    /**
     * @param $element
     * @param $label
     * @param array $attributes
     * @param array $options
     * @return mixed
     */
    public function getContents( $element, $label, $attributes = [], $options = [] )
    {
        return str_replace(
            [
                '[NAMESPACE]',
                '[CLASS_NAME]',
                '[NAME]',
                '[LABEL]',
                '[ATTRIBUTES]',
                '[OPTIONS]'
            ],
            [
                $element->namespaceWithoutClassName,
                $element->className,
                $element->inputName,
                $label,
                $this->stringifyAsArray( $attributes ),
                $this->stringifyAsArray( $options ),
            ],
            file_get_contents( $this->stubPath )
        );
    }

    /**
     * @return array|mixed
     */
    private function askForOptions()
    {
        return $this->keyPair(
            $this->console->ask( "Please enter options in key:value format separated with a comma (a:Apple,b:Pear)" )
        );
    }

}
