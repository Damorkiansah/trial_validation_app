import { Form, Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import AccessRightController from '@/actions/App/Http/Controllers/Admin/AccessRightController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
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
import { index as accessRightsIndex } from '@/routes/admin/access-rights';
import type { User } from '@/types';

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
};

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
    editUser: User | null;
    roleCategories: string[];
    reviewerDepartments: ReviewerDepartment[];
    draftTrials: DraftTrial[];
    staffUsers: StaffUser[];
    draftPermissions: DraftPermission[];
};

function DeleteReviewerDepartmentButton({
    department,
}: {
    department: ReviewerDepartment;
}) {
    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="destructive" size="sm">
                    Delete
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Delete this reviewer department?</DialogTitle>
                <DialogDescription>
                    {department.name} will be removed from the reviewer
                    department list.
                </DialogDescription>
                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Form
                        {...AccessRightController.destroyReviewerDepartment.form(
                            department.id,
                        )}
                        options={{ preserveScroll: true }}
                    >
                        {({ processing }) => (
                            <Button
                                type="submit"
                                variant="destructive"
                                disabled={processing}
                            >
                                Delete
                            </Button>
                        )}
                    </Form>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

function RevokePermissionButton({
    permission,
}: {
    permission: DraftPermission;
}) {
    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="destructive" size="sm">
                    Revoke
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Revoke this edit permission?</DialogTitle>
                <DialogDescription>
                    {permission.user?.name} won&apos;t be able to edit{' '}
                    {permission.trial?.trial_code} anymore.
                </DialogDescription>
                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Form
                        {...AccessRightController.revokePermission.form(
                            permission.id,
                        )}
                        options={{ preserveScroll: true }}
                    >
                        {({ processing }) => (
                            <Button
                                type="submit"
                                variant="destructive"
                                disabled={processing}
                            >
                                Revoke
                            </Button>
                        )}
                    </Form>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

export default function AdminAccessRightsIndex({
    auth,
    users,
    filters,
    editUser,
    roleCategories,
    reviewerDepartments,
    draftTrials,
    staffUsers,
    draftPermissions,
}: PageProps) {
    const [search, setSearch] = useState(filters.q);

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

                <section className="space-y-4 rounded-lg border p-4">
                    <h2 className="text-lg font-semibold">
                        User Role & Department
                    </h2>

                    {editUser && (
                        <Form
                            {...AccessRightController.updateRole.form(
                                editUser.id,
                            )}
                            options={{ preserveScroll: true }}
                            className="grid gap-4 rounded-lg border p-4 sm:grid-cols-2 lg:grid-cols-4"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <div className="grid gap-2 sm:col-span-2 lg:col-span-4">
                                        <Label>Editing</Label>
                                        <p className="text-sm text-muted-foreground">
                                            {editUser.name} ({editUser.email})
                                        </p>
                                        <InputError message={errors.role} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="role">Role</Label>
                                        <Select
                                            name="role"
                                            defaultValue={editUser.role}
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
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="department">
                                            Department
                                        </Label>
                                        <Input
                                            id="department"
                                            name="department"
                                            defaultValue={
                                                editUser.department ?? ''
                                            }
                                            placeholder="Auto untuk role reviewer"
                                        />
                                        <InputError
                                            message={errors.department}
                                        />
                                    </div>

                                    <div className="flex items-end gap-2">
                                        <Button
                                            type="submit"
                                            disabled={processing}
                                        >
                                            Update Role
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="secondary"
                                            onClick={() =>
                                                router.get(
                                                    accessRightsIndex().url,
                                                )
                                            }
                                        >
                                            Cancel
                                        </Button>
                                    </div>
                                </>
                            )}
                        </Form>
                    )}

                    <form
                        onSubmit={submitSearch}
                        className="flex items-end gap-2"
                    >
                        <div className="grid gap-2">
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

                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b text-left">
                                    <th className="p-2">Name</th>
                                    <th className="p-2">Email</th>
                                    <th className="p-2">Role</th>
                                    <th className="p-2">Dept</th>
                                    <th className="p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.data.map((usr) => (
                                    <tr key={usr.id} className="border-b">
                                        <td className="p-2">{usr.name}</td>
                                        <td className="p-2">{usr.email}</td>
                                        <td className="p-2">{usr.role}</td>
                                        <td className="p-2">
                                            {usr.department}
                                        </td>
                                        <td className="p-2">
                                            {usr.id !== auth.user.id && (
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        router.get(
                                                            accessRightsIndex()
                                                                .url,
                                                            {
                                                                q: filters.q,
                                                                edit: usr.id,
                                                            },
                                                        )
                                                    }
                                                >
                                                    Edit
                                                </Button>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                                {users.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={5}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada user.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <div className="flex items-center justify-between text-sm text-muted-foreground">
                        <span>
                            Page {users.current_page} of {users.last_page} (
                            {users.total} users)
                        </span>
                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={users.current_page <= 1}
                                onClick={() =>
                                    router.get(
                                        accessRightsIndex().url,
                                        {
                                            q: filters.q,
                                            page: users.current_page - 1,
                                        },
                                        { preserveState: true },
                                    )
                                }
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={users.current_page >= users.last_page}
                                onClick={() =>
                                    router.get(
                                        accessRightsIndex().url,
                                        {
                                            q: filters.q,
                                            page: users.current_page + 1,
                                        },
                                        { preserveState: true },
                                    )
                                }
                            >
                                Next
                            </Button>
                        </div>
                    </div>
                </section>

                <section className="space-y-4 rounded-lg border p-4">
                    <h2 className="text-lg font-semibold">
                        Reviewer Department Master
                    </h2>

                    <Form
                        {...AccessRightController.storeReviewerDepartment.form()}
                        options={{ preserveScroll: true }}
                        resetOnSuccess={['name', 'sort_order']}
                        className="grid gap-4 rounded-lg border p-4 sm:grid-cols-3"
                    >
                        {({ processing, errors }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor="name">
                                        Nama Department
                                    </Label>
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

                                <div className="flex items-end">
                                    <Button type="submit" disabled={processing}>
                                        Add Department
                                    </Button>
                                </div>
                            </>
                        )}
                    </Form>

                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b text-left">
                                    <th className="p-2">Name</th>
                                    <th className="p-2">Sort</th>
                                    <th className="p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {reviewerDepartments.map((department) => (
                                    <tr
                                        key={department.id}
                                        className="border-b"
                                    >
                                        <td className="p-2">
                                            {department.name}
                                        </td>
                                        <td className="p-2">
                                            {department.sort_order}
                                        </td>
                                        <td className="p-2">
                                            <DeleteReviewerDepartmentButton
                                                department={department}
                                            />
                                        </td>
                                    </tr>
                                ))}
                                {reviewerDepartments.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={3}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada reviewer department
                                            custom.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </section>

                <section className="space-y-4 rounded-lg border p-4">
                    <h2 className="text-lg font-semibold">
                        Draft Report Edit Permission
                    </h2>

                    <Form
                        {...AccessRightController.grantPermission.form()}
                        options={{ preserveScroll: true }}
                        className="grid gap-4 rounded-lg border p-4 sm:grid-cols-3"
                    >
                        {({ processing, errors }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor="trial_id">
                                        Draft Report
                                    </Label>
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

                                <div className="flex items-end">
                                    <Button type="submit" disabled={processing}>
                                        Grant Access
                                    </Button>
                                </div>
                            </>
                        )}
                    </Form>

                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b text-left">
                                    <th className="p-2">Draft Report</th>
                                    <th className="p-2">Owner</th>
                                    <th className="p-2">Granted To</th>
                                    <th className="p-2">Granted By</th>
                                    <th className="p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {draftPermissions.map((permission) => (
                                    <tr
                                        key={permission.id}
                                        className="border-b"
                                    >
                                        <td className="p-2">
                                            {permission.trial?.trial_code} —{' '}
                                            {permission.trial?.product_name}
                                        </td>
                                        <td className="p-2">
                                            {permission.trial?.created_by}
                                        </td>
                                        <td className="p-2">
                                            {permission.user?.name} (
                                            {permission.user?.email})
                                        </td>
                                        <td className="p-2">
                                            {permission.granted_by?.name ?? '-'}
                                        </td>
                                        <td className="p-2">
                                            <RevokePermissionButton
                                                permission={permission}
                                            />
                                        </td>
                                    </tr>
                                ))}
                                {draftPermissions.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={5}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada izin edit Draft report
                                            yang aktif.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </>
    );
}

AdminAccessRightsIndex.layout = {
    breadcrumbs: [{ title: 'Access Rights', href: accessRightsIndex() }],
};
