import { Form, Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import AccessRightController from '@/actions/App/Http/Controllers/Admin/AccessRightController';
import { ConfirmDialog } from '@/components/confirm-dialog';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { PaginationFooter } from '@/components/pagination-footer';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { index as accessRightsIndex } from '@/routes/admin/access-rights';
import type { Paginated, User } from '@/types';

type ReviewerDepartment = {
    id: number;
    name: string;
    sort_order: number;
};

type DraftTrial = {
    id: number;
    trial_code: string;
    product_name: string;
    created_by: string | null;
};

type StaffUser = {
    id: number;
    name: string;
    email: string;
};

type DraftPermission = {
    id: number;
    granted_at: string;
    trial: {
        id: number;
        trial_code: string;
        product_name: string;
        created_by: string | null;
    } | null;
    user: { id: number; name: string; email: string } | null;
    granted_by: { id: number; name: string; email: string } | null;
};

type PageProps = {
    auth: { user: User };
    users: Paginated<User>;
    filters: { q: string };
    roleCategories: string[];
    reviewerDepartments: ReviewerDepartment[];
    draftTrials: DraftTrial[];
    staffUsers: StaffUser[];
    draftPermissions: DraftPermission[];
};

function EditRoleDialog({
    open,
    onOpenChange,
    editingUser,
    roleCategories,
}: {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    editingUser: User | null;
    roleCategories: string[];
}) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Edit Role & Department</DialogTitle>
                </DialogHeader>
                {editingUser && (
                    <Form
                        {...AccessRightController.updateRole.form(
                            editingUser.id,
                        )}
                        options={{ preserveScroll: true }}
                        onSuccess={() => onOpenChange(false)}
                        className="grid gap-4"
                    >
                        {({ processing, errors }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label>User</Label>
                                    <p className="text-sm text-muted-foreground">
                                        {editingUser.name} ({editingUser.email})
                                    </p>
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="role">Role</Label>
                                    <Select
                                        name="role"
                                        defaultValue={editingUser.role}
                                    >
                                        <SelectTrigger id="role">
                                            <SelectValue placeholder="Role" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {roleCategories.map((role) => (
                                                <SelectItem
                                                    key={role}
                                                    value={role}
                                                >
                                                    {role}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.role} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="department">
                                        Department
                                    </Label>
                                    <Input
                                        id="department"
                                        name="department"
                                        defaultValue={
                                            editingUser.department ?? ''
                                        }
                                        placeholder="Auto untuk role reviewer"
                                    />
                                    <InputError message={errors.department} />
                                </div>

                                <DialogFooter>
                                    <Button type="submit" disabled={processing}>
                                        Save Changes
                                    </Button>
                                </DialogFooter>
                            </>
                        )}
                    </Form>
                )}
            </DialogContent>
        </Dialog>
    );
}

function ReviewerDepartmentFormDialog({
    open,
    onOpenChange,
}: {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Add Reviewer Department</DialogTitle>
                </DialogHeader>
                <Form
                    {...AccessRightController.storeReviewerDepartment.form()}
                    options={{ preserveScroll: true }}
                    onSuccess={() => onOpenChange(false)}
                    resetOnSuccess={['name', 'sort_order']}
                    className="grid gap-4"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Nama Department</Label>
                                <Input id="name" name="name" required />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="sort_order">Sort</Label>
                                <Input
                                    id="sort_order"
                                    name="sort_order"
                                    type="number"
                                    defaultValue={0}
                                />
                                <InputError message={errors.sort_order} />
                            </div>

                            <DialogFooter>
                                <Button type="submit" disabled={processing}>
                                    Add Department
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}

function GrantPermissionFormDialog({
    open,
    onOpenChange,
    draftTrials,
    staffUsers,
}: {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    draftTrials: DraftTrial[];
    staffUsers: StaffUser[];
}) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Grant Draft Report Edit Access</DialogTitle>
                </DialogHeader>
                <Form
                    {...AccessRightController.grantPermission.form()}
                    options={{ preserveScroll: true }}
                    onSuccess={() => onOpenChange(false)}
                    className="grid gap-4"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="trial_id">Draft Report</Label>
                                <Select name="trial_id">
                                    <SelectTrigger id="trial_id">
                                        <SelectValue placeholder="Pilih Draft report" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {draftTrials.map((trial) => (
                                            <SelectItem
                                                key={trial.id}
                                                value={String(trial.id)}
                                            >
                                                {trial.trial_code} —{' '}
                                                {trial.product_name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.trial_id} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="user_id">Staff User</Label>
                                <Select name="user_id">
                                    <SelectTrigger id="user_id">
                                        <SelectValue placeholder="Pilih Staff" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {staffUsers.map((staff) => (
                                            <SelectItem
                                                key={staff.id}
                                                value={String(staff.id)}
                                            >
                                                {staff.name} ({staff.email})
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.user_id} />
                            </div>

                            <DialogFooter>
                                <Button type="submit" disabled={processing}>
                                    Grant Access
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}

export default function AdminAccessRightsIndex({
    auth,
    users,
    filters,
    roleCategories,
    reviewerDepartments,
    draftTrials,
    staffUsers,
    draftPermissions,
}: PageProps) {
    const [search, setSearch] = useState(filters.q);
    const [editingUser, setEditingUser] = useState<User | null>(null);
    const [departmentDialogOpen, setDepartmentDialogOpen] = useState(false);
    const [permissionDialogOpen, setPermissionDialogOpen] = useState(false);

    function submitSearch(e: FormEvent) {
        e.preventDefault();
        router.get(
            accessRightsIndex().url,
            { q: search },
            { preserveState: true },
        );
    }

    return (
        <>
            <Head title="Access Rights" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Access Rights"
                    description="Super Admin only: reassign role/department, kelola master reviewer department, dan izin edit Draft report."
                />

                <EditRoleDialog
                    open={editingUser !== null}
                    onOpenChange={(open) => {
                        if (!open) {
                            setEditingUser(null);
                        }
                    }}
                    editingUser={editingUser}
                    roleCategories={roleCategories}
                />

                <Card>
                    <CardHeader>
                        <CardTitle>User Role & Department</CardTitle>
                        <form
                            onSubmit={submitSearch}
                            className="flex items-end gap-2 pt-2"
                        >
                            <div className="grid gap-2 sm:max-w-sm">
                                <Label htmlFor="q">Search User</Label>
                                <Input
                                    id="q"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="Nama, email, role, department"
                                />
                            </div>
                            <Button type="submit" variant="secondary">
                                Search
                            </Button>
                        </form>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Role</TableHead>
                                    <TableHead>Dept</TableHead>
                                    <TableHead>Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {users.data.map((usr) => (
                                    <TableRow key={usr.id}>
                                        <TableCell>{usr.name}</TableCell>
                                        <TableCell>{usr.email}</TableCell>
                                        <TableCell>{usr.role}</TableCell>
                                        <TableCell>{usr.department}</TableCell>
                                        <TableCell>
                                            {usr.id !== auth.user.id && (
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        setEditingUser(usr)
                                                    }
                                                >
                                                    Edit
                                                </Button>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {users.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={5}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada user.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={accessRightsIndex().url}
                            query={{ q: filters.q }}
                            currentPage={users.current_page}
                            lastPage={users.last_page}
                            total={users.total}
                            itemLabel="users"
                        />
                    </CardContent>
                </Card>

                <ReviewerDepartmentFormDialog
                    open={departmentDialogOpen}
                    onOpenChange={setDepartmentDialogOpen}
                />

                <Card>
                    <CardHeader className="flex-row items-center justify-between">
                        <CardTitle>Reviewer Department Master</CardTitle>
                        <Button
                            type="button"
                            onClick={() => setDepartmentDialogOpen(true)}
                        >
                            Add Department
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Sort</TableHead>
                                    <TableHead>Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {reviewerDepartments.map((department) => (
                                    <TableRow key={department.id}>
                                        <TableCell>{department.name}</TableCell>
                                        <TableCell>
                                            {department.sort_order}
                                        </TableCell>
                                        <TableCell>
                                            <ConfirmDialog
                                                trigger={
                                                    <Button
                                                        variant="destructive"
                                                        size="sm"
                                                    >
                                                        Delete
                                                    </Button>
                                                }
                                                title="Delete this reviewer department?"
                                                description={`${department.name} will be removed from the reviewer department list.`}
                                                confirmLabel="Delete"
                                                formProps={AccessRightController.destroyReviewerDepartment.form(
                                                    department.id,
                                                )}
                                            />
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {reviewerDepartments.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={3}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada reviewer department
                                            custom.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <GrantPermissionFormDialog
                    open={permissionDialogOpen}
                    onOpenChange={setPermissionDialogOpen}
                    draftTrials={draftTrials}
                    staffUsers={staffUsers}
                />

                <Card>
                    <CardHeader className="flex-row items-center justify-between">
                        <CardTitle>Draft Report Edit Permission</CardTitle>
                        <Button
                            type="button"
                            onClick={() => setPermissionDialogOpen(true)}
                        >
                            Grant Access
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Draft Report</TableHead>
                                    <TableHead>Owner</TableHead>
                                    <TableHead>Granted To</TableHead>
                                    <TableHead>Granted By</TableHead>
                                    <TableHead>Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {draftPermissions.map((permission) => (
                                    <TableRow key={permission.id}>
                                        <TableCell>
                                            {permission.trial?.trial_code} —{' '}
                                            {permission.trial?.product_name}
                                        </TableCell>
                                        <TableCell>
                                            {permission.trial?.created_by}
                                        </TableCell>
                                        <TableCell>
                                            {permission.user?.name} (
                                            {permission.user?.email})
                                        </TableCell>
                                        <TableCell>
                                            {permission.granted_by?.name ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            <ConfirmDialog
                                                trigger={
                                                    <Button
                                                        variant="destructive"
                                                        size="sm"
                                                    >
                                                        Revoke
                                                    </Button>
                                                }
                                                title="Revoke this edit permission?"
                                                description={`${permission.user?.name} won't be able to edit ${permission.trial?.trial_code} anymore.`}
                                                confirmLabel="Revoke"
                                                formProps={AccessRightController.revokePermission.form(
                                                    permission.id,
                                                )}
                                            />
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {draftPermissions.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={5}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada izin edit Draft report
                                            yang aktif.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AdminAccessRightsIndex.layout = {
    breadcrumbs: [{ title: 'Access Rights', href: accessRightsIndex() }],
};
