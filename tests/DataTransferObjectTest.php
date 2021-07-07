<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Tests\Dummy\BasicDto;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDto;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithCastedAttributeHavingCast;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithNullableProperty;
use Spatie\DataTransferObject\Tests\Dummy\ComplexCamelCaseDto;
use Spatie\DataTransferObject\Tests\Dummy\ComplexSnakeCaseDto;
use Spatie\DataTransferObject\Tests\Dummy\WithDefaultValueDto;

class DataTransferObjectTest extends TestCase
{
    /** @test */
    public function array_of()
    {
        $list = BasicDto::arrayOf([
            ['name' => 'a'],
            ['name' => 'b'],
        ]);

        $this->assertCount(2, $list);

        $this->assertEquals('a', $list[0]->name);
        $this->assertEquals('b', $list[1]->name);
    }

    /** @test */
    public function create_with_nested_dto()
    {
        $dto = new ComplexDto([
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertEquals('b', $dto->other->name);
    }

    /** @test */
    public function create_with_nested_dto_already_casted()
    {
        $dto = new ComplexDto([
            'name' => 'a',
            'other' => new BasicDto([
                'name' => 'b',
            ]),
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertEquals('b', $dto->other->name);
    }

    /** @test */
    public function create_with_null_nullable_nested_dto()
    {
        $dto = new ComplexDtoWithNullableProperty([
            'name' => 'a',
            'other' => null,
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertNull($dto->other);
    }

    /** @test */
    public function create_with_nested_dto_having_cast()
    {
        $dto = new ComplexDtoWithCastedAttributeHavingCast([
            'name' => 'a',
            'other' => [
                'name' => 'b',
                'object' => [
                    'name' => 'c',
                ],
            ],
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertEquals('b', $dto->other->name);
        $this->assertEquals('c', $dto->other->object->name);
    }

    /** @test */
    public function all_with_nested_dto()
    {
        $array = [
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ];

        $dto = new ComplexDto($array);

        $all = $dto->all();

        $this->assertCount(2, $all);
        $this->assertEquals('a', $all['name']);
        $this->assertEquals('b', $all['other']->name);
    }

    /** @test */
    public function to_array_with_nested_dto()
    {
        $array = [
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ];

        $dto = new ComplexDto($array);

        $this->assertEquals($array, $dto->toArray());
    }

    /** @test */
    public function to_array_with_only()
    {
        $array = [
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ];

        $dto = new ComplexDto($array);

        $this->assertEquals(['name' => 'a'], $dto->only('name')->toArray());
    }

    /** @test */
    public function to_array_with_except()
    {
        $array = [
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ];

        $dto = new ComplexDto($array);

        $this->assertEquals(['other' => ['name' => 'b']], $dto->except('name')->toArray());
    }

    /** @test */
    public function to_array_snake_case_with_nested_dto()
    {
        $array = [
            'namePersonal' => 'a',
            'otherField' => [
                'nameField' => 'b',
            ],
        ];

        $arraySnakeCase = [
            'name_personal' => 'a',
             'other_field' => [
                'name_field' => 'b',
            ],
        ];

        $dto = new ComplexCamelCaseDto($array);

        $this->assertEquals($arraySnakeCase, $dto->toArraySnakeCase());
    }

    /** @test */
    public function to_array_camel_case_with_nested_dto()
    {
        $array = [
            'name_personal' => 'a',
            'other_field' => [
                'name_field' => 'b',
            ],
        ];

        $arrayCamelCase = [
            'namePersonal' => 'a',
             'otherField' => [
                'nameField' => 'b',
            ],
        ];

        $dto = new ComplexSnakeCaseDto($array);

        $this->assertEquals($arrayCamelCase, $dto->toArrayCamelCase());
    }

    /** @test */
    public function to_array_studly_case_with_nested_dto()
    {
        $array = [
            'name_personal' => 'a',
            'other_field' => [
                'name_field' => 'b',
            ],
        ];

        $arrayStudlyCase = [
            'NamePersonal' => 'a',
             'OtherField' => [
                'NameField' => 'b',
            ],
        ];

        $dto = new ComplexSnakeCaseDto($array);

        $this->assertEquals($arrayStudlyCase, $dto->toArrayStudlyCase());
    }

    /** @test */
    public function create_with_default_value()
    {
        $dto = new WithDefaultValueDto();

        $this->assertEquals(['name' => 'John'], $dto->toArray());
    }

    /** @test */
    public function create_with_overriden_default_value()
    {
        $dto = new WithDefaultValueDto(name: 'Doe');

        $this->assertEquals(['name' => 'Doe'], $dto->toArray());
    }

    /** @test */
    public function test_clone()
    {
        $array = [
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ];

        $dto = new ComplexDto($array);

        $clone = $dto->clone(other: ['name' => 'a']);

        $this->assertEquals('a', $clone->name);
        $this->assertEquals('a', $clone->other->name);
    }
}
