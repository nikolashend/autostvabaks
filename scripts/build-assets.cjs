// Production assets: minified twins of the readable sources (deterministic, esbuild).
//   NODE_PATH=C:/Users/nikol/Documents/projects/motionmotors/node_modules node scripts/build-assets.cjs
// Writes public/assets/css/autostvabaks.min.css and public/assets/js/autostvabaks.min.js.
// Site::asset() serves the .min twin only while it is not older than its source, so
// re-run this after every edit of autostvabaks.css / autostvabaks.js (and commit both).
const path = require('path');
const fs = require('fs');
const esbuild = require('esbuild');

const root = path.resolve(__dirname, '..', 'public', 'assets');
const targets = [
  { src: path.join(root, 'css', 'autostvabaks.css'), out: path.join(root, 'css', 'autostvabaks.min.css'), loader: 'css' },
  { src: path.join(root, 'js', 'autostvabaks.js'), out: path.join(root, 'js', 'autostvabaks.min.js'), loader: 'js' },
];

for (const t of targets) {
  const source = fs.readFileSync(t.src, 'utf8');
  const result = esbuild.transformSync(source, {
    loader: t.loader,
    minify: true,
    charset: 'utf8',
    target: t.loader === 'js' ? ['es2017'] : undefined,
    legalComments: 'none',
  });
  let code = result.code;
  if (t.loader === 'css' && !code.startsWith('@charset')) code = '@charset "UTF-8";' + code;
  fs.writeFileSync(t.out, code);
  // keep the twin at least as new as the source (git checkout may order mtimes arbitrarily)
  const now = new Date();
  fs.utimesSync(t.out, now, now);
  console.log(`${path.relative(process.cwd(), t.out)}: ${source.length} -> ${code.length} bytes`);
}
