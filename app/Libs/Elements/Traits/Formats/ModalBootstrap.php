<?php

namespace Laraview\Libs\Elements\Traits\Formats;

trait ModalBootstrap
{

    /**
     * @return string
     */
    public function render()
    {
        $this->preRender();
        return $this->wrapper(
            $this->modalHeader( $this->name() ),
            $this->modalBody(
                $this->renderElements()
            ),
            $this->modalFooter()
        );
    }

    /**
     * @param $elements
     * @return string
     */
    public function modalBody( $elements )
    {
        return sprintf( '<div class="modal-body">%s</div>', $elements );
    }

    /**
     * @return string
     */
    public function modalFooter()
    {
        return sprintf( '
            <div class="modal-footer">
                %s
                %s
            </div>',
            $this->submitButton(),
            $this->closeButton()
        );
    }

    /**
     * @return string
     */
    protected function submitButton()
    {
        if( $this->action() ) {
            return sprintf( '<button type="submit" class="btn btn-primary">%s</button>', $this->submitButtonText );
        }
    }

    /**
     * @return string
     */
    protected function closeButton()
    {
        return sprintf( '<button type="button" class="btn btn-secondary" data-dismiss="modal">%s</button>', $this->closeButtonText );
    }

    /**
     * @param $heading
     * @return string
     */
    public function modalHeader( $heading )
    {
        return sprintf( '
            <div class="modal-header">
                <h5 class="modal-title">%s</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>',
            $heading
        );
    }

    /**
     * @return string
     */
    public function id()
    {
        return str_slug( $this->name(), '-' ) . '-modal';
    }

    /**
     * @param $header
     * @param $body
     * @param $footer
     * @return string
     */
    protected function wrapper( $header, $body, $footer )
    {
        return sprintf( '
            <div class="modal fade" id="%s" tabindex="-1" role="dialog">
                <div class="modal-dialog %s" role="document">
                    <div class="modal-content">
                        %s
                        %s
                        %s
                        %s
                        %s
                    </div>
                </div>
             </div>',
            $this->id(),
            $this->size(),
            $this->formOpeningTag(),
            $header,
            $body,
            $footer,
            $this->formClosingTag()
        );
    }

    /**
     * @return string
     */
    protected function size()
    {
        if( in_array( $this->size, [ 'small', 'large' ] ) ) {
            return $this->size === 'small' ? 'modal-sm' : 'modal-lg';
        }
    }

    /**
     * @return string
     */
    protected function formOpeningTag()
    {
        if( $action = $this->action() ) {
            return sprintf(
                '<form action="%s" method="%s" class="form">',
                $action,
                $this->method()
            );
        }
    }

    /**
     * @return string
     */
    protected function formClosingTag()
    {
        if( $action = $this->action() ) {
            return '</form>';
        }
    }

    /**
     * @return string
     */
    public function trigger()
    {
        return sprintf( '<a href="#" data-toggle="modal" data-target="#%s">%s</a>', $this->id(), $this->name() );
    }

}
