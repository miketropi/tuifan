/**
 * tuifan/signal-visual — front-end enhancement.
 *
 * Adapts the reference ray-bending study (Schwarzschild null-geodesic
 * integration) into a local Three.js composition:
 *
 *   - full-screen plane + orthographic camera,
 *   - 112 integration steps, accretion disk between radius 3 and 11,
 *   - Doppler-like brightness asymmetry on the approaching side,
 *   - pointer-driven subtle orbit.
 *
 * The static SVG rendered by `render.php` stays visible until the first
 * successful draw, so WebGL failure, disabled JavaScript and reduced motion all
 * keep a real composition on screen.
 */

import * as THREE from 'three';

const STAGE_SELECTOR = '[data-tuifan-signal-stage]';
const FALLBACK_ACCENT = '#8a65a5';
const MAX_PIXEL_RATIO = 1.25;
const INTEGRATION_STEPS = 112;
const DISK_INNER_RADIUS = 3.0;
const DISK_OUTER_RADIUS = 11.0;

const cleanups = new Map<HTMLElement, () => void>();

/**
 * Resolve the theme accent from the preset CSS variables.
 *
 * Values are normalized through a canvas so any CSS color space (`oklch()`
 * included) becomes something Three.js can parse.
 */
function resolveAccentColor(): THREE.Color {
	const probe = document.createElement('canvas');
	const context = probe.getContext('2d');
	const styles = window.getComputedStyle(document.documentElement);
	const candidates = [
		styles.getPropertyValue('--wp--preset--color--accent-text'),
		styles.getPropertyValue('--wp--preset--color--primary'),
	];

	if (context) {
		for (const candidate of candidates) {
			const value = candidate.trim();

			if ('' === value) {
				continue;
			}

			context.fillStyle = '#000000';
			context.fillStyle = value;

			const normalized = String(context.fillStyle);

			if ('#000000' === normalized || '#000' === normalized) {
				continue;
			}

			if (normalized.startsWith('#') || normalized.startsWith('rgb')) {
				return new THREE.Color(normalized);
			}
		}
	}

	return new THREE.Color(FALLBACK_ACCENT);
}

const VERTEX_SHADER = `
varying vec2 vUv;

void main() {
	vUv = uv;
	gl_Position = vec4( position.xy, 0.0, 1.0 );
}
`;

