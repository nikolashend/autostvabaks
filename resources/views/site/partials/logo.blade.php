{{--
    Approved Autostvabaks wordmark. The single implementation is the <x-asv-logo> component
    (resources/views/components/asv-logo.blade.php); this partial only keeps the old include working.
    variant: "dark" (light backgrounds) | "light" (dark backgrounds)
--}}
<x-asv-logo :variant="$variant ?? 'dark'" />
