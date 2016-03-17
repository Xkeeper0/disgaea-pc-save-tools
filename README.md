# disgaea-pc-save-tools

Tools for decrypting and decompressing (soon: compressing?) Disgaea PC save files.

Written in PHP. Yes, I know. It works. Currently uses `php 5.6.11`, probably will work fine with anything modern (`>=5.5`) but who knows. Also requires [php5-intl](http://php.net/manual/en/intl.installation.php) (`apt-get install php5-intl`) if you want to normalize strings. (If you can't get `intl` or don't mind fullwidth, edit `utils.php` `sjis()` to remove that bit. todo: check if intl exists first.)


## Usage

`php extract.php <path to save file>`

Example:

`php extract.php saves/SAVE000.DAT`

This will decrypt, decompress, and write the internal save data to `<file>.bin`.

It will also store the plain decrypted version to `<file>.dec`, which is usable directly as a save file if you want. (It changes the XOR key to `00 00 00 00`.) The contents are still compressed, but if you know what you're doing you can still break.


Editing the save file is doable. See `inject.php` if you want to edit the raw file, or `test.php` (currently) to see how editing objects works. It's complicated and brand new, so hopefully more documentation hapens soon.


Some save files are included in `saves/` for ease of making sure things work.


## Contributing

Please follow the style used in this (even if it isn't very good). Comments and documentation appreciated.
Maybe a text file describing how the format(s) work in the future.

Currently chat goes in `irc.badnik.net #disgaea` though a lot of it is just banter about the game's data format and me pulling my hair out.



## Notes
Describes save file format for PS2 version; it's different here (some fields are removed or changed), but was still useful: http://www.gamefaqs.com/ps2/589678-disgaea-hour-of-darkness/faqs/35073

Phantom Kingdom Portable translation thread, mostly useful because of `YKCMP_V1` format documentation in third post: https://gbatemp.net/threads/phantom-kingdom-portable-english-translation.365313/

Some other things might be in assorted files.