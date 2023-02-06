<?php

namespace Beer\Form;
use Laminas\Form\Element;

class BeerForm extends \Laminas\Form\Form
{
    public function __construct($name = 'beer', $categoryList = [], $styleList = [], $breweryList = [])
    {
        parent::__construct($name);
        $this->add([
            'name' => 'id',
            'type' => 'hidden'
        ]);
        $this->add([
            'type' => Element\Csrf::class,
            'name' => 'beer_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600,
                ],
            ],
        ]);
        $this->add([
            'name' => 'name',
            'type' => 'text',
            'options' => [
                'label' => 'Beer Name'
            ]
        ]);
        
        $categoryDropDownVals = [];
        foreach($categoryList as $category) {
           $categoryDropDownVals[$category['id']] = $category['name'];
        }

        $this->add([
            'name' => 'cat_id',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Category',
                'empty_option' => 'Please select category',
                'value_options' => $categoryDropDownVals
            ],
            'attributes' => [
                'required' => true
            ],
        ]);
        
        $styleDropDownVals = [];
        foreach($styleList as $style) {
           $styleDropDownVals[$style['id']] = $style['name'];
        }

        $this->add([
            'name' => 'style_id',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Style',
                'empty_option' => 'Please select style',
                'value_options' => $styleDropDownVals
            ],
            'attributes' => [
                'required' => true
            ],
        ]);
        
        $breweryDropDownVals = [];
        foreach($breweryList as $brewery) {
           $breweryDropDownVals[$brewery['id']] = $brewery['name'];
        }

        $this->add([
            'name' => 'brewery_id',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Brewery',
                'empty_option' => 'Please select brewery',
                'value_options' => $breweryDropDownVals
            ],
            'attributes' => [
                'required' => true
            ],
        ]);

        $this->add([
            'name' => 'abv',
            'type' => 'text',
            'options' => [
                'label' => 'ABV'
            ]
        ]);

        $this->add([
            'name' => 'ibu',
            'type' => 'text',
            'options' => [
                'label' => 'IBU'
            ]
        ]);

        $this->add([
            'name' => 'srm',
            'type' => Element\Number::class,
            'options' => [
                'label' => 'SRM'
            ]
        ]);

        $this->add([
            'name' => 'upc',
            'type' => Element\Number::class,
            'options' => [
                'label' => 'UPC'
            ]
        ]);

        $this->add([
            'name' => 'filepath',
            'type' => Element\File::class,
            'options' => [
                'label' => 'Photo'
            ]
        ]);
        
        $this->add([
            'name' => 'descript',
            'type' => 'textarea',
            'options' => [
                'label' => 'Description'
            ],
            'attributes' => [
                'required' => false,
                'class' => 'ckeditor',
                'style' => 'width:100%'
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
