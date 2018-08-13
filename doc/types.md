Types
=====

PHP (as of 5.6) has three kinds of types; classes, interfaces and traits.
The usual way of using types is by creating and accessing instance objects.
However, there are a lot of situations where it's useful to refer to the
types themselves.

The usual way to refer to types in PHP is to use the fully-qualified class
name (FQCN), which is a string that uniquely identifies a type. Unfortunately,
this string then has to be verified anytime it's used because a string can
contain any value, and it's easy to make a typo. The `::class` magic property
which resolves to the type of a class at compile time mitigates this somewhat,
but if you define a function that takes a FQCN as a parameter, it's still
possible to pass any string in there, so your function will need to use 
some validation such as `class_exists`.

The `SolidPhp\ValueObjects\Type` namespace contains type value objects that
make it easier to deal with type parameters. You can now write

```php
function doSomething(Type $myType) {
    ...
}
```



```php
function doSomethingWithInterface(InterfaceType $myInterface) {
    ...
}
```