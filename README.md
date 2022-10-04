# Orator (WIP)
PHP logger

# Overview
Helpful library for pretty logging (no more vardump mess). No css just indents and `JSON_PRETTY_PRINT` (best use case is inside files but `<pre>` should also suffice).

# How to use

```php
use Orator\Log;

Log::log('Log A'); // Log A

Log::increaseIndent();
Log::log('Log B'); // →→Log A
Log::decreaseIndent();

Log::log(['asd' => 'asd']);
/*
    Output:
    {
        "asd": "asd"
    }
 */

$log = "First line
Second Line";
Log::log($log); // First line\nSecond Line

// Set $addClass and $addFunction to true
class LogClass
{
    public static function test()
    {
        Log::log('Test');
    }
}
LogClass::test(); // Tetraquark\LogClass | test * Test
```

# Options
Currently there is almost no option to change how logger behave mid run so any changes have to be made directly inside the `Log` file.

## Verbose
You can define how important log output is by passing it as second parameter to `Log` method:
```php
Log::log("log", 2);
```
If `$maxVerbose` is bigger or equal `$verbose` inside the log it will be outputed.

## Trace Level
When `$addClass` and `$addFunction` are enabled you can decide how much back on debug trace you want to go:
```php
class LogClass
{
    public static function test(int $trace = 0)
    {
        Log::log('Test', null, $trace);
    }

    public static function testNested()
    {
        self::test(1);
    }
}

LogClass::test();       // Tetraquark\LogClass | test * Test
LogClass::testNested(); // Tetraquark\LogClass | testNested * Test
```

## Run time
By calling `timerStart` and `timerEnd` you will get how much time flown between those calls in 1000th of the second:

```php
Log::timerStart();
for ($i=0; $i < 10**6; $i++) {
    // code...
}
Log::timerEnd(); // Duration: 0.024s
```

## Stop replacing of the new lines
If you want to display new lines inside of the log set `$replaceNewLine` to false in `Log` method:
```php
Log::log("Line 1\nLine 2", replaceNewLine: false);
/*
    Output:
    Line 1
    Line 2
 */
```
