<?php

declare(strict_types=1);

namespace App\Dtos;

use Exception;
use JsonException;
use Spatie\LaravelData\Data;

final class PrompterApiRandomItem extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly string $format,
        public readonly string $hash,
        public readonly string $prompt,
        public readonly string $markdown = '',
        public readonly string $file = '',
        public readonly string $template = '',
    ) {}

    /**
     * @throws JsonException
     */
    public function getFileData(): array
    {
        return json_decode($this->file, true, 512, JSON_THROW_ON_ERROR);
    }

    public function withTemplate(string $template): self
    {
        return new self(
            title: $this->title,
            format: $this->format,
            hash: $this->hash,
            prompt: $this->prompt,
            markdown: $this->markdown,
            file: $this->file,
            template: $template,
        );
    }

    /**
     * @throws Exception
     */
    public function parsePrompt(): self
    {
        $fileInfo = $this->getFileData();
        if (filled($fileInfo['base64'])) {
            $markdown = base64_decode($fileInfo['base64']);
        } else {
            $markdown = json_decode($this->prompt, true, 512, JSON_THROW_ON_ERROR);
        }

        return new self(
            title: $this->title,
            format: $this->format,
            hash: $this->hash,
            prompt: $this->prompt,
            markdown: $markdown,
            file: $this->file,
            template: $this->template,
        );
    }

    public function clearFile(): self
    {
        return new self(
            title: $this->title,
            format: $this->format,
            hash: $this->hash,
            prompt: $this->prompt,
            markdown: $this->markdown,
            file: '',
            template: $this->template,
        );
    }
}
