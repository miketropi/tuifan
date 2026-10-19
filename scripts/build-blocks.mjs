/**
 * build-blocks.mjs — Tuifan child theme build.
 *
 * Convention (adapted from the parent Nextora theme's `scripts/build-blocks.mjs`):
 *
 *   blocks/<slug>/index.tsx  → blocks/<slug>/index.js + index.asset.php
 *   blocks/<slug>/view.ts    → blocks/<slug>/view.js  + view.asset.php
 *   resources/ts/site.ts     → assets/js/site.js
 *
 * Differences from the parent build, on purpose:
 *   - JSX compiles to `window.wp.element.createElement` / `.Fragment`, so React
 *     is never bundled into a block.
 *   - `index.asset.php` lists only the `@wordpress/*` packages the block's
 *     sources actually import.
 *   - Versions are content hashes of the emitted file, not timestamps.
 *
 * Usage: node ./scripts/build-blocks.mjs [--watch]
 */

import esbuild from 'esbuild';
import { glob } from 'glob';
import crypto from 'crypto';
import fs from 'fs';
import path from 'path';

const isWatch = process.argv.includes('--watch');

/** WordPress packages exposed as `window.wp.*` globals by WordPress core. */
const WP_PACKAGES = [
	'@wordpress/blocks',
	'@wordpress/block-editor',
	'@wordpress/server-side-render',
	'@wordpress/components',
	'@wordpress/element',
	'@wordpress/i18n',
	'@wordpress/hooks',
	'@wordpress/data',
	'@wordpress/core-data',
	'@wordpress/compose',
	'@wordpress/primitives',
	'@wordpress/blob',
	'@wordpress/notices',
	'@wordpress/plugins',
];

function toWpHandle(pkg) {
	return pkg.replace('@wordpress/', 'wp-').replace(/\//g, '-');
}

function contentVersion(file) {
	return crypto.createHash('sha1').update(fs.readFileSync(file)).digest('hex').slice(0, 12);
}

function writeAssetFile(jsFile, dependencies) {
	const assetFile = jsFile.replace(/\.js$/, '.asset.php');
	const deps = dependencies.map((d) => `'${d}'`).join(', ');
	const php = `<?php return array( 'dependencies' => array( ${deps} ), 'version' => '${contentVersion(jsFile)}' );\n`;

	fs.writeFileSync(assetFile, php, 'utf8');
}

/**
 * The `@wordpress/*` packages actually imported by a block's sources.
 *
 * @param {string} blockDir Block directory.
 * @returns {string[]} Script handles, sorted.
 */
function collectWpDependencies(blockDir) {
	const found = new Set();
	const sources = glob.sync(`${blockDir}/**/*.{ts,tsx}`, { nodir: true });

	for (const file of sources) {
		const source = fs.readFileSync(file, 'utf8');
		const patterns = [
			/from\s+['"](@wordpress\/[^'"]+)['"]/g,
			/import\s*\(\s*['"](@wordpress\/[^'"]+)['"]\s*\)/g,
			/require\(\s*['"](@wordpress\/[^'"]+)['"]\s*\)/g,
		];

		for (const pattern of patterns) {
			for (const match of source.matchAll(pattern)) {
				if (WP_PACKAGES.includes(match[1])) {
					found.add(toWpHandle(match[1]));
				}
			}
		}
	}

	return [...found].sort();
}

/** Maps `@wordpress/*` imports to the already-loaded `window.wp.*` globals. */
const wpExternalsPlugin = {
	name: 'wp-externals',
	setup(build) {
		build.onResolve({ filter: /^@wordpress\// }, (args) => ({
			path: args.path,
			namespace: 'wp-external',
		}));

		build.onLoad({ filter: /.*/, namespace: 'wp-external' }, (args) => {
			const globalName = args.path
				.replace('@wordpress/', '')
				.replace(/-([a-z])/g, (_, letter) => letter.toUpperCase());

			return {
				contents: `module.exports = window.wp['${globalName}'];`,
				loader: 'js',
			};
		});
	},
};

const editorEntryPoints = await glob('./blocks/*/index.{ts,tsx}');
const viewEntryPoints = await glob('./blocks/*/view.ts');
const siteEntryPoint = './resources/ts/site.ts';
const editorCanvasEntryPoint = './resources/ts/editor-canvas.ts';

if (editorEntryPoints.length === 0) {
	console.warn('No block entry points found at ./blocks/*/index.{ts,tsx}');
	process.exit(0);
}

