# Variables

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Code Quality][ico-quality]][link-scrutinizer]
[![Code Coverage][ico-coverage]][link-scrutinizer]
[![Mutation testing badge][ico-mutation]][link-mutator]
[![Total Downloads][ico-downloads]][link-downloads]

[![Email][ico-email]][link-email]

The Setherator Variable management library. Manages and processes variable values with some additional magic.


## Install

Via Composer

```bash
$ composer require setherator/variables
```

## Usage

A compilation of several use cases of `Variables` and it's helpers.

```php
use Setherator\Variables\Variables;
use function Setherator\Variables\all;
use function Setherator\Variables\context;
use function Setherator\Variables\factory;
use function Setherator\Variables\logic;
use function Setherator\Variables\passthrough;
use function Setherator\Variables\ref;

$variables = new Variables();
$variables->setContext(['Prefix: ']);

$globalLogicState  = true;
$providedVariables = [
    'scalar'              => 'foo',
    // will execute closure and catch value
    'closure'             => fn() => 'closure Foo ' . random_int(1, 10),
    // Will be fetched at code load
    'ref'                 => ref('scalar', 'Not found'),
    // Will be only fetched when 'ref_on_get' is fetched
    'ref_on_get'          => fn() => ref('scalar'),
    // will return number increasing every time its fetched and 'Number: ' will be passed as argument to
    'factory'             => factory(
        function ($prefix) {
            static $i = 0;

            return $prefix . $i++;
        },
        function () {
            static $i = 0;

            return 'Number ' . $i++ . ': ';
        },
    ),
    // Will inject context values as arguments from Variables::getContext();
    'context'             => context(fn(string $prefix) => $prefix . random_int(1, 10)),
    'factory_context'     => factory(context(fn(string $prefix) => $prefix . random_int(1, 10))),
    'all'                 => all(
        fn() => 'First',
        fn() => 'Second',
        'Third'
    ),
    'logic'               => logic(
        fn() => 1,
        fn() => 'Condition: true',
        fn() => 'Condition: false',
    ),
    'logic_strict'        => logic(
        fn() => 1,
        fn() => 'Condition: true',
        fn() => 'Condition: false',
        true
    ),
    'logic_factory'       => factory(
        logic(
            function () use (&$globalLogicState) {
                return $globalLogicState;
            },
            function () use (&$globalLogicState) {
                $globalLogicState = !$globalLogicState;

                return 'Factory Condition: true';
            },
            function () use (&$globalLogicState) {
                $globalLogicState = !$globalLogicState;

                return 'Factory Condition: false';
            },
        )
    ),
    'passthrough'         => passthrough(
        Closure::fromCallable('strtoupper'),
        fn() => 'Passthrough: ' . ref('logic_factory'),
    ),
    'passthrough_factory' => factory(
        passthrough(
            Closure::fromCallable('strtoupper'),
            fn() => 'Passthrough Factory: ' . ref('logic_factory'),
        )
    ),
];


$variables->add($providedVariables);


echo $variables->get('scalar') . PHP_EOL;
echo $variables->get('closure') . PHP_EOL;
echo $variables->get('closure') . PHP_EOL;
echo $variables->get('ref') . PHP_EOL;
echo $variables->get('ref_on_get') . PHP_EOL;
echo $variables->get('factory') . PHP_EOL;
echo $variables->get('factory') . PHP_EOL;
echo $variables->get('context') . PHP_EOL;
echo $variables->get('context') . PHP_EOL;
echo $variables->get('factory_context') . PHP_EOL;
echo $variables->get('factory_context') . PHP_EOL;
echo json_encode($variables->get('all')) . PHP_EOL;
echo $variables->get('logic') . PHP_EOL;
echo $variables->get('logic_strict') . PHP_EOL;
echo $variables->get('logic_factory') . PHP_EOL;
echo $variables->get('logic_factory') . PHP_EOL;
echo $variables->get('logic_factory') . PHP_EOL;
echo $variables->get('passthrough') . PHP_EOL;
echo $variables->get('passthrough') . PHP_EOL;
echo $variables->get('passthrough_factory') . PHP_EOL;
echo $variables->get('passthrough_factory') . PHP_EOL;
echo $variables->get('passthrough_factory') . PHP_EOL . PHP_EOL;

// Print all computed values
echo json_encode($variables->all(), JSON_PRETTY_PRINT) . PHP_EOL;

// Print all non computed values (Closures do not json encode)
echo json_encode($variables->all(false), JSON_PRETTY_PRINT) . PHP_EOL;
```

