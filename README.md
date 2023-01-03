# ptlis/diff-parser

A parser for unified diff files, returning a hydrated object graph.

Uses __toString() to serialize back into unified diff format.

[![CircleCI](https://dl.circleci.com/status-badge/img/gh/ptlis/diff-parser/tree/main.svg?style=svg)](https://dl.circleci.com/status-badge/redirect/gh/ptlis/diff-parser/tree/main) [![codecov](https://codecov.io/gh/ptlis/diff-parser/branch/master/graph/badge.svg?token=r8NgjZyVVL)](https://codecov.io/gh/ptlis/diff-parser) [![Latest Stable Version](https://poser.pugx.org/ptlis/diff-parser/v/stable.png)](https://packagist.org/packages/ptlis/diff-parser)


## Install

Install with composer:

```shell
$ composer require ptlis/diff-parser
```


## Usage


### Parsing a diff file

Create a parser:

```php
$parser = new \ptlis\DiffParser\Parser();
```

And then get a changeset from a file path:

```php
$changeset = $parser->parseFile('path/to/git/diff', Parser::VCS_GIT);
```

or parse the patch data stored from a variable:

```php
$changeset = $parser->parse($patchData, Parser::VCS_SVN);
```


### Serialization

All the value classes implement the ```__toString()``` method to support direct serialization of that component back to unified diff format.

For example this serializes the data in `$changeset` into the file `my.patch`.

```php
\file_put_contents('my.patch', $changeset);
```


### The Object Graph

The tree built to store changesets is very simple, mapping one-to-one to the components of a diff file. In essence:

* A Changeset is the root node & contains Files
* A File contain Hunks
* A Hunk contain Lines
* Lines are the leaf nodes.

#### Changeset

From a Changeset you may iterate over the array of files that have changed:

```php
foreach ($changeset->files as $file) {
    // $file is an instance of ptlis\DiffParser\File
}
```

#### File

Get the original and new filenames:

```php    
$file->filename->original;  // Eg 'readme.md' or '' (empty) on create
$file->filename->new;       // EG 'README.md' or '' (empty) on delete
```

Get the operation that was performed (create, delete or change):

```php
$file->operation;   // One of File::CREATED, File::CHANGED, File::DELETED  
```

From a file you may iterate over the change hunks:

```php
foreach ($file->hunks as $hunk) {
    // $hunk is an instance of ptlis\DiffParser\Hunk
}  
```

#### Hunk

Get the start line number of the hunk:

```php
$hunk->startLine->original; // Eg '0'
$hunk->startLine->new;      // Eg '0'
```

Get the number of lines affected in the hunk:

```php
$hunk->affectedLines->original; // Eg '5'
$hunk->affectedLines->new;      // Eg '7'
```

From a hunk you may iterate over the changed lines:

```php
foreach ($hunk->lines as $line) {
    // $line is an instance of ptlis\DiffParser\Line
}
```


#### Line

Get the original and new line numbers:

```php
$line->number->original;    // Eg '7' or '-1' on create
$line->number->new;         // Eg '7' or '-1' on delete
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
