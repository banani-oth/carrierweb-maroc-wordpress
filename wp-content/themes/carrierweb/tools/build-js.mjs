/**
 * Assemble les modules de assets/src/js/ dans leur ordre numérique.
 * Usage : node tools/build-js.mjs
 */

import { readdir, readFile, writeFile, mkdir } from 'node:fs/promises';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const themeDir = path.join(path.dirname(fileURLToPath(import.meta.url)), '..');
const srcDir = path.join(themeDir, 'assets/src/js');
const outFile = path.join(themeDir, 'assets/dist/app.js');

const files = (await readdir(srcDir)).filter((f) => f.endsWith('.js')).sort();

if (!files.length) {
  console.error('Aucun fichier .js dans assets/src/js');
  process.exit(1);
}

const parts = [
  '/*!',
  ' * CarrierWeb Maroc — JavaScript du thème (FICHIER GÉNÉRÉ).',
  ' * Source : assets/src/js/ — régénérer avec `npm run build:js`.',
  ` * Modules : ${files.join(', ')}`,
  ' */',
  '',
];

for (const file of files) {
  parts.push(`/* ── ${file} ── */`);
  parts.push(await readFile(path.join(srcDir, file), 'utf8'));
  parts.push('');
}

await mkdir(path.dirname(outFile), { recursive: true });
const output = parts.join('\n').trimEnd() + '\n';
await writeFile(outFile, output, 'utf8');

const bytes = Buffer.byteLength(output, 'utf8');
console.log(`app.js — ${files.length} modules, ${(bytes / 1024).toFixed(1)} Ko`);
