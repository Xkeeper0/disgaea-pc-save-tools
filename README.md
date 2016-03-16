# disgaea-pc-save-tools

Tools for decrypting and decompressing (soon: compressing?) Disgaea PC save files.

Written in PHP. Yes, I know. It works. Currently uses `php 5.6.11`, probably will work fine with anything modern (`>=5.5`) but who knows


## Usage

`php extract.php <path to save file>`

Example:

`php extract.php saves/SAVE000.DAT`

This will decrypt, decompress, and write the internal save data to `<file>.bin`.

It will also store the plain decrypted version to `<file>.dec`, which is usable directly as a save file if you want. (It changes the XOR key to `00 00 00 00`.) The contents are still compressed, but if you know what you're doing you can still break.


Recompressing is a work in progress (see `inject.php`) but crashes the game for Reasons. We're working on it.


A lot of junk is in `test.php` and other scattered files. Some of the code is commented. A lot isn't. Work in progress.



Some save files are included in `saves/` for ease of making sure things work.


## Contributing

Please follow the style used in this (even if it isn't very good). Comments and documentation appreciated.
Maybe a text file describing how the format(s) work in the future.

Currently chat goes in `irc.badnik.net #disgaea` though a lot of it is just banter about the game's data format and me pulling my hair out.



## Notes
Describes save file format for PS2 version; it's different here (some fields are removed or changed), but was still useful: http://www.gamefaqs.com/ps2/589678-disgaea-hour-of-darkness/faqs/35073

Phantom Kingdom Portable translation thread, mostly useful because of `YKCMP_V1` format documentation in third post: https://gbatemp.net/threads/phantom-kingdom-portable-english-translation.365313/

