Evolver: EventSourcing Framework
================================

Evolver is an EventSourcing Framework for PHP.

It aims to stay simple, and not enforce too many requirements on your application and it's model.

## Features

* Entities/Models/Aggregates don't need to extend anything
* PDO EventStore
* JSON EventStore
* Projectors (aka denormalizers)
* Events don't need to handle their own serialization
* Example application with Projector included

## Usage

Please refer to the `example/` directory for an example application (including it's own README.md)

## Inspiration

Evolver started out as an exercise in learning more about CQRS and EventSourcing.

It is heavily inspired by wading through the internals of other excellent projects such as:

* [Broadway](https://github.com/qandidate-labs/broadway)
* [buttercup.protects](https://github.com/buttercup-php/protects)
* [SimpleBus](https://github.com/SimpleBus/)

I'd also highly recommend checking out the book [DDD in PHP](https://leanpub.com/ddd-in-php)

## License

MIT (see [LICENSE.md](LICENSE.md))

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!
