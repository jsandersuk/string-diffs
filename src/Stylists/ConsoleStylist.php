<?php
declare(strict_types=1);

namespace JSandersUK\StringDiffs\Stylists;

use Bramus\Ansi\Ansi;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use Bramus\Ansi\Writers\BufferWriter;

final class ConsoleStylist implements Stylist
{
    private $removedTextColour;
    private $insertedTextColour;
    private $ansi;

    public function __construct(
        string $removedTextColour = SGR::COLOR_FG_RED,
        string $insertedTextColour = SGR::COLOR_FG_GREEN
    ) {
        $this->removedTextColour = $insertedTextColour;
        $this->insertedTextColour = $insertedTextColour;
        $this->ansi = new Ansi(new BufferWriter());
    }

    public function styleRemovedText(string $text): string
    {
        return $this->styleText($this->removedTextColour, $text);
    }

    public function styleInsertedText(string $text): string
    {
        return $this->styleText($this->insertedTextColour, $text);
    }

    private function styleText(string $colour, $text): string
    {
        $ansi = $this->ansi;
        $ansi->color([$colour]);
        $ansi->text($text);
        $ansi->nostyle();
        return $ansi->flush();
    }
}