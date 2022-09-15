# dompdf

To work with unicode symbols in DOMPDF 0.6 you have two alternatives: use existed fonts or create your own font.

* Use existed font (applied for DOMPDF 0.6):

 1. [Download][1] archive and extract.
 2. Copy extracted files in your dompdf fonts folder `/dompdf/lib/fonts/`.
 3. Edit `dompdf_font_family_cache.dist.php` with snippet 1.
 4. In CSS use `font-family: times;`.

Snippet 1:

    /* ... */
    'times' => array (
        'normal' => DOMPDF_FONT_DIR . 'times',
        'bold' => DOMPDF_FONT_DIR . 'timesbd',
        'italic' => DOMPDF_FONT_DIR . 'timesi',
        'bold_italic' => DOMPDF_FONT_DIR . 'timesbi'
    ),
    'times-roman' => array (
        'normal' => DOMPDF_FONT_DIR . 'times',
        'bold' => DOMPDF_FONT_DIR . 'timesbd',
        'italic' => DOMPDF_FONT_DIR . 'timesi',
        'bold_italic' => DOMPDF_FONT_DIR . 'timesbi'
    ),
    /* ... */

----------
* If you want to use your own TTF font (say, `Arial.ttf`):

 1. Run: `ttf2afm -o Arial.afm Arial.ttf`. (I did it in Ubuntu.)
 2. Run: `ttf2ufm -a -F Arial.ttf`. (I did it in Windows using exe from [UFPDF][2], but I guess you can use `/dompdf/lib/ttf2ufm/bin/ttf2ufm.exe`.)
 4. Copy `Arial.*` files in `/dompdf/lib/fonts/`.
 5. Add to `dompdf_font_family_cache.dist.php` snippet 2.
 6. In CSS use `font-family: arial;`.

Snippet 2:

    /* ... */
    'arial' => array (
        'normal' => DOMPDF_FONT_DIR . 'Arial',
        'bold' => DOMPDF_FONT_DIR . 'Arial',
        'italic' => DOMPDF_FONT_DIR . 'Arial',
        'bold_italic' => DOMPDF_FONT_DIR . 'Arial'
    )
    /* ... */

  [1]: https://github.com/tellnobody1/dompdf/archive/refs/heads/main.zip
  [2]: http://acko.net/blog/ufpdf-unicode-utf-8-extension-for-fpdf/
