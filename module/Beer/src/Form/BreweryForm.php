<?php

namespace Beer\Form;

class BreweryForm extends \Laminas\Form\Form
{
    public function __construct($name = 'brewery')
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
                'label' => 'Brewery Name'
            ]
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Save',
                'id' => 'saveBreweryForm'
            ]
        ]);
        //by default it’s also POST
        $this->setAttribute('method', 'POST');
    }
}
