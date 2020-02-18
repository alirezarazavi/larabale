# LaraBale

## Laravel Bale Bot API

LaraBale is laravel package for working with bale bot api.

### Notice:

> Package is in early development and it's not production ready yet. Please wait for future releases. I also would greatly appreciate it if you kindly give me some feedback on this package.

## Installation

1. install with composer:

> composer require alirezarazavi/larabale

2. get your bot token (https://devbale.ir) and copy inside your project .env file:

> BALE_BOT_TOKEN={YOUR-BOT-API-TOKEN}

3. (optional) publish config file:

> php artisan vendor:publish --tag=bale-config

## Usage

> A simple method for testing your bot's auth token. Requires no parameters. Returns basic information about the bot in form of a User object:

```php
Bale::getMe();
```

## Credits

[Alireza Razavi](https://www.alirezarazavi.com) [(Contact me)](mailto:sar.razavi@gmail.com)

## License

The LaraBale is open-sourced software licensed under the MIT license.
