import { Form, Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import { ConfirmDialog } from '@/components/confirm-dialog';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { PaginationFooter } from '@/components/pagination-footer';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
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
import { index as usersIndex } from '@/routes/admin/users';
import type { Auth, Paginated, User } from '@/types';

type PageProps = {
    auth: Auth;
    users: Paginated<User>;
    roleCategories: string[];
    filters: { q: string };
};

function UserFormDialog({
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
                    <DialogTitle>
                        {editingUser ? 'Edit User' : 'Add User'}
                    </DialogTitle>
                </DialogHeader>
                <Form
                    {...UserController.store.form()}
                    options={{ preserveScroll: true }}
                    onSuccess={() => onOpenChange(false)}
                    resetOnSuccess={['name', 'email', 'password']}
                    className="grid gap-4 sm:grid-cols-2"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2 sm:col-span-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    required
                                    defaultValue={editingUser?.name ?? ''}
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2 sm:col-span-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    required
                                    defaultValue={editingUser?.email ?? ''}
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2 sm:col-span-2">
                                <Label htmlFor="password">
                                    {editingUser ? 'New Password' : 'Password'}
                                </Label>
                                <Input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    placeholder={
                                        editingUser
                                            ? 'Wajib diisi ulang untuk menyimpan perubahan'
                                            : undefined
                                    }
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="role">Role</Label>
                                <Select
                                    name="role"
                                    defaultValue={
                                        editingUser?.role ?? roleCategories[0]
                                    }
                                >
                                    <SelectTrigger id="role">
                                        <SelectValue placeholder="Role" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {roleCategories.map((role) => (
                                            <SelectItem key={role} value={role}>
                                                {role}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.role} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="department">Department</Label>
                                <Input
                                    id="department"
                                    name="department"
                                    placeholder="Auto untuk role reviewer"
                                    defaultValue={editingUser?.department ?? ''}
                                />
                                <InputError message={errors.department} />
                            </div>

                            <DialogFooter className="sm:col-span-2">
                                <Button type="submit" disabled={processing}>
                                    {editingUser ? 'Save Changes' : 'Add User'}
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}

export default function AdminUsersIndex({
    auth,
    users,
    roleCategories,
    filters,
}: PageProps) {
    const [search, setSearch] = useState(filters.q);
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingUser, setEditingUser] = useState<User | null>(null);

    function submitSearch(e: FormEvent) {
        e.preventDefault();
        router.get(usersIndex().url, { q: search }, { preserveState: true });
    }

    function openCreate() {
        setEditingUser(null);
        setDialogOpen(true);
    }

    function openEdit(user: User) {
        setEditingUser(user);
        setDialogOpen(true);
    }

    return (
        <>
            <Head title="Users" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Users"
                    description="Manage user account dan role akses aplikasi."
                />

                <UserFormDialog
                    open={dialogOpen}
                    onOpenChange={setDialogOpen}
                    editingUser={editingUser}
                    roleCategories={roleCategories}
                />

                <Card>
                    <CardHeader className="flex-row flex-wrap items-end justify-between gap-4">
                        <form
                            onSubmit={submitSearch}
                            className="flex flex-1 items-end gap-2"
                        >
                            <div className="grid flex-1 gap-2 sm:max-w-sm">
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
                        <Button type="button" onClick={openCreate}>
                            Add User
                        </Button>
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
                                            <div className="flex gap-2">
                                                {usr.id !== auth.user.id && (
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() =>
                                                            openEdit(usr)
                                                        }
                                                    >
                                                        Edit
                                                    </Button>
                                                )}
                                                {usr.id !== auth.user.id &&
                                                    (usr.role !==
                                                        'Super Admin' ||
                                                        auth.user.role ===
                                                            'Super Admin') && (
                                                        <ConfirmDialog
                                                            trigger={
                                                                <Button
                                                                    variant="destructive"
                                                                    size="sm"
                                                                >
                                                                    Delete
                                                                </Button>
                                                            }
                                                            title="Delete this user account?"
                                                            description={`${usr.name} (${usr.email}) will be deactivated and won't be able to log in anymore.`}
                                                            confirmLabel="Delete"
                                                            formProps={UserController.destroy.form(
                                                                usr.id,
                                                            )}
                                                        />
                                                    )}
                                            </div>
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
                            url={usersIndex().url}
                            query={{ q: filters.q }}
                            currentPage={users.current_page}
                            lastPage={users.last_page}
                            total={users.total}
                            itemLabel="users"
                        />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AdminUsersIndex.layout = {
    breadcrumbs: [{ title: 'Users', href: usersIndex() }],
};