const FRAGMENT_SHADER = `
precision highp float;

varying vec2 vUv;

uniform float uTime;
uniform float uAspect;
uniform vec2 uPointer;
uniform vec3 uAccent;

float hash( vec2 p ) {
	return fract( sin( dot( p, vec2( 127.1, 311.7 ) ) ) * 43758.5453 );
}

vec3 emission( vec3 p ) {
	float r = length( p.xz );

	if ( r < ${DISK_INNER_RADIUS.toFixed( 1 )} || r > ${DISK_OUTER_RADIUS.toFixed( 1 )} ) {
		return vec3( 0.0 );
	}

	float a = atan( p.z, p.x );

	// Keplerian differential rotation; dots are emissive texture, not stars.
	float orbit = uTime * 2.8 / pow( r, 1.5 );
	vec2 cell = vec2( ( a + orbit ) * 115.0, r * 45.0 );
	vec2 id = floor( cell );
	vec2 q = fract( cell ) - 0.5;
	float grain = hash( id );
	float dotLight = exp( -dot( q, q ) * 24.0 ) * smoothstep( 0.18, 0.9, grain );
	float eddy = 0.7 + 0.3 * sin( a * 13.0 + r * 8.0 - orbit * 19.0 );
	float inner = smoothstep( ${DISK_INNER_RADIUS.toFixed( 1 )}, 3.35, r ) * exp( -( r - ${DISK_INNER_RADIUS.toFixed( 1 )} ) * 0.32 ) * ( 1.0 - smoothstep( 8.0, ${DISK_OUTER_RADIUS.toFixed( 1 )}, r ) );

	// Approaching side brighter; artistic mapping, not true spectral color.
	float beam = pow( 1.0 / ( 1.0 + 0.38 * p.x / r ), 3.0 );
	float flux = inner * beam * eddy * ( 0.075 + dotLight * 1.5 );
	vec3 color = mix( uAccent, vec3( 0.94, 0.91, 1.0 ), exp( -( r - ${DISK_INNER_RADIUS.toFixed( 1 )} ) * 0.65 ) * 0.72 );

	return color * flux;
}

void main() {
	vec2 uv = ( vUv - 0.5 ) * vec2( uAspect, 1.0 );

	// Camera above a thin equatorial disk: nearly edge-on, but not exactly.
	vec3 ro = vec3( uPointer.x * 0.35, 5.0 + uPointer.y * 0.35, 17.0 );
	vec3 forward = normalize( -ro );
	vec3 right = normalize( cross( forward, vec3( 0.0, 1.0, 0.0 ) ) );
	vec3 up = cross( right, forward );
	vec3 v = normalize( forward + uv.x * right * 0.91 + uv.y * up * 0.91 );
	vec3 p = ro;
	vec3 light = vec3( 0.0 );
	vec3 angular = cross( ro, v );
	float b2 = dot( angular, angular );
	float captured = 0.0;

	// Adaptive finite integration approximates Schwarzschild null-geodesic geometry.
	for ( int i = 0; i < ${INTEGRATION_STEPS}; i++ ) {
		float r = length( p );

		if ( r < 1.015 ) {
			captured = 1.0;
			break;
		}

		if ( r > 24.0 && dot( p, v ) > 0.0 ) {
			break;
		}

		float h = clamp( ( r - 1.0 ) * 0.12, 0.055, 0.8 );
		vec3 acc = -1.5 * b2 * p / pow( r, 5.0 );
		vec3 next = p + v * h + 0.5 * acc * h * h;
		float nr = length( next );
		vec3 nextAcc = -1.5 * b2 * next / pow( max( nr, 1.0 ), 5.0 );
		v += 0.5 * ( acc + nextAcc ) * h;

		if ( p.y * next.y < 0.0 ) {
			float f = p.y / ( p.y - next.y );
			vec3 hit = mix( p, next, f );
			light += emission( hit ) * 0.85;
		}

		p = next;
	}

	// Multiple disk intersections yield lensed arcs and a narrow critical ring.
	light = vec3( 1.0 ) - exp( -light * 1.65 );
	float energy = max( light.r, max( light.g, light.b ) );
	float alpha = max( captured, smoothstep( 0.002, 0.12, energy ) );

	gl_FragColor = vec4( light, alpha );
}
`;

