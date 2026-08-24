import { Form, Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
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
import { index as usersIndex } from '@/routes/admin/users';
import type { Auth, User } from '@/types';

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
};

type PageProps = {
    auth: Auth;
    users: Paginated<User>;
    roleCategories: string[];
    filters: { q: string };
};

function DeleteUserButton({ user }: { user: User }) {
    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="destructive" size="sm">
                    Delete
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Delete this user account?</DialogTitle>
                <DialogDescription>
                    {user.name} ({user.email}) will be deactivated and won't be
                    able to log in anymore.
                </DialogDescription>
                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Form
                        {...UserController.destroy.form(user.id)}
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

export default function AdminUsersIndex({
    auth,
    users,
    roleCategories,
    filters,
}: PageProps) {
    const [search, setSearch] = useState(filters.q);

    function submitSearch(e: FormEvent) {
        e.preventDefault();
        router.get(usersIndex().url, { q: search }, { preserveState: true });
    }

    return (
        <>
            <Head title="Users" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Users"
                    description="Manage user account dan role akses aplikasi."
                />

                <Form
                    {...UserController.store.form()}
                    options={{ preserveScroll: true }}
                    resetOnSuccess={['name', 'email', 'password']}
                    className="grid gap-4 rounded-lg border p-4 sm:grid-cols-2 lg:grid-cols-5"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input id="name" name="name" required />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    required
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">New Password</Label>
                                <Input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="role">Role</Label>
                                <Select
                                    name="role"
                                    defaultValue={roleCategories[0]}
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
                                />
                                <InputError message={errors.department} />
                            </div>

                            <div className="sm:col-span-2 lg:col-span-5">
                                <Button type="submit" disabled={processing}>
                                    Save / Change Password
                                </Button>
                            </div>
                        </>
                    )}
                </Form>

                <div className="space-y-4 rounded-lg border p-4">
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
                                            {usr.id !== auth.user.id &&
                                                (usr.role !== 'Super Admin' ||
                                                    auth.user.role ===
                                                        'Super Admin') && (
                                                    <DeleteUserButton
                                                        user={usr}
                                                    />
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
                                        usersIndex().url,
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
                                        usersIndex().url,
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
                </div>
            </div>
        </>
    );
}

AdminUsersIndex.layout = {
    breadcrumbs: [{ title: 'Users', href: usersIndex() }],
};
