# disgaea-pc-save-tools

Tools for decrypting and decompressing (soon: compressing?) Disgaea PC save files.

Written in PHP. Yes, I know. It works.


## Usage

`php test.php <path to save file>`

Example:
`php test.php saves/SAVE000.DAT`

This will decrypt, decompress, and write the internal save data to `<file>.bin`.
As of right now, there is no way to re-encode, but maybe in the future.

Possibly also in the future: Splitting the data into sensible values for editing and re-importing???

Most of the text in the save file is in Shift-JIS format, so have fun with that.


## Contributing

Please follow the style used in this (even if it isn't very good). Comments and documentation appreciated.
Maybe a text file describing how the format(s) work in the future.