Will result in:

```

foo
closure Foo 4
closure Foo 4
Not found
foo
Number 0: 0
Number 1: 1
Prefix: 6
Prefix: 6
Prefix: 10
Prefix: 1
["First","Second","Third"]
Condition: true
Condition: false
Factory Condition: true
Factory Condition: false
Factory Condition: true
PASSTHROUGH: FACTORY CONDITION: FALSE
PASSTHROUGH: FACTORY CONDITION: FALSE
PASSTHROUGH FACTORY: FACTORY CONDITION: TRUE
PASSTHROUGH FACTORY: FACTORY CONDITION: FALSE
PASSTHROUGH FACTORY: FACTORY CONDITION: TRUE

{
    "scalar": "foo",
    "closure": "closure Foo 4",
    "ref": "Not found",
    "ref_on_get": "foo",
    "factory": "Number 2: 2",
    "context": "Prefix: 6",
    "factory_context": "Prefix: 8",
    "all": [
        "First",
        "Second",
        "Third"
    ],
    "logic": "Condition: true",
    "logic_strict": "Condition: false",
    "logic_factory": "Factory Condition: false",
    "passthrough": "PASSTHROUGH: FACTORY CONDITION: FALSE",
    "passthrough_factory": "PASSTHROUGH FACTORY: FACTORY CONDITION: TRUE"
}

{
    "scalar": "foo",
    "closure": "closure Foo 4",
    "ref": "Not found",
    "ref_on_get": "foo",
    "factory": {},
    "context": "Prefix: 6",
    "factory_context": {},
    "all": [
        "First",
        "Second",
        "Third"
    ],
    "logic": "Condition: true",
    "logic_strict": "Condition: false",
    "logic_factory": {},
    "passthrough": "PASSTHROUGH: FACTORY CONDITION: FALSE",
    "passthrough_factory": {}
}
```


## Helpers

A variable value can be any scalar, array, object type but also closure which will be executed and cached. To prevent caching value should implement `NonCacheableClosure` interface for e.g. `factory` helper function.

### Reference

A function to fetch variable value from Variables instance. If not found `$default = null` will be returned.

```php
function ref(string $name, $default = null);
function reference(string $name, $default = null);
```

**Tip:** To fetch value at variable get time surround the function with closure: `fn() => ref('foo.bar')`.

### Enviroment variable

A function to fetch environment variable. If not found `$default = null` will be returned.

```php
function env(string $name, $default = null): string;
```

### Context

A function to wrap your closure and inject context from `Variables::getContext()`;

```php
function context(Closure $closure, ...$args): Closure;Cacheable
```

Will return your closure wrapped with context variables + extra `$args` as arguments. `$args` will be evaluated using `Variables::evalValue()` function. It will behave as normal Variable value.

```php
[
    'context_aware_variable' => context(
        function (Setherator $setherator, string $input) {
            // ... Your logic
            
            return 'Your typed input: ' . $input;
        },
        ask('Input')
    ),
]
```

**Attention**: Context returned closure does not accept any arguments.

### Factory

A function to prevent your closue value from getting cached. Your closure will be called everytime value is accessed. `$args` will be passed as arguments to closure function. `$args` will be evaluated using `Variables::evalValue()` function. It will behave as normal Variable value.


