import jQuery from "jquery";

/**
 * Patch for PublishPress Revisions (revisionary) 3.9.1 – 3.9.2.
 *
 * In the block editor sidebar, after "Create Revision" (or "Schedule Revision") succeeds,
 * the plugin tries to fill in the href of the "Edit Revision" / "Preview" links with
 * selectors like `button.revision-created a.revision-edit`. Since 3.9.1 the markup is
 * `<a class="revision-edit"><button class="revision-created">…</button></a>`, so the
 * selectors match nothing and the links keep their `javascript:void(0)` placeholder.
 * Clicking one then opens a blank `pp_revisions_copy` tab.
 *
 * This resolves the intended URL from the plugin's own `rvyObjEdit` config and sets it
 * on the link just before the browser follows it. It is a no-op once the link already
 * has a real href, so it is safe to leave in place after the plugin is fixed.
 *
 * TODO: Remove this file (and its import in `src/js/admin/index.js`) once the fix has
 * landed in a released version of the plugin. Verify by upgrading, clicking
 * "Create Revision" then "Edit Revision" in the block editor sidebar, and checking that
 * the revision editor opens without this patch. Don't rely on the changelog alone:
 * the 4.0.0 changelog claims a fix, but at the time of writing the `development`
 * branch had reverted it (PR #2294 undid PR #2284).
 *
 * @see https://github.com/publishpress/publishpress-revisions/issues/2283
 * @see https://github.com/publishpress/publishpress-revisions/issues/2293
 */

jQuery(document).ready(function ($) {
  if (!window.rvyObjEdit) {
    return;
  }

  const placeholderHrefs = ["", "#", "javascript:void(0)"];

  /**
   * Map a link to the rvyObjEdit property holding its URL.
   *
   * @param {jQuery} $link
   * @returns {string|undefined}
   */
  const resolveUrl = ($link) => {
    const isEdit = $link.hasClass("revision-edit");
    const isScheduled = $link.closest("div.revision-scheduled").length > 0;

    if (isScheduled) {
      return isEdit
        ? window.rvyObjEdit.scheduledEditURL
        : window.rvyObjEdit.scheduledURL;
    }

    return isEdit
      ? window.rvyObjEdit.completedEditURL
      : window.rvyObjEdit.completedURL;
  };

  $(document).on(
    "click",
    "div.rvy-creation-ui a.revision-edit, div.rvy-creation-ui a.revision-preview",
    function () {
      const $link = $(this);
      const href = ($link.attr("href") || "").trim();

      if (!placeholderHrefs.includes(href)) {
        return;
      }

      const url = resolveUrl($link);

      if (!url) {
        return;
      }

      // Setting the href before the default action runs is enough: the browser reads
      // the attribute when it performs the navigation, so the (named) target tab
      // opens on the real URL instead of about:blank.
      $link.attr("href", url);
    },
  );
});
