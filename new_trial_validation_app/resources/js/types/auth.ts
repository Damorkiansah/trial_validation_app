export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    role: string;
    department: string | null;
    is_active: boolean;
    created_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
};
