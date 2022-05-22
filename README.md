# ptlis/diff-parser

A parser for unified diff files, returning a hydrated object graph.

Uses __toString() to serialize back into unified diff format.


[![Build Status](https://api.travis-ci.com/ptlis/diff-parser.svg?branch=master)](https://app.travis-ci.com/github/ptlis/diff-parser) [![codecov](https://codecov.io/gh/ptlis/diff-parser/branch/master/graph/badge.svg?token=r8NgjZyVVL)](https://codecov.io/gh/ptlis/diff-parser) [![Latest Stable Version](https://poser.pugx.org/ptlis/diff-parser/v/stable.png)](https://packagist.org/packages/ptlis/diff-parser)



## Install

Install with composer:

```shell
$ composer require ptlis/diff-parser
```


## Usage


### Build a Changeset

Get a changeset from a file:

```php
<?php

use ptlis\DiffParser\Parser;

$parser = new Parser();

$changeset = $parser->parseFile('path/to/svn/diff', Parser::VCS_SVN);
```

Get a changeset from a variable containg the contents of a patch file:

```php
<?php

use ptlis\DiffParser\Parser;

$parser = new Parser();

$patchData = \file_get_contents('/path/to/patchfile');

$changeset = $parser->parse($patchData, Parser::VCS_SVN);
```


### Serialization

All of the value classes implement the ```__toString()``` method to support direct serialization of that component back to unified diff format.

For example, serialization of a changeset back to a file is as simple as:

```php
\file_put_contents('my.patch', $changeset);
```


### The Object Graph

The tree built to store changesets is very simple, in essence:

* A Changeset is the root node & contains Files
* A File contain Hunks
* A Hunk contain Lines
* Lines are the leaf nodes.

#### Changeset

From a Changeset you get an array of files that have changed:

```php
$files = $changeset->files;  // Array of ptlis\DiffParser\File instances.
```

#### File

```php
$file = $files[0];   // Get the first changed file
```

Get the original and new filenames:

```php    
$file->originalFilename;    // Eg 'readme.md' or '' (empty) on create
$file->newFilename;         // EG 'README.md' or '' (empty) on delete
```

Get the operation that was performed (create, delete or change):

```php
$file->operation;   // One of File::CREATED, File::CHANGED, File::DELETED  
```

Get the changed hunks for the file:

```php
$hunks = $file->hunks;  // Array of ptlis\DiffParser\Hunk instances.  
```

#### Hunk

```php
$hunk = $hunks[0];  // Get the first hunk for this file
```

Get the hunk metadata:

```php
$hunk->originalStart;   // Eg '0'
$hunk->originalCount;   // Eg '5'
$hunk->newStart;        // Eg '0'
$hunk->newCount;        // Eg '7'
```

Get the changed lines:

```php
$lines = $hunk->lines;  // Array of ptlis\DiffParser\Line instances.  
```


#### Line

```php
$line = $lines[0];  // Get the first line for this hunk
```

Get the original and new line numbers:

```php
$line->originalLineNo;  // Eg '7' or '-1' on create
$line->newLineNo;       // Eg '7' or '-1' on delete
```

Get the operation:

```php
$line->operation;   // One of Line::ADDED, Line::REMOVED, Line::UNCHANGED
```

Get the value of the line:

```php
$line->content; // Eg ' $foo = bar;'
```


## Contributing

You can contribute by submitting an Issue to the [issue tracker](https://github.com/ptlis/vcs/issues), improving the documentation or submitting a pull request. For pull requests i'd prefer that the code style and test coverage is maintained, but I am happy to work through any minor issues that may arise so that the request can be merged.


## TODO

* Add more tests for robustness - being generated, in theory diffs should be reliable, but we still need to gracefully fail when this assumption is false.
