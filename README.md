
# SoureCode/ConventionalCommits

[![Latest Stable Version](https://poser.pugx.org/sourecode/conventional-commits/v)](https://packagist.org/packages/sourecode/conventional-commits)
[![Latest Unstable Version](https://poser.pugx.org/sourecode/conventional-commits/v/unstable)](https://packagist.org/packages/sourecode/conventional-commits)
[![Coverage Status](https://coveralls.io/repos/github/SoureCode/ConventionalCommits/badge.svg?branch=master)](https://coveralls.io/github/SoureCode/ConventionalCommits?branch=master)
[![Type Coverage Status](https://shepherd.dev/github/SoureCode/ConventionalCommits/coverage.svg)](https://shepherd.dev/github/SoureCode/ConventionalCommits)
[![Total Downloads](https://poser.pugx.org/sourecode/conventional-commits/downloads)](https://packagist.org/packages/sourecode/conventional-commits)
[![License](https://poser.pugx.org/sourecode/conventional-commits/license)](https://packagist.org/packages/sourecode/conventional-commits)

A library to parse and format [conventional commit](https://www.conventionalcommits.org) messages.

## Features

- Immutable, chainable, unambiguous API.
- Parsing and formatting
- Command to validate string
- Command to validate commits

## Install

```
composer require sourecode/conventional-commits
```

## Usage

Just place a `conventional-commits.json` file in your project and run `conventional-commits`
To use default configuration insert `{}` in your configuration file.

## Default configuration


```json
{
  "type": {
    "min": 2,
    "max": 10,
    "extra": false,
    "values": [
      "add",
      "build",
      "bump",
      "chore",
      "ci",
      "cut",
      "docs",
      "enhance",
      "feat",
      "fix",
      "make",
      "optimize",
      "perf",
      "refactor",
      "revert",
      "style",
      "test"
    ]
  },
  "scope": {
    "min": 3,
    "max": 10,
    "extra": true,
    "required": false,
    "values": []
  },
  "description": {
    "min": 5,
    "max": 50
  }
}
```

### Type

Hint: *This is just for inspiration, just use it as you need.*

- **add**: changes to add new capability or functions
- **build**: changes to build system or external dependencies
- **bump**: increasing the versions or dependency versions
- **chore**: changes for housekeeping (avoiding this will force more meaningful message)
- **ci**: changes to CI configuration files and scripts
- **cut**: removing the capability or functions
- **docs**: changes to the documentation
- **feat**: addition of some new features
- **fix**: a bug fix
- **make**: change to the build process, or tooling, or infra
- **optimize**/**perf**/**enhance**: a code change that improves performance
- **refactor**: a code change that neither fixes a bug nor adds a feature
- **revert**: reverting an accidental commit
- **style**: changes to the code that do not affect the meaning
- **test**: adding missing tests or correcting existing tests
