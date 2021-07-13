# Extract text from a pdf

Convert pdf to plain text, even on empty and encrypted files. This package is based on [spatie/pdf-to-text](https://github.com/spatie/pdf-to-text). 

## Requirements

Behind the scenes this package leverages [pdftotext](https://en.wikipedia.org/wiki/Pdftotext). You can verify if the binary installed on your system by issueing this command:

```bash
which pdftotext
```

If it is installed it will return the path to the binary.

To install the binary you can use this command on Ubuntu or Debian:

```bash
apt-get install poppler-utils
```

On a mac you can install the binary using brew

```bash
brew install poppler
```

If you're on RedHat or CentOS use this:

```bash
yum install poppler-utils
```

To use the OCR functionality, install the following dependencies, in accordance to your package manager. The following example is based on Ubuntu.
```bash
apt-get install tesseract-ocr ocrmypdf

```


To use the decryption functionality, install the following dependencies, in accordance to your package manager.
```bash
#this is Debian based
apt-get install qpdf
```

## Installation

You can install the package via composer:

```bash
composer require spatie/pdf-to-text
```

## Usage

Extracting text from a pdf is easy.

```php
$text = (new Pdf())
    ->setPdf('book.pdf')
    ->text();
```

Or easier:

```php
echo Pdf::getText('book.pdf');
```

By default the package will assume that the `pdftotext` command is located at `/usr/bin/pdftotext`.
If it is located elsewhere pass its binary path to constructor

```php
$text = (new Pdf('/custom/path/to/pdftotext'))
    ->setPdf('book.pdf')
    ->text();
```

or as the second parameter to the `getText` static method:

```php
echo Pdf::getText('book.pdf', '/custom/path/to/pdftotext');
```

Sometimes you may want to use [pdftotext options](https://linux.die.net/man/1/pdftotext). To do so you can set them up using the `setOptions` method.

```php
$text = (new Pdf())
    ->setPdf('table.pdf')
    ->setOptions(['layout', 'r 96'])
    ->text()
;
```

or as the third parameter to the `getText` static method:

```php
echo Pdf::getText('book.pdf', null, ['layout', 'opw myP1$$Word']);
```

Please note that successive calls to `setOptions()` will overwrite options passed in during previous calls. 

If you need to make multiple calls to add options (for example if you need to pass in default options when creating 
the `Pdf` object from a container, and then add context-specific options elsewhere), you can use the `addOptions()` method:
 
 ```php
 $text = (new Pdf())
     ->setPdf('table.pdf')
     ->setOptions(['layout', 'r 96'])
     ->addOptions(['f 1'])
     ->text()
 ;
 ```
 
 To extract data from empty files, use the `scan` method, with it's respective options setter. The method `decrypt` opens "encrypted" files with empty passwords.
 
 ```php
 $text = (new Pdf())
     ->setPdf('table.pdf')
     ->setOptions(['layout', 'r 96'])
     ->addOptions(['f 1'])
     ->setScanOptions(['-l nld+eng', '--skip-text'])
     ->scan()
     ->decrypt()
     ->text()
 ;
 ```
 
## Testing

```bash
 composer test
```

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [Dragomir Țurcanu](https://github.com/dragomirt)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
