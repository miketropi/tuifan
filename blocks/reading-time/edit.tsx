import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Editor preview for the computed reading time.
 *
 * The value comes from the server renderer, so the editor always shows the
 * number the front end will print for the current post context.
 */
export default function Edit() {
  const blockProps = useBlockProps();

  return (
    <div { ...blockProps }>
      <ServerSideRender block="tuifan/reading-time" />
    </div>
  );
}
