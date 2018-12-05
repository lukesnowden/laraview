<?php

namespace Laraview\Libs;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{

    /**
     * @var
     */
    protected $view;

    /**
     * @var array
     */
    protected $validation = [
        'rules' => [],
        'messages' => []
    ];

    /**
     * @param $key
     * @return object|string
     */
    public function parameter( $key )
    {
        return $this->route()->parameter( $key );
    }

    /**
     * @return void
     */
    protected function prepareForValidation()
    {
        if( is_string( $this->view ) ) {
            $this->validation = app( Register::class )->getValidation( $this->view, $this );
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function rules()
    {
        return $this->validation[ 'rules' ];
    }

    /**
     * @return array|mixed
     */
    public function messages()
    {
        return $this->validation[ 'messages' ];
    }

}
