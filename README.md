# ptlis/diff-parser

A parser for unified diff files, returning a hydrated object graph.

Uses __toString() to serialize back into unified diff format.


[![Build Status](https://travis-ci.org/ptlis/diff-parser.png?branch=master)](https://travis-ci.org/ptlis/diff-parser) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/diff-parser/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/diff-parser/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/diff-parser/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/diff-parser/) [![Latest Stable Version](https://poser.pugx.org/ptlis/diff-parser/v/stable.png)](https://packagist.org/packages/ptlis/diff-parser)



## Install

Either from the console:

```shell
    $ composer require ptlis/diff-parser:"~0.4"
```

Or by Editing composer.json:

```javascript
    {
        "require": {
            ...
            "ptlis/diff-parser": "~0.4",
            ...
        }
    }
```

Followed by a composer update:

```shell
    $ composer update
```



## Usage


### Build a Changeset

Both public methods of the ```ptlis\DiffParser\Parser``` class return a changeset representation of the diff:

Get a changeset from a file:

```php
    
    use ptlis\DiffParser\Parser;
    
    $parser = new Parser();
    
    $changeset = $parser->parseFile('path/to/svn/diff', Parser::VCS_SVN);
```


Get a changeset from string array:

```php
    
    use ptlis\DiffParser\Parser;
    
    $lineList = ...    // the array of diff lines is created/retrieved somehow 
    
    $parser = new Parser();
    
    $changeset = $parser->parseLines($lineList, Parser::VCS_SVN);
```


### Serialization

All of the value classes implement the ```__toString()``` method to support direct serialization of that component back to unified diff format.

For example, serialization of a changeset back to a file is as simple as:

```php
    $file = fopen('my.patch', 'w');
    fwrite($file, $changeset);
    fclose($file);
```


### The Object Graph

The tree built to store changesets is very simple, in essence:

* A Changeset is the root node & contains Files
* A File contain Hunks
* A Hunk contain Lines
* Lines are the leaf nodes.

#### Changeset

The Changeset class provides a single method to retrieve a list of files that have changed:

```php
    $fileList = $changeset->getChangedFiles();  // Array of ptlis\DiffParser\File instances.
```

#### File

```php
    $file = $fileList[0];           // Get the first changed file
```

Get the original and new filenames:

```php    
    $file->getOriginalFilename();   // Eg 'readme.md' or '' (empty) on create
    $file->getNewFilename();        // EG 'README.md' or '' (empty) on delete
```

Get the operation that was performed (create, delete or change):

```php
    $file->getOperation();          // One of File::CREATED, File::CHANGED, File::DELETED  
```

Get the changed hunks for the file:

```php
    $hunkList = $file->getHunks();  // Array of ptlis\DiffParser\Hunk instances.  
```

#### Hunk

```php
    $hunk = $hunkList[0];           // Get the first hunk for this file
```

Get the hunk metadata:

```php
    $hunk->getOriginalStart();      // Eg '0'
    $hunk->getOriginalCount();      // Eg '5'
    $hunk->getNewStart();           // Eg '0'
    $hunk->getNewCount();           // Eg '7'
```

Get the changed lines:

```php
    $lineList = $hunk->getLines();  // Array of ptlis\DiffParser\Line instances.  
```


#### Line

```php
    $line = $lineList[0];           // Get the first line for this hunk
```

Get the original and new line numbers:

```php
    $line->getOriginalLineNo();     // Eg '7' or '-1' on create
    $line->getNewLineNo();          // Eg '7' or '-1' on delete
```

Get the operation:

```php
    $line->getOperation();          // One of Line::ADDED, Line::REMOVED, Line::UNCHANGED
```

Get the value of the line:

```php
    $line->getContent();              // Eg ' $foo = bar;'
```


## Contributing

You can contribute by submitting an Issue to the [issue tracker](https://github.com/ptlis/vcs/issues), improving the documentation or submitting a pull request. For pull requests i'd prefer that the code style and test coverage is maintained, but I am happy to work through any minor issues that may arise so that the request can be merged.


## TODO

* Add more tests for robustness - being generated, in theory diffs should be reliable, but we still need to gracefully fail when this assumption is false.
