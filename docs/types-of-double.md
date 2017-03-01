# Types of Double

- [Introduction](#introduction)
- [Basic Doubles](#basic-doubles)
    - [Spying Doubles](#spying-doubles)
- [Named Doubles](#named-doubles)
- [Partial Doubles](#partial-doubles)
    - [Runtime Partial Doubles](#runtime-partial-doubles)
    - [Generated Partial Doubles ⚠️️  ](#generated-partial-doubles)
    - [Proxied Partial Doubles ☠️ ](#proxied-partial-doubles)
- [Instance Doubles ☠️ ](#instance-doubles)

<a name="introduction"></a>
## Introduction

This part of the documentation will talk about types of test double (mocks) and
how you go about configuring them with Mockery.

Some of the options available to you have been around for a long time and we
plan to continue supporting them, but wouldn't necessarily recommend you use
them.

<a name="basic-doubles">
## Basic Doubles

To create a basic double, we use the `Mockery::mock` method. The object Mockery
returns can have stubs and expectations set for all of it's methods.

``` php
$double = Mockery::mock();
$double->allows()
    ->getBar(123)
    ->andReturns('bar');

$double->expects()
    ->doSomething();
```

The example above doesn't pass anything to the `mock` method, so is returned a bare
double. This kind of double won't satisfy any type hints you might have in your
code. If you need a double of a specific type, you need to ask for it.

``` php
interface Dispatcher {} 
$dispatcherDouble = Mockery::mock(Dispatcher::class);

class Logger {}
$loggerDouble = Mockery::mock(Logger::class);
```

You can pass Mockery as many types as you would like and it will try and
generate a class to satisfy them all.

``` php
$double = Mockery::mock(Logger::class, Dispatcher::class);
```

<a name="spying-doubles">
### Spying Doubles
By default, a basic double will throw an exception if it receives a method call
it has not been told to `allow` or `expect`. If this isn't what you desire, a
basic double can be turned in to a spying double, by calling the
`shouldIgnoreMissing` method. This makes the double return `null` for all method
calls it has not been configured to `allow` or `expect`, allowing the user to
inspect the received calls after it has been used.

``` php
$spy = Mockery::spy(); // shortcut for Mockery::mock()->shouldIgnoreMissing()

$spy->foo();

$spy->shouldHaveReceived()
    ->foo();
```

<a name="named-doubles">
## Named Doubles

Named doubles act in the same way as [Basic Doubles](#basic-doubles), but are
configured so that the generated class has a specific name. 

``` php
$double = Mockery::namedMock('MockDispatcher', Dispatcher::class);
get_class($double); // string("MockDispatcher")
```

This can be useful if your code or tests require that the class name is
predictable.

<a name="partial-doubles">
## Partial Doubles

Partial doubles are useful when you want to stub out, set expectations for, or
spy on *some* methods of a class, but run the actual code for other methods.

<a name="runtime-partial-doubles">
### Runtime Partial Doubles

What we call a runtime partial, involves creating a double and then telling it
to make itself partial. Any method calls that the double hasn't been told to
allow or expect, will act as they would on a normal instance of the object.

``` php
class Foo {
    function bar() { return 123; }
    function baz() { return $this->bar(); }
}

$foo = mock(Foo::class)->makePartial();
$foo->bar(); // int(123);
```

We can then tell the mock to allow or expect calls as with any other mockery
double.

``` php
$foo->allows()->bar()->andReturns(456);
$foo->baz(); // int(456)
```

<a name="generated-partial-doubles">
### Generated Partial Doubles ⚠️️ 

> Note: Generated Partial Doubles are supported, but we try to avoid using them.

The second type of partial double we can create is what we call a generated
partial. With generated partials, you specifically tell mockery which methods
you want to be able to allow or expect calls to. All other methods will run the
actual code *directly*, so stubs and expectations on these methods will not
work.

``` php
class Foo {
    function bar() { return 123; }
    function baz() { return $this->bar(); }
}

$foo = mock("Foo[bar]");

$foo->bar(); // error, no expectation set

$foo->allows()->bar()->andReturns(456);
$foo->bar(); // int(456)

// setting an expectation for this has no effect
$foo->allows()->baz()->andReturns(999);
$foo->baz(); // int(456)
```

<a name="proxied-partial-doubles">
### Proxied Partial Doubles ☠️ 

> Note: Proxied partial doubles are supported, but are not recommended.

<a name="instance-doubles">
## Instance Doubles ☠️ 

> Note: Instance doubles are supported, but are not recommended.

