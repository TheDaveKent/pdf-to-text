<?php

namespace Spatie\PdfToText;

use Spatie\PdfToText\Exceptions\CouldNotDecryptFile;
use Spatie\PdfToText\Exceptions\CouldNotExtractText;
use Spatie\PdfToText\Exceptions\CouldNotScanPdf;
use Spatie\PdfToText\Exceptions\PdfNotFound;
use Symfony\Component\Process\Process;

class Pdf
{
    protected $pdf;

    protected $binPath;

    protected $binPathOcr;

    protected $binPathQPDF;

    protected $options = [];

    protected $scanOptions = [];

    public function __construct(string $binPath = null, string $binPathOcr = null, string $binPathQPDF = null)
    {
        $this->binPath = $binPath ?? '/usr/bin/pdftotext';
        $this->binPathOcr = $binPathOcr ?? '/usr/bin/ocrmypdf';
        $this->binPathQPDF = $binPathQPDF ?? '/usr/bin/qpdf';
    }

    public function setPdf(string $pdf): self
    {
        if (!is_readable($pdf)) {
            throw new PdfNotFound("Could not read `{$pdf}`");
        }

        $this->pdf = $pdf;

        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = $this->parseOptions($options);

        return $this;
    }

    public function setScanOptions(array $options): self
    {
        $this->scanOptions = $this->parseOptions($options);

        return $this;
    }

    public function addOptions(array $options): self
    {
        $this->options = array_merge(
            $this->options,
            $this->parseOptions($options)
        );

        return $this;
    }

    public function addScanOptions(array $options): self
    {
        $this->scanOptions = array_merge(
            $this->scanOptions,
            $this->parseOptions($options)
        );

        return $this;
    }

    protected function parseOptions(array $options): array
    {
        $mapper = function (string $content) : array {
            $content = trim($content);
            if ('-' !== ($content[0] ?? '')) {
                $content = '-'.$content;
            }

            return explode(' ', $content, 2);
        };

        $reducer = function (array $carry, array $option) : array {
            return array_merge($carry, $option);
        };

        return array_reduce(array_map($mapper, $options), $reducer, []);
    }

    public function scan() : self
    {
        $process = new Process(array_merge([$this->binPathOcr], $this->scanOptions, [$this->pdf, $this->pdf]));
        $process->run();
        if (!$process->isSuccessful()) {
            throw new CouldNotScanPdf($process);
        }

        return $this;
    }

    public function text() : string
    {
        $process = new Process(array_merge([$this->binPath], $this->options, [$this->pdf, '-']));
        $process->run();
        if (!$process->isSuccessful()) {
            throw new CouldNotExtractText($process);
        }

        return trim($process->getOutput(), " \t\n\r\0\x0B\x0C");
    }

    public function decrypt(): self
    {
        $tempfile = "/tmp/temp" . rand(0, 999999999) . ".pdf";
        $process = new Process(array_merge([$this->binPathQPDF], ["--decrypt --no-warn"], [$this->pdf, $tempfile]));
        $process->run();
        if (!$process->isSuccessful()) {
            throw new CouldNotDecryptFile($process);
        }


        // Copy back the contents
        $process = new Process(["mv", $tempfile, $this->pdf]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new CouldNotDecryptFile($process);
        }

        return $this;
    }

    public static function getText(string $pdf, string $binPath = null, array $options = []) : string
    {
        return (new static($binPath))
            ->setOptions($options)
            ->setPdf($pdf)
            ->text()
        ;
    }
}
