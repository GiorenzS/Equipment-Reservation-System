<?php

if (! function_exists('equipment_has_custom_image')) {
    /**
     * Determine if an equipment item has a non-empty image specified.
     */
    function equipment_has_custom_image(?string $image): bool
    {
        return $image !== null && trim($image) !== '';
    }
}

if (! function_exists('equipment_image_url')) {
    /**
     * Resolve the public URL for an equipment image, or provide a sleek SVG placeholder.
     */
    function equipment_image_url(?string $image): string
    {
        if (! equipment_has_custom_image($image)) {
            return equipment_default_placeholder_svg();
        }

        $image = trim((string) $image);

        // If it is already an absolute URL (e.g., http:// or https://)
        if (preg_match('#^https?://#i', $image)) {
            return $image;
        }

        // If it starts with 'uploads/' or 'public/uploads/'
        if (str_starts_with($image, 'uploads/')) {
            return base_url($image);
        }

        if (str_starts_with($image, 'public/uploads/')) {
            return base_url(substr($image, 7));
        }

        // Otherwise assume it's stored inside uploads/equipment/
        return base_url('uploads/equipment/' . ltrim($image, '/'));
    }
}

if (! function_exists('equipment_default_placeholder_svg')) {
    /**
     * Generates a modern SVG data URI placeholder for equipment without an image.
     */
    function equipment_default_placeholder_svg(): string
    {
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 450" width="100%" height="100%">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0f172a"/>
      <stop offset="100%" stop-color="#1e293b"/>
    </linearGradient>
    <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#38bdf8"/>
      <stop offset="100%" stop-color="#6366f1"/>
    </linearGradient>
  </defs>
  <rect width="600" height="450" fill="url(#bgGrad)"/>
  <circle cx="300" cy="190" r="70" fill="#334155" opacity="0.4"/>
  <g transform="translate(260, 150)" stroke="url(#accentGrad)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round">
    <rect x="0" y="10" width="80" height="56" rx="8"/>
    <circle cx="40" cy="38" r="14"/>
    <path d="M22 10V4a2 2 0 0 1 2-2h32a2 2 0 0 1 2 2v6"/>
    <circle cx="64" cy="22" r="2" fill="#38bdf8"/>
  </g>
  <text x="300" y="290" text-anchor="middle" fill="#94a3b8" font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="600" letter-spacing="1">NO IMAGE AVAILABLE</text>
  <text x="300" y="315" text-anchor="middle" fill="#64748b" font-family="system-ui, -apple-system, sans-serif" font-size="13">Equipment Tracker</text>
</svg>
SVG;

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }
}
