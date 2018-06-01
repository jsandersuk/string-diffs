<?php
declare(strict_types=1);

namespace JSandersUK\StringDiffs;

use JSandersUK\StringDiffs\Stylists\Stylist;

class Calculator
{
    private $stylist;

    public function __construct(Stylist $stylist)
    {
        $this->stylist = $stylist;
    }

    public function diff(string $old, string $new): string
    {
        $diff = $this->diffArray(
            str_split($old),
            str_split($new)
        );

        $buffer = '';
        foreach ($diff as $item) {
            if (is_array($item)) {
                if ($item['deleted']) {
                    $buffer .= $this->stylist->styleRemovedText(implode('', $item['deleted']));
                }
                if ($item['inserted']) {
                    $buffer .= $this->stylist->styleInsertedText(implode('', $item['inserted']));
                }
            } else {
                $buffer .= $item;
            }
        }

        return $buffer;
    }

    private function diffArray(array $old, array $new): array
    {
        $matrix = array();
        $maxlen = 0;
        foreach ($old as $oldIndex => $oldValue) {
            $newKeys = array_keys($new, $oldValue);
            foreach ($newKeys as $newIndex) {
                if (isset($matrix[$oldIndex - 1][$newIndex - 1])) {
                    $matrix[$oldIndex][$newIndex] = $matrix[$oldIndex - 1][$newIndex - 1] + 1;
                } else {
                    $matrix[$oldIndex][$newIndex] = 1;
                }

                if ($matrix[$oldIndex][$newIndex] > $maxlen) {
                    $maxlen = $matrix[$oldIndex][$newIndex];
                    $oldMax = $oldIndex + 1 - $maxlen;
                    $newMax = $newIndex + 1 - $maxlen;
                }
            }
        }

        if ($maxlen == 0) {
            return [
                [
                    'deleted' => $old,
                    'inserted' => $new
                ]
            ];
        } else {
            return array_merge(
                $this->diffArray(array_slice($old, 0, $oldMax), array_slice($new, 0, $newMax)),
                array_slice($new, $newMax, $maxlen),
                $this->diffArray(array_slice($old, $oldMax + $maxlen), array_slice($new, $newMax + $maxlen))
            );
        }
    }
}
