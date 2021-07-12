<?php

namespace Spatie\PdfToText\Exceptions;

use Symfony\Component\Process\Exception\ProcessFailedException;

class CouldNotDecryptFile extends ProcessFailedException
{
}
