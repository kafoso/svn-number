svn-number
==================================

**WORK IN PROGRESS**

-------------------------

Subversion (SVN) management in terminal made easy.

-------------------------

# What `svn-number` does

`svn-number` makes management of your SVN projects a blast.

This library includes:

- <a name="pointers"></a>**Pointers**<br/>
Pointers are file indexes, added in front file references when running `svn-number status`. By referencing said indexes when you type new commands, `svn-number` turns each number into its respective file path, meaning you only have to type a number instead of partial or full file paths.
  - **Ranges**<br/>
  Apply the same `svn` command to multiple files at once using the hypen, `-`, to indicate a number ranges. E.g. `2-6` = `[2,3,4,5,6]`.
  - **Non-consecutive numbers (comma separated)**<br/>
  Use commas (without any spaces) to reference non-consecutive numbers. E.g. `2,4` = `[2,4]`. May also be combined with ranges, e.g. `2-4,7` = `[2,3,4,7]`.
- **Colors**<br/>
Colors are added to `svn status` and `svn diff` - i.e. when using `svn-number status` and `svn-number diff`, respectively - vastly improving readability. For details, see [Styling](#usage-styling).
- <a name="fixed-file-paths-escapes"></a>**Fixed file paths & escapes**<br/>
File paths are converted to always use forward slashes, e.g. `C:\bad\windows\is\bad` becomes `C:/bad/windows/is/bad`. No more backslash frustrations!<br/>
<br/>Additionally, file paths are escaped, meaning space characters no longer pose a problem!<br/>
<br/>If you dislike the pointer logic (explained above), you may now - through `svn-number status` - copy-paste the file names, and use them with the standard `svn` actions.

# What `svn-number` **does not** do

- It doesn't modify other arguments than the [Pointers](#pointers) and the [file paths](#fixed-file-paths-escapes) (slash conversion and escaping). This means you have to apply additional arguments as needed, just as before. E.g. `svn-number revert -R my/folder` to recursively revert the folder and all of its contents.

# Installation

Requires: PHP 5.3+

- Retrieve this repository, e.g.: `git clone git@github.com:kafoso/svn-number.git`.
- Add an [alias](#installation-aliasing) or symlink, referencing the location of `svn-number.php`.

That's it!

# Usage

- In a terminal, navigate to your respective SVN project repository.
- Use `svn-number`, e.g. by running `svn-number status`.

Use `svn-number` same way as `svn`, but instead write `svn-number`. I.e. `svn-number <args>` or `svn-number # <args>`, where the `#` is the integer presented to you when doing `svn-number status`.

<a name="usage-styling"></a>
## Styling

Styling is added to `svn-number status` and `svn-number diff`.

<a name="installation-aliasing"></a>
## Aliasing

I recommend doing some aliases in your `.bashrc` or `.profile` for convenience, ease of use, and speed.

For instance:

- `sn ` = `svn-number `

- `sa ` = `svn-number add `
- `sd ` = `svn-number diff `
- `so ` = `svn-number checkout `
- `srev ` = `svn-number revert `
- `srm ` = `svn-number remove `
- `ss ` = `svn-number status `

# Motivation

Going from `git` to `svn`, and from OSX/Linux to Windows, I needed a convenient yet reliable means of managing my SVN projects in a [Git Bash](https://git-for-windows.github.io/) terminal on Windows.

I'm not fond of GUIs for these purposes (PhpStorm, TortoiseSVN, WinSVN, etc.), and I wanted to stick with my ol' faithful terminal.

`git svn` was not an option due to a workplace policy, and getting existing libraries ([`svn-color`](https://github.com/philchristensen/svn-color), [`colordiff`](http://www.colordiff.org/), etc.) to function properly on Windows is often tedious and frustrating.

Therefore, I decided it was time for a minimalist SVN library, providing some much needed conveniences. A library, which should work cross-platform.

PHP became the choice of programming language. It's a language I'm fairly adept in, it's easy to install on most operating systems, and these days it usually works out-of-the-box <sup>**1**</sup>.

# Inspiration & Credits

This library was inspired by:

- It's `git` equivalent: `git-number` (https://github.com/holygeek/git-number)
- `svn-color` (https://github.com/philchristensen/svn-color)

# Disclaimer

License: [MIT](/LICENSE)

Basically: Use `svn-number` at your own risk.

# TODO

- `svn merge`
- Make argument order insignificant. Currently, the svn action and pointers must be provided as 2nd and/or 3rd arguments, e.g. "svn-number status 1" or "svn-number 1 status". However, a command like `svn -u st js\views\home\HomeView.js` is valid; a behavior which `svn-number` should reflect.

# Future plans

- Enhance execution speed. Perhaps comparing change hashes is faster than a full `svn status` when having to do an `svn diff`?

# Footnotes

<sup>**1**</sup>: If you run Git Bash on Windows (as I am), you need to [setup the PHP PATH in Environment Variables](http://stackoverflow.com/a/18190202/1879194) so that it points to the location of your `php.exe` file.
