const VALID_PROTOCOLS = new Set(['http:', 'https:', 'mailto:', 'tel:']);
const CONTROL_CHARACTERS = /[\u0000-\u001f\u007f]/;

/**
 * Return an author-provided section URL only when it is safe to serialize.
 *
 * Relative paths and fragments are parsed against a constant base so block
 * serialization is deterministic in every editor origin.
 */
export function getSafeSectionUrl(value: string): string {
  const trimmed = value.trim();

  if (!trimmed || CONTROL_CHARACTERS.test(trimmed) || trimmed.includes('\\')) {
    return '';
  }

  try {
    const parsed = new URL(trimmed, 'https://example.invalid/');

    return VALID_PROTOCOLS.has(parsed.protocol) ? trimmed : '';
  } catch {
    return '';
  }
}
