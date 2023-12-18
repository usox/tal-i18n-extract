<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Writer;

use InvalidArgumentException;
use Traversable;

final class PotWriter
{
    /**
     * @param Traversable<non-empty-string> $extractor
     */
    public function write(
        Traversable $extractor,
        string $destination = 'php://stdout'
    ): void {
        $dict = [];

        $stream = @fopen($destination, 'w');
        if ($stream === false) {
            throw new InvalidArgumentException(
                sprintf('Cannot open `%s` for writing', $destination)
            );
        }

        /** @var non-empty-string $line */
        foreach ($extractor as $line) {
            if (!array_key_exists($line, $dict)) {
                fwrite(
                    $stream,
                    <<<MSG
                        msgid "$line"
                        msgstr "$line"\n\n
                        MSG
                );
            }
            $dict[$line] = $line;
        }

        fclose($stream);
    }
}
