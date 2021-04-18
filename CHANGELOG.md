# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.1.2 - TBD

## 0.1.1 - 2021-04-18

### Bug Fixes

- Change range pattern from dash to tripple dot ([#6](https://github.com/SoureCode/ConventionalCommits/pull/6)) ([0ea71c7](https://github.com/SoureCode/ConventionalCommits/commit/0ea71c75fed34578bdb3a17ea753ebd6e330f2eb))

### Chore

- Fix version ([#5](https://github.com/SoureCode/ConventionalCommits/pull/5)) ([d6d152d](https://github.com/SoureCode/ConventionalCommits/commit/d6d152daa0ee69bc9d9c4c7763a63fe5c8745aba))

### Build

- Add missing install dependencies step ([#9](https://github.com/SoureCode/ConventionalCommits/pull/9)) ([302892e](https://github.com/SoureCode/ConventionalCommits/commit/302892e2fe33898e43d83eb5bccbeccd0b599525))
- Remove deprecated --no-suggest flag ([#9](https://github.com/SoureCode/ConventionalCommits/pull/9)) ([a299956](https://github.com/SoureCode/ConventionalCommits/commit/a299956e1cc5700f162b4410f83602368e853ca7))

### BREAKING CHANGES

- Change range pattern from dash to tripple dot.
  It causes split issues if the branch name contains a dash and the git returns the error that the branch was not found.

## 0.1.0 - 2021-04-17

**Note:** Initial release. :rocket:
