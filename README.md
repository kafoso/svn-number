Svn Number
==================================

**WORK IN PROGRESS**

-------------------------

Do your Subversion modifications in terminal through numbers instead of full file paths.

-------------------------

- Adds numbers in front of `svn number status`, and enables files references through said numbers. No more writing full paths!
- Adds colors to `svn status` (`svn number status`) and  `svn diff` (`svn number diff`).

# Installation

Requires: PHP 5.3+

**TODO**

# Usage

Use the same was as every other `svn` command, but instead append "number" directly after "svn". I.e. `svn number <args>` or `svn number # <args>`, where the `#` is the integer presented to you when doing `git number status`.

## Aliasing

I recommend doing some aliases in your `.bashrc` or `.profile` for ease of use. E.g.:

- `sn ` = `svn number `
- `ss ` = `svn number status`
- `sd ` = `svn number <#> diff`

# Inspiration & Credit

This library was inspired by:

- It's `git` equivalent: `git-number` (https://github.com/holygeek/git-number)
- `svn-color` (https://github.com/philchristensen/svn-color)

# TODO

- `svn merge`
