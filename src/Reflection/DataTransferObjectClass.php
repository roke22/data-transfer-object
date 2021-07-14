<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject\Reflection;

use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\Attributes\StrictType;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\ValidationException;

class DataTransferObjectClass
{
    private ReflectionClass $reflectionClass;

    private DataTransferObject $dataTransferObject;

    private bool $isStrict;

    private bool $isStrictType;

    public function __construct(DataTransferObject $dataTransferObject)
    {
        $this->reflectionClass = new ReflectionClass($dataTransferObject);
        $this->dataTransferObject = $dataTransferObject;
    }

    /**
     * @return \Spatie\DataTransferObject\Reflection\DataTransferObjectProperty[]
     */
    public function getProperties(): array
    {
        $publicProperties = array_filter(
            $this->reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC),
            fn (ReflectionProperty $property) => ! $property->isStatic()
        );

        return array_map(
            fn (ReflectionProperty $property) => new DataTransferObjectProperty(
                $this->dataTransferObject,
                $property
            ),
            $publicProperties
        );
    }

    public function validate(): void
    {
        $validationErrors = [];

        foreach ($this->getProperties() as $property) {
            $validators = $property->getValidators();

            foreach ($validators as $validator) {
                $result = $validator->validate($property->getValue());

                if ($result->isValid) {
                    continue;
                }

                $validationErrors[$property->name][] = $result;
            }
        }

        if (count($validationErrors)) {
            throw new ValidationException($this->dataTransferObject, $validationErrors);
        }
    }

    public function isStrict(): bool
    {
        return $this->isStrict ??= ! empty($this->reflectionClass->getAttributes(Strict::class));
    }

    public function isStrictType(): bool
    {
        return $this->isStrictType ??= ! empty($this->reflectionClass->getAttributes(StrictType::class));
    }
}
