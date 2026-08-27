import type { SyntheticEvent } from 'react';

/**
 * Some attachment rows point at files that no longer exist on disk (e.g. old
 * trials whose photos live on a server this environment's `legacy_uploads`
 * disk doesn't have a full copy of) — without this the browser renders its
 * own ugly broken-image icon. Swap it for a neutral placeholder instead.
 */
const MISSING_IMAGE_PLACEHOLDER =
    'data:image/svg+xml;utf8,' +
    encodeURIComponent(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>',
    );

export function handleAttachmentImageError(
    event: SyntheticEvent<HTMLImageElement>,
) {
    const img = event.currentTarget;
    img.onerror = null;
    img.src = MISSING_IMAGE_PLACEHOLDER;
    img.title = 'File tidak ditemukan di server';
    img.classList.add('p-6', 'opacity-40', 'object-contain');
    img.classList.remove('object-cover');
}