/** @type {import('esbuild').BuildOptions} */
const editorBuildOptions = {
	entryPoints: editorEntryPoints,
	bundle: true,
	format: 'iife',
	platform: 'browser',
	target: 'es2020',
	/*
	 * `tsconfig.json` is typed for `tsc` (`jsx: react-jsx`), which would make
	 * esbuild pull in `react/jsx-runtime` and bundle a second React. This raw
	 * config overrides the on-disk file so JSX compiles to the WordPress global
	 * factory instead (one small bundle, no duplicate React).
	 */
	tsconfigRaw: {
		compilerOptions: {
			jsx: 'react',
			jsxFactory: 'window.wp.element.createElement',
			jsxFragmentFactory: 'window.wp.element.Fragment',
		},
	},
	sourcemap: isWatch ? 'inline' : false,
	minify: !isWatch,
	logLevel: isWatch ? 'warning' : 'info',
	plugins: [wpExternalsPlugin],
	define: {
		'process.env.NODE_ENV': isWatch ? '"development"' : '"production"',
	},
	outdir: '.',
	outbase: '.',
};

/** Front-end view bundles (local dependencies such as Three) — no WP externals. */
const viewBuildOptions = {
	entryPoints: viewEntryPoints,
	bundle: true,
	format: 'iife',
	platform: 'browser',
	target: 'es2020',
	sourcemap: isWatch ? 'inline' : false,
	minify: !isWatch,
	logLevel: isWatch ? 'warning' : 'info',
	outdir: '.',
	outbase: '.',
};

const siteBuildOptions = {
	entryPoints: [siteEntryPoint],
	bundle: true,
	format: 'iife',
	platform: 'browser',
	target: 'es2020',
	sourcemap: isWatch ? 'inline' : false,
	minify: !isWatch,
	logLevel: isWatch ? 'warning' : 'info',
	outfile: './assets/js/site.js',
};

/** Admin-only bundle: mirrors the selected page template onto the editor canvas. */
const editorCanvasBuildOptions = {
	entryPoints: [editorCanvasEntryPoint],
	bundle: true,
	format: 'iife',
	platform: 'browser',
	target: 'es2020',
	sourcemap: isWatch ? 'inline' : false,
	minify: !isWatch,
	logLevel: isWatch ? 'warning' : 'info',
	outfile: './assets/js/editor-canvas.js',
};

function writeEditorAssets() {
	editorEntryPoints.forEach((entry) => {
		const jsFile = entry.replace(/\.tsx?$/, '.js');

		if (!fs.existsSync(jsFile)) {
			return;
		}

		writeAssetFile(jsFile, collectWpDependencies(path.dirname(entry)));
		console.log(`  ✓ ${jsFile}`);
		console.log(`  ✓ ${jsFile.replace(/\.js$/, '.asset.php')}`);
	});
}

function writeViewAssets() {
	viewEntryPoints.forEach((entry) => {
		const jsFile = entry.replace(/\.ts$/, '.js');

		if (!fs.existsSync(jsFile)) {
			return;
		}

		writeAssetFile(jsFile, []);
		console.log(`  ✓ ${jsFile}`);
		console.log(`  ✓ ${jsFile.replace(/\.js$/, '.asset.php')}`);
	});
}

if (isWatch) {
	/** Rewrite `*.asset.php` hashes after every watch rebuild. */
	const assetWriterPlugin = {
		name: 'asset-writer',
		setup(build) {
			build.onEnd((result) => {
				if (result.errors.length > 0) {
					return;
				}

				writeEditorAssets();
				writeViewAssets();

				if (fs.existsSync('./assets/js/site.js')) {
					console.log(`  ✓ assets/js/site.js — ${new Date().toTimeString().slice(0, 8)}`);
				}
			});
		},
	};

	const editorContext = await esbuild.context({
		...editorBuildOptions,
		plugins: [...editorBuildOptions.plugins, assetWriterPlugin],
	});
	const viewContext = await esbuild.context({ ...viewBuildOptions, plugins: [assetWriterPlugin] });
	const siteContext = await esbuild.context({ ...siteBuildOptions, plugins: [assetWriterPlugin] });
	const editorCanvasContext = await esbuild.context({
		...editorCanvasBuildOptions,
		plugins: [assetWriterPlugin],
	});

	await Promise.all([
		editorContext.watch(),
		viewContext.watch(),
		siteContext.watch(),
		editorCanvasContext.watch(),
	]);

	console.log(
		`Watching ${editorEntryPoints.length} block(s), ${viewEntryPoints.length} view bundle(s), resources/ts/site.ts and resources/ts/editor-canvas.ts — Ctrl+C to stop`
	);
} else {
	const result = await esbuild.build(editorBuildOptions);

	if (result.errors.length > 0) {
		console.error('Editor build failed:', result.errors);
		process.exit(1);
	}

	writeEditorAssets();

	if (viewEntryPoints.length > 0) {
		const viewResult = await esbuild.build(viewBuildOptions);

		if (viewResult.errors.length > 0) {
			console.error('View build failed:', viewResult.errors);
			process.exit(1);
		}

		writeViewAssets();
	}

	if (fs.existsSync(siteEntryPoint)) {
		await esbuild.build(siteBuildOptions);
		console.log('  ✓ assets/js/site.js');
	}

	if (fs.existsSync(editorCanvasEntryPoint)) {
		await esbuild.build(editorCanvasBuildOptions);
		console.log('  ✓ assets/js/editor-canvas.js');
	}

	console.log('\nBuild complete.');
}
