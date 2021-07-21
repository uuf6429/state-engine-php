# State Engine (PHP)

[![CI](https://github.com/uuf6429/state-engine-php/workflows/CI/badge.svg)](https://github.com/uuf6429/state-engine-php/actions/workflows/ci.yml)
[![Minimum PHP Version](https://img.shields.io/badge/php-%5E7.4%20%7C%20%5E8-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/uuf6429/state-engine-php/main/LICENSE)
[![Packagist](https://img.shields.io/packagist/v/uuf6429/state-engine.svg)](https://packagist.org/packages/uuf6429/state-engine)

This library provides some interfaces and a basic implementation of a State Engine.

**Highlights:**
- Highly composable - everything can be replaced as desired
- [PSR-14](http://www.php-fig.org/psr/psr-14/) (Event Dispatcher) compatible
- Fluent interface
- Generates PlantUML markup

## Installation

The recommended and easiest way to install this library is through [Composer](https://getcomposer.org/):

```bash
composer require uuf6429/state-engine-php "~2.0"
```

## Why?

In principle such an engine is easy to implement, but in practice it is typically implemented badly or forgotten.

For instance, one might have an `is_active` field thinking there will not be other states and then later on realizes the
need for an `is_pending` field, at which point refactoring flags to state is too late.

In any case, this library abstracts away that situation or at least decreases the amount of code.

## How?

There are a few key parts to how this works:

- **State** - an object representing a single state of a model. So models may have different state levels, for example a
  door can have _open_ and _closed_ states, but it can also be _locked_ and _unlocked_. In such a case, either consider
  the door lock as a separate model (with a separate engine instance) or merge all the states: _open_, _closed-unlocked_
  and _closed-locked_.
- **Transition** - an object representing a transition from one state to another. This is how you define the various
  state flows that your model can go through.
- **TransitionRepository** - an object that is aware of and provides all possible allowed transitions.
- **Engine** - an object that performs the transition of a model from one state to another. Usually you would have an
  engine instance for each stateful model in your application.

## Usage

Usage depends on if you want to do it from scratch or plug it into your existing code.

### From Scratch

Here's a quick & dirty example with the provided implementation (that assumes that there is a "door" model):

```php
use App\Models\Door;  // example model

use uuf6429\StateEngine\Implementation\Builder;
use uuf6429\StateEngine\Implementation\Entities\State;

$doorStateManager = Builder::create()
    ->addState('open', 'Open')
    ->addState('closed', 'Closed')
    ->addState('locked', 'Locked')
    ->addTransition('open', 'closed', 'Close the door')
    ->addTransition('closed', 'locked', 'Lock the door')
    ->addTransition('locked', 'closed', 'Unlock the door')
    ->addTransition('closed', 'open', 'Open the door')
    ->getEngine(); // you can pass an event dispatcher to the engine here

// find Door 123 (laravel-style repository-model)
$door = Door::find(123);

// build a state mutator (useful when the model does not have get/setState)
$doorStateMutator = Builder::stateMutator(
    static fn(): State => new State($door->status),                                    // getter
    static fn(State $newState) => $door->update(['status' => $newState->getName()])    // setter
);

// close the door :)
$doorStateManager->changeState($doorStateMutator, new State('closed'));
```

### From Scratch (Custom)

You don't like how the Engine works? Or you feel that State could have more details?
Then you're in luck! With the whole library based on interfaces, you can easily replace parts of the implementation.
For example, you could store states or transitions in a database, in which case you can have your own
`TransitionRepository` that accesses the database.

### Existing Code

The library provides some flexibility so that you can connect your existing code with it. In more complicated scenarios,
you may have to build a small layer to bridge the gap. The example below illustrates how one can handle models with
flags instead of a single state.
```php
use App\Models\Door;  // example model

use uuf6429\StateEngine\Implementation\Builder;
use uuf6429\StateEngine\Implementation\Entities\State;

$door = Door::find(123);

$doorStateMutator = Builder::stateMutator(
    static function () use ($door): State {             // getter
        if ($door->is_locked) {
            return new State('locked');
        }

        return $door->is_open
            ? new State('open')
            : new State('closed');
    },
    function (State $newState) use ($door): void {      // setter
        $door->update([
            'is_locked' => $newState->getName() === 'locked',
            'is_open' => $newState->getName() === 'open',
        ]);
    }
);
```

## Examples & Testing

The JiraIssueTest class serves as a test as well as a realistic example of how Jira Issue states could be set up.

The test also generates the Plant UML diagram below (embedded as an image due to GFM limitations).

![example](https://www.planttext.com/api/plantuml/svg/TPBDRiCW48JlFCKUauDV88SgZgfAlLIrymGqJ2rK31PiBENjYurfux_hpZVB370EB3tVMoF4uI9lFyOrHogA5pgKLff7qE589xgWqPRaD5cIxvPUqG_ScmnSi8ygVJjF2ZsCwrfO5a_xHbCDgHuZDNcpJZVNTWQCbUNlr1FLuBktn8w-qb0i5wuwV02AMkSHOx7K9cnR_ikaqhCEMLmqgCg1lyAg8L5Lxe8r36J0nbNvfEmwfqnNTjqyqZn5hf0IfGQCmDes8i-tDrTbZAGDr1xtb3sodpA4WTtG9rzmfeTAZpKg8vsdwmTr7QmGvtY9yJV-0W00)