```php
function factory(Closure $closure, ...$args): NonCacheableClosure;
```

Will return a value which implements `NonCacheableClosure` interface and prevents it from caching and behave as your normal closure.

```php
[
    'factory_value' => factory(
        function (string $input) {
            // ... Your logic
            
            return 'Your typed input: ' . $input;
        },
        ask('Input')
    ),
]
```

### All

A function to evaluate all values passed to arguments and return result as array.

```php
function all(...$args): array
```

Will return an array of all `$args` evaluated.

```php
[
    'all' => all(
        ask('Please provide project name'),
        ask('Please provide folder name')
    )
]
```

**Tip:** To evaluate all values at variable get time surround the function with closure: `fn() => all(/* ... */)`.

### Logic

A function to evaluate condition and decide which value to use. `$condtion`, `$true`, `$false` are evaluated using `Variables::evalValue()` function.

```php
function logic($condition, $true, $false, bool $strict = false): Closure;
```

Will return a closure which will decide which `$true` or `$false` to return based on `$condition`

```php
[
    'logic_based_value' => logic(
        fn() => askChoice('Please select app env', ['dev', 'prod']) !== 'dev',
        'Environement is production',
        fn() => 'Enviroment is dev and time is: ' . time()
    )
]
```

**Attention**: Logic returned closure does not accept any arguments.

### Passthrough

A function which will pass all evaluted values as argumnents to closure.

```php
function passthrough(Closure $closure, ...$args): Closure;
```

Will return a closure which will pass `$args` evaluated as arguments to Closure.

```php
[
    'passthough_value' => passthrough(
        function (string $projectName, string $projectFolderName) {
            return 'Project "' . $projectName . '" at "' . $projectFolderName . '"';
        }
        ask('Please provide project name'),
        ask('Please provide project folder name')
    )
]
```

**Attention**: Passthrough returned closure does not accept any arguments.

## Value Parsers

You can implement your own value parsers to be parsed when accessing variables value.

```php
class JsonEncodeValue
{
    private $value;
    
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
}

class JsonEncodeValueParser implements ValueParserInterface
{
    public function supports($value): bool
    {
        return $value instanceof JsonEncodeValue;
    }

    public function parseValue($value, Variables $variables)
    {
        return json_encode($value->getValue());
    }
}

$variables = new Variables();
$variables->addValueParser(new JsonEncodeValueParser());

$variables->set('json', new JsonEncodeValue(['foo' => 'bar']);
$variables->get('json') // => {"foo":"bar"}
```

## Testing

Run test cases

```bash
$ composer test
```

Run test cases with coverage (HTML format)

```bash
$ composer test-coverage
```

Run PHP style checker

```bash
$ composer cs-check
```

Run PHP style fixer

```bash
$ composer cs-fix
```

Run all continuous integration tests

```bash
$ composer ci-run
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.


## License

Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/setherator/variables.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/com/setherator/variables/master.svg?style=flat-square
[ico-quality]: https://img.shields.io/scrutinizer/quality/g/setherator/variables?style=flat-square
[ico-coverage]: https://img.shields.io/scrutinizer/coverage/g/setherator/variables?style=flat-square
[ico-mutation]: https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fsetherator%2Fvariables%2Fmaster
[ico-downloads]: https://img.shields.io/packagist/dt/setherator/variables.svg?style=flat-square
[ico-email]: https://img.shields.io/badge/email-aurimas@niekis.lt-blue.svg?style=flat-square

[link-travis]: https://travis-ci.com/setherator/variables
[link-packagist]: https://packagist.org/packages/setherator/variables
[link-scrutinizer]: https://scrutinizer-ci.com/g/setherator/variables
[link-mutator]: https://dashboard.stryker-mutator.io/reports/github.com/setherator/variables/master
[link-downloads]: https://packagist.org/packages/setherator/variables/stats
[link-email]: mailto:aurimas@niekis.lt
