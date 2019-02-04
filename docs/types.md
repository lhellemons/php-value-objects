Types
=====

PHP (as of 5.6) has three kinds of types; classes, interfaces and traits.
The usual way of using types is by creating and accessing instance objects.
However, there are a lot of situations where it's useful to refer to the
types themselves.

The usual way to refer to types in PHP is to use the fully-qualified class
 name (FQCN), which is a string that uniquely identifies a type.
Unfortunately, this string then has to be verified anytime it's used
 because a string can contain any value, and it's easy to make a typo.
 The `::class` magic property which resolves to the type of a classe at
 compile time mitigates this somewhat, but if you define a function that
 takes a FQCN as a parameter, it's still possible to pass any string to
 it, so your function will need to use some validation such as
 `class_exists`.

The `SolidPhp\ValueObjects\Type` namespace contains type value objects
 that make it easier to deal with type parameters. You can now write

```php
use SolidPhp\ValueObjects\Type\Type;
use SolidPhp\ValueObjects\Type\ClassType;
use SolidPhp\ValueObjects\Type\InterfaceType;

class MyClass {}

// Type objects can be constructed from the FQCN
$myClassType = Type::of(MyClass::class);

// You'll get back the correct kind of specific type object
$myClassType instanceof Type; // true
$myClassType instanceof ClassType; // true
$myClassType instanceof InterfaceType; // false

doSomethingWithAType($myClassType);

// Type objects can also be constructed from an instance
$myInstance = new MyClass();
$myInstanceType = Type::of($myInstance);

$myInstanceType === $myClassType; // true

// constructing a Type only works if the underlying class/interface/trait
// actually exists
$myNonExistingClassType = Type::of('NonExistingClass'); // InvalidArgumentException

// Type objects contain some useful methods for analyzing types and
// their relations
function doSomethingWithAType(Type $myType) {
    // $myType must refer to a class, interface or trait that actually
    // exists.
    echo $myType->getKind(); // this is the 'kind' of type (class, interface or trait)
    echo $myType->getFullyQualifiedName(); // this is the FQCN string
    
    if ($myType->isSubtypeOf(Type::of(MyClass::class))) {
        echo sprintf('Type %s extends, implements or uses %s', $myType, Type::of(MyClass::class));
    }
}
 
```

`Kind` is an enum with the following values:
- `Kind::CLASS()` - for regular classes (whether concrete or abstract)
- `Kind::INTERFACE()` - for interfaces
- `Kind::TRAIT()` - for traits

See the manual page on [enums](enums.md) for instructions on how to
work with enums.

```php
use SolidPhp\ValueObjects\Type\InterfaceType;

function doSomethingWithInterface(InterfaceType $myInterface) {
    // $myInterface will refer to an interface
}
```

Type objects contain methods that are useful for analyzing the
relation between types, and the `ClassType` in particular has
utility methods for getting the `ReflectionClass` and calling static methods.

```php
use SolidPhp\ValueObjects\Type\Type;

function compareTypes(Type $A, Type $B) {
    if ($A->isSubtypeOf($B)) {
        echo "$A extends, implements or uses $B!";
    }
    if ($A->isSupertypeOf($B)) {
        echo "$B extends, implements or uses $A!";
    }
}

class TypedCollection {
    /** @var Type */
    private $elementType;
    
    private $elements = [];
    
    public function __construct(Type $elementType, array $initialElements = []) {
        $this->elementType = $elementType;
        $this->addElements($initialElements);
    }
    
    public function addElements(array $elements): void {
        foreach ($elements as $element) {
            $this->addElement($element);
        }
    }
    
    public function addElement($element): void {
        if (!$this->elementType->isInstance($element)) {
            throw new InvalidArgumentException(sprintf('This collection can only contain elements of %s', $this->elementType));
        }
        
        $this->elements[] = $element;
    }
    
    public function getElements(): array {
        return $this->elements;
    }
    
    public function getElementType(): Type {
        return $this->elementType;
    }
}
