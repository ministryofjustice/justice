import domReady from "@wordpress/dom-ready";
import { getCanvasDocument, whenCanvasReady } from "../../js/block-editor/canvas";

domReady(async () => {
    const checkImages = () => {
        const images = getCanvasDocument().querySelectorAll('.editor-styles-wrapper img');
        images.forEach(img => {
            const src = img.getAttribute('src');
            if (
                src &&
                !src.startsWith(document.location.origin) &&
                !img.classList.contains('external-image-warning')
            ) {
                img.classList.add('external-image-warning');
            }
        });
    };

    // Wait for the canvas to be rendered - it is iframed and loads after domReady.
    const target = await whenCanvasReady();
    if (!target) {
        return;
    }

    // Initial check
    checkImages();

    // Observe changes
    const observer = new MutationObserver(() => {
        checkImages();
    });

    observer.observe(target, {
        childList: true,
        subtree: true,
        attributes: true,
    });
});
