# gpg-php
Implementation of [zbateson/gpg-interface](https://github.com/zbateson/gpg-interface) using [pecl/gnupg](https://github.com/php-gnupg/php-gnupg).  This library is intended for use with [zbateson/mail-mime-parser](https://github.com/zbateson/mail-mime-parser) to integrate encryption, decryption, signing, or verification with the mail-mime-parser library.

The library isn't intended to abstract gnupg functions, and so its expected that any required setup is performed using the gnupg APIs directly.  [Click here for the gnupg documentation](https://www.php.net/manual/en/book.gnupg.php).

*NOTE*: this library is still a work-in-progress, and its usage in zbateson/mail-mime-parser hasn't yet been completed.

To include it for use in your project, please install via composer:

```
composer require zbateson/gpg-php
```

## Requirements

gpg-php requires PHP 5.4 or newer.

## Usage

```
// see PHP's documentation for setup instructions
$res = \gnupg_init();

// ...
// specify keys to use, etc...
// ...

$gpgPhp = new GpgPhp($res);

// pass it to ZBateson\MailMimeParser

// ... stay tuned
```

## License

BSD licensed - please see [license agreement](https://github.com/zbateson/gpg-interface/blob/master/LICENSE).