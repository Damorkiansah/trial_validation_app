import { Link, usePage } from '@inertiajs/react';
import {
    Bell,
    FlaskConical,
    History,
    KeyRound,
    LayoutGrid,
    ListTree,
    Package,
    Trash2,
    Users,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as accessRightsIndex } from '@/routes/admin/access-rights';
import { index as activityLogsIndex } from '@/routes/admin/activity-logs';
import { index as mastersIndex } from '@/routes/admin/masters';
import { index as notificationsIndex } from '@/routes/admin/notifications';
import { index as parametersIndex } from '@/routes/admin/parameters';
import { index as productsIndex } from '@/routes/admin/products';
import { index as trashIndex } from '@/routes/admin/trash';
import { index as usersIndex } from '@/routes/admin/users';
import type { Auth, NavGroup } from '@/types';

export function AppSidebar() {
    const { auth } = usePage<{ auth: Auth }>().props;
    const isSuperAdmin = auth.user.role === 'Super Admin';
    const isAdmin = auth.user.role === 'Admin' || isSuperAdmin;
    const canManageTemplates = isAdmin || auth.user.role === 'Staff';

    const navGroups: NavGroup[] = [
        {
            label: 'Overview',
            items: [
                {
                    title: 'Dashboard',
                    href: dashboard(),
                    icon: LayoutGrid,
                },
            ],
        },
        {
            label: 'Master Data',
            items: canManageTemplates
                ? [
                      {
                          title: 'Products',
                          href: productsIndex(),
                          icon: Package,
                      },
                      {
                          title: 'Parameters',
                          href: parametersIndex(),
                          icon: FlaskConical,
                      },
                      {
                          title: 'Masters',
                          href: mastersIndex(),
                          icon: ListTree,
                      },
                  ]
                : [],
        },
        {
            label: 'User Management',
            items: [
                ...(isAdmin
                    ? [
                          {
                              title: 'Users',
                              href: usersIndex(),
                              icon: Users,
                          },
                      ]
                    : []),
                ...(isSuperAdmin
                    ? [
                          {
                              title: 'Access Rights',
                              href: accessRightsIndex(),
                              icon: KeyRound,
                          },
                      ]
                    : []),
            ],
        },
        {
            label: 'System',
            items: isAdmin
                ? [
                      {
                          title: 'Notifications',
                          href: notificationsIndex(),
                          icon: Bell,
                      },
                      {
                          title: 'Trash',
                          href: trashIndex(),
                          icon: Trash2,
                      },
                      {
                          title: 'Activity Logs',
                          href: activityLogsIndex(),
                          icon: History,
                      },
                  ]
                : [],
        },
    ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain groups={navGroups} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
