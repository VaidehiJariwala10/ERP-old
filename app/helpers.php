<?php

if (! function_exists('canUseBranches')) {
    /**
     * Determine whether the authenticated user's plan allows multiple branches.
     * Returns true if branch_limit > 1 (or null, meaning unlimited).
     * Returns false if branch_limit is exactly 1 (Starter plan).
     */
    function canUseBranches(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();
        $plan = $user->plan;

        // No plan assigned → allow by default (don't lock out unassigned users)
        if (! $plan) {
            return true;
        }

        // null means unlimited; any value > 1 allows branches
        return $plan->branch_limit === null || $plan->branch_limit > 1;
    }
}

if (! function_exists('image_path_base')) {
    /**
     * Base URL for public assets (always ends with /).
     */
    function image_path_base(): string
    {
        static $cached = null;

        if ($cached !== null) {
            return $cached;
        }

        try {
            if (function_exists('app') && app()->bound('config')) {
                $configured = config('app.image_path');
                if (is_string($configured) && $configured !== '') {
                    return $cached = rtrim($configured, '/').'/';
                }

                $appUrl = config('app.url');
                if (is_string($appUrl) && $appUrl !== '') {
                    return $cached = rtrim($appUrl, '/').'/';
                }
            }
        } catch (\Throwable) {
            // Application not booted yet.
        }

        $raw = env('ImagePath') ?: env('IMAGE_PATH') ?: env('APP_URL', '');

        return $cached = ($raw !== '' ? rtrim((string) $raw, '/').'/' : '/');
    }
}

if (! function_exists('image_path')) {
    /**
     * Build a full URL to a file under public/ (e.g. admin/assets/... or storage/...).
     */
    function image_path(?string $path = ''): string
    {
        $base = image_path_base();

        if ($path === null || $path === '') {
            return $base;
        }

        return $base.ltrim($path, '/');
    }
}

if (! function_exists('resolve_local_storage_path')) {
    /**
     * Resolve a stored relative path to an absolute local file path for PDFs/exports.
     */
    function resolve_local_storage_path(?string $relativePath): ?string
    {
        $relativePath = trim((string) $relativePath);
        if ($relativePath === '') {
            return null;
        }

        $relativePath = preg_replace('#^https?://[^/]+/#', '', $relativePath);
        $relativePath = ltrim($relativePath, '/');
        $relativePath = preg_replace('#^storage/#', '', $relativePath);

        $candidates = [
            storage_path('app/public/'.$relativePath),
            public_path('storage/'.$relativePath),
            public_path($relativePath),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}

if (! function_exists('local_image_to_base64')) {
    /**
     * Convert a local image file to a base64 data URI for DomPDF rendering.
     */
    function local_image_to_base64(?string $relativePath, ?string $fallbackPath = null): ?string
    {
        $filePath = resolve_local_storage_path($relativePath);

        if (! $filePath && $fallbackPath) {
            $filePath = resolve_local_storage_path($fallbackPath);
        }

        if (! $filePath) {
            return null;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeMap = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
        ];

        $mime = $mimeMap[$extension] ?? null;
        if (! $mime && function_exists('mime_content_type')) {
            $mime = @mime_content_type($filePath) ?: null;
        }
        $mime ??= 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($filePath));
    }
}
