import { Link, usePage } from '@inertiajs/react';
import {
    AlertTriangle,
    Bell,
    CheckCircle2,
    CircleCheckBig,
    ClipboardCheck,
    Clock,
    FileEdit,
    FilePlus2,
    FlaskConical,
    History,
    KeyRound,
    LayoutGrid,
    ListTree,
    Package,
    Search,
    Trash2,
    Users,
    XCircle,
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
import { index as approvalsIndex } from '@/routes/approvals';
import { index as reviewsIndex } from '@/routes/reviews';
import { create as createTrial, index as trialsIndex } from '@/routes/trials';
import type { Auth, NavGroup } from '@/types';

export function AppSidebar() {
    const { auth, canReviewTrials, canApproveTrials } = usePage<{
        auth: Auth;
        canReviewTrials: boolean;
        canApproveTrials: boolean;
    }>().props;
    const isSuperAdmin = auth.user.role === 'Super Admin';
    const isAdmin = auth.user.role === 'Admin' || isSuperAdmin;
    const isStaff = auth.user.role === 'Staff' || isAdmin;
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
            label: 'Trials',
            items: [
                ...(isStaff
                    ? [
                          {
                              title: 'New Trial',
                              href: createTrial(),
                              icon: FilePlus2,
                          },
                      ]
                    : []),
                {
                    title: 'Draft',
                    href: trialsIndex('draft'),
                    icon: FileEdit,
                },
                {
                    title: 'In Review',
                    href: trialsIndex('in-review'),
                    icon: Search,
                },
                {
                    title: 'Ready for Approval',
                    href: trialsIndex('waiting-approval'),
                    icon: Clock,
                },
                {
                    title: 'Approved',
                    href: trialsIndex('approved'),
                    icon: CheckCircle2,
                },
                {
                    title: 'Need Revision',
                    href: trialsIndex('need-revision'),
                    icon: AlertTriangle,
                },
                {
                    title: 'Rejected',
                    href: trialsIndex('rejected'),
                    icon: XCircle,
                },
            ],
        },
        {
            label: 'Reviews',
            items: canReviewTrials
                ? [
                      {
                          title: 'Review Queue',
                          href: reviewsIndex(),
                          icon: ClipboardCheck,
                      },
                  ]
                : [],
        },
        {
            label: 'Approval',
            items: canApproveTrials
                ? [
                      {
                          title: 'Approval Queue',
                          href: approvalsIndex(),
                          icon: CircleCheckBig,
                      },
                  ]
                : [],
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
