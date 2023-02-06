<?php

namespace Beer\Form;

class StyleForm extends \Laminas\Form\Form
{
    public function __construct($name = 'style')
    {
        parent::__construct($name);
        $this->add([
            'name' => 'id',
            'type' => 'hidden'
        ]);
        $this->add([
            'name' => 'name',
            'type' => 'text',
            'options' => [
                'label' => 'Beer Style Name'
            ]
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Save',
                'id' => 'saveBeerForm'
            ]
        ]);
        //by default it’s also POST
        $this->setAttribute('method', 'POST');
    }
}
