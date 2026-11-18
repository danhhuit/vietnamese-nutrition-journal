import { context, build } from 'esbuild';
import { mkdirSync, readFileSync, rmSync, watch as watchFiles, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';
import postcss from 'postcss';
import tailwindcss from '@tailwindcss/postcss';

const watch = process.argv.includes('--watch');
const buildDirectory = resolve('public/build');
const assetsDirectory = resolve('public/build/assets');
const cssInput = resolve('resources/css/app.css');
const cssOutput = resolve('public/build/assets/app.css');

rmSync(buildDirectory, { recursive: true, force: true });
mkdirSync(assetsDirectory, { recursive: true });

writeFileSync(resolve(buildDirectory, 'manifest.json'), JSON.stringify({
    'resources/css/app.css': {
        file: 'assets/app.css',
        src: 'resources/css/app.css',
        isEntry: true,
    },
    'resources/js/app.js': {
        file: 'assets/app.js',
        name: 'app',
        src: 'resources/js/app.js',
        isEntry: true,
    },
}, null, 2));

const javascriptOptions = {
    entryPoints: [resolve('resources/js/app.js')],
    bundle: true,
    minify: !watch,
    sourcemap: watch,
    outfile: resolve('public/build/assets/app.js'),
};

async function buildCss() {
    const result = await postcss([tailwindcss({ optimize: !watch })]).process(
        readFileSync(cssInput, 'utf8'),
        { from: cssInput, to: cssOutput },
    );
    writeFileSync(cssOutput, result.css);
}

if (!watch) {
    await buildCss();
    await build(javascriptOptions);
    console.log('Frontend assets built in public/build.');
    process.exit(0);
}

await buildCss();
const javascript = await context(javascriptOptions);
await javascript.watch();
console.log('Watching CSS, JavaScript, and Blade sources...');

let timer;
const resourceWatcher = watchFiles(resolve('resources'), { recursive: true }, () => {
    clearTimeout(timer);
    timer = setTimeout(() => buildCss().catch(console.error), 100);
});

const stop = async () => {
    resourceWatcher.close();
    await javascript.dispose();
    process.exit(0);
};

process.on('SIGINT', stop);
process.on('SIGTERM', stop);
