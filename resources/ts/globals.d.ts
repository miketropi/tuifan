/**
 * Tuifan globals provided by PHP or by the editor runtime.
 */
interface TuifanSiteStrings {
  copy: string;
  copied: string;
  copyFailed: string;
  codeLabel: string;
}

/**
 * Minimal surface of the block editor data store used by the canvas parity
 * script; the editor packages ship no ambient types in this theme.
 */
interface WpEditorStoreSelect {
  getEditedPostAttribute?: (attribute: string) => unknown;
}

interface WpData {
  select: (store: string) => WpEditorStoreSelect;
  subscribe?: (listener: () => void) => void;
}

interface Window {
  tuifanSite?: Partial<TuifanSiteStrings>;
  wp?: {
    data?: WpData;
  };
}