function initStage(stage: HTMLElement): void {
	if (cleanups.has(stage)) {
		return;
	}

	const host = stage.closest<HTMLElement>('.wp-block-tuifan-signal-visual');
	const motionEnabled = host ? host.dataset.tuifanMotion !== '0' : true;
	const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

	let renderer: THREE.WebGLRenderer;

	try {
		renderer = new THREE.WebGLRenderer({
			alpha: true,
			antialias: false,
			powerPreference: 'high-performance',
		});
	} catch {
		return;
	}

	renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, MAX_PIXEL_RATIO));
	renderer.setClearColor(0x000000, 0);

	const canvas = renderer.domElement;
	canvas.setAttribute('aria-hidden', 'true');
	canvas.setAttribute('tabindex', '-1');
	stage.prepend(canvas);

	const scene = new THREE.Scene();
	const camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
	const geometry = new THREE.PlaneGeometry(2, 2);
	const uniforms = {
		uTime: { value: 0 },
		uAspect: { value: 1 },
		uPointer: { value: new THREE.Vector2() },
		uAccent: { value: resolveAccentColor() },
	};
	const material = new THREE.ShaderMaterial({
		transparent: true,
		depthTest: false,
		depthWrite: false,
		uniforms,
		vertexShader: VERTEX_SHADER,
		fragmentShader: FRAGMENT_SHADER,
	});

	scene.add(new THREE.Mesh(geometry, material));

	const pointer = { x: 0, y: 0, tx: 0, ty: 0 };
	const hero = stage.closest<HTMLElement>('.tuifan-hero') ?? stage.parentElement ?? stage;

	let frame = 0;
	let last = 0;
	let clock = 0;
	let visible = true;
	let live = false;
	let disposed = false;
	let still = motionQuery.matches || !motionEnabled;

	function draw(): void {
		if (disposed) {
			return;
		}

		renderer.render(scene, camera);

		if (!live) {
			live = true;
			stage.classList.add('is-live');
		}
	}

	function resize(): void {
		const width = canvas.clientWidth;
		const height = canvas.clientHeight;

		if (!width || !height) {
			return;
		}

		renderer.setSize(width, height, false);
		uniforms.uAspect.value = width / height;
		draw();
	}

	function stop(): void {
		if (frame) {
			window.cancelAnimationFrame(frame);
			frame = 0;
		}
	}

	function resetPointer(): void {
		pointer.x = 0;
		pointer.y = 0;
		pointer.tx = 0;
		pointer.ty = 0;
		uniforms.uPointer.value.set(0, 0);
	}

	function tick(time: number): void {
		frame = window.requestAnimationFrame(tick);

		if (!visible || document.hidden) {
			last = 0;
			return;
		}

		const delta = last ? Math.min((time - last) / 1000, 0.04) : 1 / 60;
		last = time;
		clock += delta;

		const ease = 1 - Math.exp(-3 * delta);
		pointer.x += (pointer.tx - pointer.x) * ease;
		pointer.y += (pointer.ty - pointer.y) * ease;
		uniforms.uPointer.value.set(pointer.x, pointer.y);
		uniforms.uTime.value = clock;
		draw();
	}

	function syncLoop(): void {
		if (still) {
			stop();
			clock = 0;
			last = 0;
			uniforms.uTime.value = 0;
			resetPointer();
			draw();

			return;
		}

		if (visible && !document.hidden) {
			if (!frame) {
				last = 0;
				frame = window.requestAnimationFrame(tick);
			}

			return;
		}

		stop();
	}

	function onPointerMove(event: PointerEvent): void {
		if (still || 'touch' === event.pointerType) {
			return;
		}

		const rect = canvas.getBoundingClientRect();

		if (!rect.width || !rect.height) {
			return;
		}

		pointer.tx = ((event.clientX - rect.left) / rect.width - 0.5) * 2;
		pointer.ty = (0.5 - (event.clientY - rect.top) / rect.height) * 2;
	}

	function onPointerLeave(): void {
		pointer.tx = 0;
		pointer.ty = 0;
	}

	function onVisibilityChange(): void {
		syncLoop();
	}

	function onMotionChange(): void {
		still = motionQuery.matches || !motionEnabled;
		syncLoop();
	}

	const observer = new IntersectionObserver((entries) => {
		const entry = entries[0];
		visible = entry ? entry.isIntersecting : true;
		onPointerLeave();
		syncLoop();
	});
	const resizer = new ResizeObserver(() => resize());

	hero.addEventListener('pointermove', onPointerMove);
	hero.addEventListener('pointerleave', onPointerLeave);
	hero.addEventListener('pointercancel', onPointerLeave);
	document.addEventListener('visibilitychange', onVisibilityChange);
	motionQuery.addEventListener('change', onMotionChange);

	observer.observe(stage);
	resizer.observe(canvas);

	const dispose = (): void => {
		if (disposed) {
			return;
		}

		disposed = true;
		stop();
		observer.disconnect();
		resizer.disconnect();
		hero.removeEventListener('pointermove', onPointerMove);
		hero.removeEventListener('pointerleave', onPointerLeave);
		hero.removeEventListener('pointercancel', onPointerLeave);
		document.removeEventListener('visibilitychange', onVisibilityChange);
		motionQuery.removeEventListener('change', onMotionChange);
		geometry.dispose();
		material.dispose();
		renderer.dispose();

		if (typeof renderer.forceContextLoss === 'function') {
			renderer.forceContextLoss();
		}

		canvas.remove();
		stage.classList.remove('is-live');
		cleanups.delete(stage);
	};

	cleanups.set(stage, dispose);

	resize();
	syncLoop();
}

function boot(): void {
	const stages = document.querySelectorAll<HTMLElement>(STAGE_SELECTOR);

	stages.forEach((stage) => initStage(stage));
}

function teardown(): void {
	cleanups.forEach((dispose) => dispose());
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
	boot();
}

window.addEventListener('pagehide', teardown);
window.addEventListener('pageshow', (event) => {
	if (event.persisted) {
		boot();
	}
});
