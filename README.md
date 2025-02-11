# Schema Converter

[![CI](https://github.com/instapro/schema-converter/actions/workflows/ci.yml/badge.svg)](https://github.com/instapro/schema-converter/actions/workflows/ci.yml)
[![Packagist Version](https://img.shields.io/packagist/v/instapro/schema-converter)](https://packagist.org/packages/instapro/schema-converter)
[![PHP Version](https://img.shields.io/packagist/php-v/instapro/schema-converter)](https://packagist.org/packages/instapro/schema-converter)
[![License](https://img.shields.io/github/license/instapro/schema-converter)](https://github.com/instapro/schema-converter/blob/main/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/instapro/schema-converter)](https://packagist.org/packages/instapro/schema-converter)

## Overview

This library provides functionality to convert PHP types into schemas and cast values based on types.

## Installation

You can install the library via Composer:

```bash
composer require instapro/schema-converter
```

## Usage

There are several schema converters that can be used to convert PHP types into schemas. Bellow an example using the most basic converter which can be used to convert primitive types:

```php
$converter = new \Instapro\SchemaConverter\PrimitiveConverter();

echo json_encode($converter->toSchema('string')->toArray()); // {"type": "string"}
```

You can also cast values based on the schema:

```php
$converter = new \Instapro\SchemaConverter\PrimitiveConverter();

var_dump($converter->castValue('string', 12.3)); // string(4) "12.3"
```

## Supported converters

### `PrimitiveConverter`

The primitive converter can be used to convert primitive types into schemas. Primitives can be `array`, `string`, `int`, `float`, `bool`, `mixed`, or `null`.

You can see examples of how to use the primitive converter in the previous section.

### `ObjectConverter`

The object converter can be used to convert objects into schemas. The converter will recursively convert the properties of the object into schemas, but it will require you to define a converter to deal with that recursively.

For the following example, we'll let the converter handle the following class:

```php
final readonly class Person
{
    public function __construct(
        public string $name,
        public int $age,
    ) {}
}
```

You can use the converter to get a schema of any class:

```php
$converter = new \Instapro\SchemaConverter\ObjectConverter();
$converter->bindConverter(new \Instapro\SchemaConverter\PrimitiveConverter());

echo json_encode($converter->toSchema(Person::class)->toArray(), JSON_PRETTY_PRINT);
```

The code above will output the following schema:

```json
{
  "type": "object",
  "parameters": {
    "name": {
      "required": true,
      "type": "string"
    },
    "age": {
      "required": true,
      "type": "int"
    }
  }
}
```

You can also cast values based on a type, as long you use the same format as the schema:

```php
$converter = new \Instapro\SchemaConverter\ObjectConverter();
$converter->bindConverter(new \Instapro\SchemaConverter\PrimitiveConverter());

$person = $converter->castValue(Person::class, [
    'name' => 'John Doe',
    'age' => 30,
]);

echo $person->name; // John Doe
```

**Disclosure**: Note that this converter has a `bindConverter` instead of a constructor argument. This was designed to help with recursive conversions.

### `EntityConverter`

The entity converter can be used to convert Doctrine entities into schemas.

```php
$converter = new \Instapro\SchemaConverter\EntityConverter($entityManager);

echo json_encode($converter->toSchema(MyEntity::class)->toArray()); // {"type": "identifier"}   
echo $converter->castValue(MyEntity::class, 123)->id; // 123   
```

### `DateTimeConverter`

The `DateTimeConverter` can handle objects that implement `DateTimeIterface`.

```php
$converter = new \Instapro\SchemaConverter\DateTimeConverter();

echo json_encode($converter->toSchema(\DateTime::class)->toArray()); // {"type": "datetime"}
echo PHP_EOL;

$dateTime = $converter->castValue(\DateTime::class, '2021-01-01T00:00:00Z');
echo $dateTime->format('Y-m-d'); // 2021-01-01
```

### `CompositeConverter`

There are several types of schemas that can be generated. For convenience, the library provides a `CompositeConverter` that can be used to convert multiple types of schemas.

```php
$converter = new \Instapro\SchemaConverter\CompositeConverter(
    new \Instapro\SchemaConverter\PrimitiveConverter(),
    new \Instapro\SchemaConverter\DateTimeConverter(),
    new \Instapro\SchemaConverter\ObjectConverter(),
    new \Instapro\SchemaConverter\EntityConverter($entityManager);
);

echo json_encode($converter->toSchema(SomethingComplex::class)->toArray()); // {"type": "object", "parameters": {...}}

$somethingComplex = $converter->castValue(SomethingComplex::class, $complextData);
$somethingComplex->doSomething();
```

**Note**: The `CompositeConverter` will automatically call `setConverter()` for the `ObjectConverter` with itself.   
