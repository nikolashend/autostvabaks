# Brand source files (Autostvabaks)

Original logo package supplied by Oleg (oleg@motionmotors.ee) on 2026-10-27, committed unchanged
(original file names, no recolouring, no manual edits). `source/svg/` is the working master for the
site; EPS/PNG/JPEG are kept as reference/print sources only.

```
resources/brand/source/svg/   logo-av-01…06.svg   ← use these in the build
resources/brand/source/eps/   logo-av-01…06.eps
resources/brand/source/png/   logo-av-01…06.png   (1200×1200 / 1772×600 previews)
resources/brand/source/jpeg/  logo-av-01…06.jpg
```

## Variants

| File | Layout | ViewBox | Colours | Intended background |
|---|---|---|---|---|
| logo-av-01 | stacked (2 lines) | 283.46 × 283.46 | graphite `#141414` + magenta `#E6007E` | white / light |
| logo-av-02 | stacked | 283.46 × 283.46 | white `#FFFFFF` + magenta `#E6007E` | dark (graphite) |
| logo-av-03 | stacked | 283.46 × 283.46 | graphite `#141414` + white `#FFFFFF` | magenta |
| logo-av-04 | horizontal (1 line) | 425.2 × 141.73 | graphite + magenta | white / light |
| logo-av-05 | horizontal | 425.2 × 141.73 | white + magenta | dark (graphite) |
| logo-av-06 | horizontal | 425.2 × 141.73 | graphite + white | magenta |

The SVGs are Illustrator exports with generous padding around the wordmark; derive optimised
web versions (cropped viewBox, `currentColor` where appropriate) into `public/assets/` — never edit
the files in `source/`.

## Approved brand colours (taken from the SVG fills)

- Magenta (primary): `#E6007E`
- Graphite (ink): `#141414`
- White: `#FFFFFF`

These feed `config/autostvabaks.php` → `brand.*` and the `--asv-color-*` CSS tokens.
