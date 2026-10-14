import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Editor preview for the archive search + filter controls.
 *
 * The markup is generated on the server so the editor never drifts from the
 * front end: the form posts `s` and `post_type=post` back to the site root.
 */
export default function Edit() {
  const blockProps = useBlockProps();

  return (
    <div { ...blockProps }>
      <ServerSideRender block="tuifan/archive-controls" />
    </div>
  );
}
