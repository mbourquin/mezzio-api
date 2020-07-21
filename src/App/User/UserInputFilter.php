<?php
declare(strict_types=1);

namespace App\User;

use Laminas\Filter\StringTrim;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\StringLength;

class UserInputFilter extends InputFilter
{
    public function init()
    {
        $this->add([
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class]
            ],
            'validators' => [
                ['name' => EmailAddress::class]
            ]
        ]);
        $this->add([
            'name' => 'password',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => ['min' => 8],
                ]
            ]
        ]);
        $this->add([
            'name' => 'name',
            'filters' => [
                ['name' => StringTrim::class],
            ]
        ]);
    }
}
