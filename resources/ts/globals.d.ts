/**
 * Server-provided globals used by the Tuifan front-end script.
 */
interface TuifanSiteStrings {
  copy: string;
  copied: string;
  copyFailed: string;
  codeLabel: string;
}

interface Window {
  tuifanSite?: Partial<TuifanSiteStrings>;
}
