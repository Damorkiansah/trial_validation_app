import { Link, usePage } from '@inertiajs/react';
import {
    BookOpen,
    FlaskConical,
    FolderGit2,
    KeyRound,
    LayoutGrid,
    Package,
    Users,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
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
import { index as parametersIndex } from '@/routes/admin/parameters';
import { index as productsIndex } from '@/routes/admin/products';
import { index as usersIndex } from '@/routes/admin/users';
import type { Auth, NavItem } from '@/types';

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    const { auth } = usePage<{ auth: Auth }>().props;
    const isSuperAdmin = auth.user.role === 'Super Admin';
    const isAdmin = auth.user.role === 'Admin' || isSuperAdmin;
    const canManageTemplates = isAdmin || auth.user.role === 'Staff';

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
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
        ...(canManageTemplates
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
              ]
            : []),
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
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
