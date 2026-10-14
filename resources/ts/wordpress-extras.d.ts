/**
 * Ambient types for @wordpress packages that ship without `.d.ts` files.
 */
declare module '@wordpress/server-side-render' {
  import type { ComponentType } from 'react';

  interface ServerSideRenderProps {
    block: string;
    attributes?: Record<string, unknown>;
    EmptyResponsePlaceholder?: ComponentType;
  }

  const ServerSideRender: ComponentType<ServerSideRenderProps>;
  export default ServerSideRender;
}
