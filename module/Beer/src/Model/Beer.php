<?php

namespace Beer\Model;

use DomainException;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToFloat;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\I18n\Validator\IsInt;
use Laminas\I18n\Validator\IsFloat;
use Laminas\Validator\StringLength;

class Beer implements InputFilterAwareInterface
{
    public $id;
    public $name;
    public $brewery_id;
    public $cat_id;
    public $style_id;
    public $abv;
    public $ibu;
    public $srm;
    public $upc;
    public $filepath;
    public $descript;
    public $add_user;
    public $last_mod;
    public $category_name;
    public $style_name;
    public $brewery_name;

    // Add this property:
    private $inputFilter;
    
    public function exchangeArray(array $data)
    {
        $this->id     = !empty($data['id']) ? $data['id'] : null;
        $this->name = !empty($data['name']) ? $data['name'] : null;
        $this->brewery_id = !empty($data['brewery_id']) ? $data['brewery_id'] : null;
        $this->cat_id = !empty($data['cat_id']) ? $data['cat_id'] : null;
        $this->style_id = !empty($data['style_id']) ? $data['style_id'] : null;
        $this->abv = !empty($data['abv']) ? $data['abv'] : null;
        $this->ibu = !empty($data['ibu']) ? $data['ibu'] : null;
        $this->srm = !empty($data['srm']) ? $data['srm'] : null;
        $this->upc = !empty($data['upc']) ? $data['upc'] : null;
        $this->filepath = !empty($data['filepath']) ? $data['filepath'] : null;
        $this->descript = !empty($data['descript']) ? $data['descript'] : null;
        $this->add_user = !empty($data['add_user']) ? $data['add_user'] : null;
        $this->last_mod  = !empty($data['last_mod']) ? $data['last_mod'] : null;
        
        $this->category_name  = !empty($data['category_name']) ? $data['category_name'] : null;
        $this->style_name  = !empty($data['style_name']) ? $data['style_name'] : null;
        $this->brewery_name  = !empty($data['brewery_name']) ? $data['brewery_name'] : null;
    }
    
    public function getArrayCopy()
    {
        return [
            'id'     => $this->id,
            'name' => $this->name,
            'brewery_id'  => $this->brewery_id,
            'cat_id'  => $this->cat_id,
            'style_id'  => $this->style_id,
            'abv'  => $this->abv,
            'ibu'  => $this->ibu,
            'srm'  => $this->srm,
            'upc'  => $this->upc,
            'filepath'  => $this->filepath,
            'descript'  => $this->descript,
            'add_user'  => $this->add_user,
            'last_mod'  => $this->last_mod,
        ];
    }
    
    /* Add the following methods: */

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'brewery_id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => IsInt::class,
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'cat_id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => IsInt::class,
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'style_id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => IsInt::class,
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'name',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 255,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'abv',
            'required' => true,
            'filters' => [
                ['name' => ToFloat::class],
            ],
            'validators' => [
                [
                    'name' => IsFloat::class,
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'ibu',
            'required' => true,
            'filters' => [
                ['name' => ToFloat::class],
            ],
            'validators' => [
                [
                    'name' => IsFloat::class,
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'srm',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => IsInt::class,
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'upc',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => IsInt::class,
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'filepath',
            'required' => false,
            'validators' => [
                ['name'    => 'FileUploadFile'],
                [
                    'name'    => 'FileMimeType',                        
                    'options' => [                            
                        'mimeType'  => ['image/jpeg', 'image/png']
                    ]
                ],
                ['name'    => 'FileIsImage'],
                [
                    'name'    => 'FileImageSize',
                    'options' => [
                        'minWidth'  => 128,
                        'minHeight' => 128,
                        'maxWidth'  => 4096,
                        'maxHeight' => 4096
                    ]
                ],
            ],
            'filters'  => [                    
                [
                    'name' => 'FileRenameUpload',
                    'options' => [  
                        'target' => './public/img/uploads',
                        'useUploadName' => true,
                        'useUploadExtension' => true,
                        'overwrite' => true,
                        'randomize' => false
                    ]
                ]
            ],   
        ]);

        $inputFilter->add([
            'name' => 'descript',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                    ],
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}