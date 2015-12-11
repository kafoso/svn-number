svn-number
==================================

**WORK IN PROGRESS**

-------------------------

Subversion (SVN) management in terminal made easy.

-------------------------

# What is it?

`svn-number` makes management of your SVN projects much easier.

- **Pointers**<br/>
Pointers (numbers) are added in front file modifications when running `svn status`. By referencing said numbers, `svn-number` turns each number into it's respective file path.
  - **Ranges**<br/>
  Apply the same `svn` command to multiple files at once.
- **Colors**<br/>
Colors are added to `svn status` and  `svn diff`, vastly improving readability. For details, see [Styling](#usage-styling).
- **Fixed file paths**
File paths a converted to always use forward slashes. No more backslash frustrations!

# Installation

Requires: PHP 5.3+

**TODO**

# Usage

Use the same way as `svn`, but instead write `svn-number`. I.e. `svn-number <args>` or `svn-number # <args>`, where the `#` is the integer presented to you when doing `git number status`.

<a name="usage-styling"></a>
## Styling

Styling is added to `svn-number status` and  `svn-number diff`.

## Aliasing

I recommend doing some aliases in your `.bashrc` or `.profile` for ease of use. E.g.:

- `sn ` = `svn-number `
- `ss ` = `svn-number status `
- `sd ` = `svn-number diff `

# Inspiration & Credit

This library was inspired by:

- It's `git` equivalent: `git-number` (https://github.com/holygeek/git-number)
- `svn-color` (https://github.com/philchristensen/svn-color)

# Disclaimer

License: [MIT](/LICENSE)

Basically: Use `svn-number` at your own risk.

# Reasoning behind this library

Going from `git` to `svn`, and from OSX/Linux to Windows, I needed a convenient yet reliable means of managing my SVN projects in a [Git Bash](https://git-for-windows.github.io/) terminal on Windows.

I'm not fund of GUIs for these purposes (PhpStorm, TortoiseSVN, WinSVN, etc.), so I wanted to stick with my ol' faithful terminal.

`git svn` was not an option due to a workplace policy, and getting existing libraries ([`svn-color`](https://github.com/philchristensen/svn-color), [`colordiff`](http://www.colordiff.org/), etc.) to function properly on Windows is often tedious and frustrating.

Therefore, I decided it was time for a minimalist SVN library, providing some much needed conveniences. A library, which should work cross-platform.

PHP became the choice of programming language. It's a language I'm fairly adept in, it's easy to install on most operating systems, and these days it usually works out-of-the-box <sup>**1**</sup>.

# TODO

- `svn merge`
- Ranges

# Future plans

- Enhance run speed.

# Footnotes

<sup>**1**</sup>: If you run Git Bash on Windows (as I am), you need to [setup the PHP PATH in Environment Variables](http://stackoverflow.com/a/18190202/1879194) so that it points to the location of your `php.exe` file.
