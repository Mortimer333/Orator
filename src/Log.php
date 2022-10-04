<?php declare(strict_types=1);

namespace Orator;

abstract class Log
{
    static protected int     $indent        = 0;
    static protected int     $verbose       = 0;
    static protected int     $maxVerbose    = 0;
    static protected int     $classLimit    = 50;
    static protected int     $functionLimit = 20;
    static protected array   $timeStart     = [];
    static protected bool    $addClass      = false;
    static protected bool    $addFunction   = false;

    static public function timerStart(): void
    {
        self::$timeStart[] = microtime();
    }

    static public function timerEnd(): void
    {
        $timeEnd = self::getMilliseconds(microtime());

        if (empty(self::$timeStart)) {
            throw new Exception('Time start is empty', 400);
        }

        $timeStart = self::getMilliseconds(self::$timeStart[\sizeof(self::$timeStart) - 1]);
        array_pop(self::$timeStart);
        $time = $timeEnd - $timeStart;
        self::log('Duration: ' . ($time/1000) . 's', null, 1);
    }

    static private function getMilliseconds(string $microtime) {
        $mt = explode(' ', $microtime);
        return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
    }

    static public function time(): void
    {
        list($usec, $sec) = explode(" ", microtime());
        self::log(date('H:i:s:') . round($usec * 1000), 0 , 1);
    }

    static public function log(array|string|int|float $output, ?int $verbose = null, int $traceLvl = 0, bool $replaceNewLine = true): void
    {
        if (is_array($output)) {
            $replaceNewLine = false;
            $output = json_encode($output, JSON_PRETTY_PRINT);
        }
        $verbose = $verbose ?? self::$verbose;
        $indentStr = "  ";

        if ($verbose > self::$maxVerbose) {
            return;
        }

        $message = str_repeat($indentStr, self::$indent) . $output;
        $class = '';
        $function = '';
        if (self::$addClass) {
            $debug = debug_backtrace()[1 + $traceLvl] ?? debug_backtrace()[1];
            $class = self::fitString($debug['class'], self::$classLimit);
        }
        if (self::$addFunction) {
            if (!isset($debug)) {
                $debug = debug_backtrace()[1 + $traceLvl] ?? ['function' => 'not found'];
            }
            if (\mb_strlen($class) > 0) {
                $function .= ' | ';
            }
            $function .= self::fitString($debug['function'], self::$functionLimit) . ' * ';
        }
        if (\mb_strlen($class) > 0 && \mb_strlen($function) == 0) {
            $class .= ' * ';
        }
        if ($replaceNewLine) {
            echo $class . $function . str_replace("\r", '\r', str_replace("\n", '\n', $message)) . PHP_EOL;
        } else {
            echo $class . $function . str_replace("\n", "\n" . str_repeat($indentStr, self::$indent), $message) . PHP_EOL;
        }
    }

    static protected function fitString(string $string, int $size): string
    {
        $slimed = '';
        if (\mb_strlen($string) > $size) {
            $slimed = \mb_substr($string, 0, (int) (floor($size/2) - 1)) . '..';
            $slimed .= \mb_substr($string, (int) -ceil($size/2) + 1);
        } elseif (\mb_strlen($string) < $size) {
            $amount = $size - \mb_strlen($string);
            $slimed = str_repeat(' ', (int) floor($amount/2)) . $string . str_repeat(' ', (int) ceil($amount/2));
        }
        return $slimed;
    }

    static public function getIndent(): int
    {
        return self::$indent;
    }

    static public function setIndent(int $indent): void
    {
        if ($indent < 0) {
            $indent = 0;
        }
        self::$indent = $indent;
    }

    static public function increaseIndent(): void
    {
        self::$indent++;
    }

    static public function decreaseIndent(): void
    {
        self::$indent--;
        if (self::$indent < 0) {
            self::$indent = 0;
        }
    }

    static public function setVerboseLevel(int $level): void
    {
        self::$verbose = $level;
    }

    static public function setMaxVerboseLevel(int $level): void
    {
        self::$maxVerbose = $level;
    }

    public static function boolToStr(bool $bool): string
    {
        return $bool ? 'true' : 'false';
    }
}
