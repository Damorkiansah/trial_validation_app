import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import type { BreadcrumbItem } from '@/types';

export default function AppLayout({
    breadcrumbs = [],
    children,
}: {
    breadcrumbs?: BreadcrumbItem[];
    children: React.ReactNode;
}) {
    // Toasts are wired up once, globally, by <Toaster /> (resources/js/components/ui/sonner.tsx,
    // mounted unconditionally in app.tsx) — calling useFlashToast() here too
    // double-fired every toast (each Inertia 'flash' event triggered both
    // listeners), showing every flash message twice stacked.

    return (
        <AppLayoutTemplate breadcrumbs={breadcrumbs}>
            {children}
        </AppLayoutTemplate>
    );
}
