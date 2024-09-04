# Nova Subfield DependsOn

This [Laravel Nova](https://nova.laravel.com/) package includes an interface and middleware to allow fields that contain subfields (such as Dependable Panel, or Flexible Content) to allow those subfields to use the `dependsOn` functionality within Laravel Nova 4.

As long as all fields that contain subfields implement the interface methods any subfield could be nested within any field to any depth.

## Requirements

- `php: >=8.0`
- `laravel/nova: ^4.0`

## Installation

1) Install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require formfeed-uk/nova-subfield-dependson
```

## Usage

Simply implement the included interface onto any Field you wish to allow subfields to use the dependsOn functionality

#### hasSubFields

```php
    public function hasSubfields(): bool;
```

This method should return a boolean value to indicate whether the current instance of your field has subfields that require the dependsOn functionality.

#### getSubfields

```php
    public function getSubfields(): FieldCollection;
```

This method should return a `FieldCollection` of the subfields that require the dependsOn functionality

#### afterDependsOnSync

```php
    public function afterDependsOnSync() : self;
```

This method is run after any dependsOn sync where additional logic may be required. This may simply `return $this;` where no additional logic is required.


## Known Issues

Due to how Nova's DependantComponentKey works If a field uses the same Class, Component, and Attribute as another subfield within the same request this could cause a collision, where the wrong field is found. An upcoming release will include a macro for `Laravel\Nova\Fields\Field` to allow you to set your own DependantComponentKey method.