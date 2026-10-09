// @ts-check

import { addFilter } from "@wordpress/hooks";

/**
 * Removes the user mentions autocompleter, the list of people that typing @
 * opens in rich text such as a paragraph. Keeps the other completers (e.g. links).
 *
 * @param {Array<{name: string}>} completers
 * @returns {Array<{name: string}>}
 */

const removeUserMentions = (completers) =>
  completers.filter((completer) => completer.name !== "users");

// Priority 20: core adds the users completer at the default priority (10) from
// wp-editor, which may load after this script, so run after it.
addFilter(
  "editor.Autocomplete.completers",
  "moj/remove-user-mentions",
  removeUserMentions,
  20,
);
