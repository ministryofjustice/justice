// @ts-check

/**
 * Helpers for reaching the block editor canvas from editor scripts.
 *
 * Since WordPress 7.1 the post editor is always iframed, so the canvas (the
 * `.editor-styles-wrapper` element and everything the user is editing) lives
 * in the iframe's own document, not in the admin page's document where these
 * scripts run. Querying the global `document` for canvas elements finds nothing.
 *
 * @see https://make.wordpress.org/core/2026/08/03/iframed-editor-changes-in-wordpress-7-1/
 */

/**
 * How often, and for how long, to look for the canvas before giving up.
 * The canvas is rendered asynchronously, and later than the store is populated.
 */

const RETRY_MS = 300;
const MAX_ATTEMPTS = 100;

/**
 * Get the document that contains the block editor canvas.
 *
 * Falls back to the admin page's document for editors that are not iframed.
 *
 * @returns {Document}
 */

export const getCanvasDocument = () => {
  /** @type {HTMLIFrameElement | null} */
  const iframe = document.querySelector('iframe[name="editor-canvas"]');
  return iframe?.contentDocument ?? document;
};

/**
 * Get the canvas wrapper element, if it has been rendered.
 *
 * @returns {Element | null}
 */

export const getCanvasWrapper = () =>
  getCanvasDocument().querySelector(".editor-styles-wrapper");

/**
 * Resolve with the canvas wrapper element once it has been rendered.
 *
 * Resolves with null if the canvas does not appear within the retry budget,
 * e.g. because the user is in the code editor.
 *
 * @returns {Promise<Element | null>}
 */

export const whenCanvasReady = () =>
  new Promise((resolve) => {
    const check = (remaining) => {
      const wrapper = getCanvasWrapper();
      if (wrapper) {
        resolve(wrapper);
      } else if (remaining <= 0) {
        resolve(null);
      } else {
        setTimeout(() => check(remaining - 1), RETRY_MS);
      }
    };
    check(MAX_ATTEMPTS);
  });
