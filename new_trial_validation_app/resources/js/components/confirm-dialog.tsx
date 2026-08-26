import { Form } from '@inertiajs/react';
import type { ComponentProps, ReactNode } from 'react';
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

type ConfirmDialogProps = {
    trigger: ReactNode;
    title: string;
    description: ReactNode;
    confirmLabel?: string;
    confirmVariant?: ComponentProps<typeof Button>['variant'];
    formProps: Omit<ComponentProps<typeof Form>, 'children'>;
};

export function ConfirmDialog({
    trigger,
    title,
    description,
    confirmLabel = 'Confirm',
    confirmVariant = 'destructive',
    formProps,
}: ConfirmDialogProps) {
    return (
        <Dialog>
            <DialogTrigger asChild>{trigger}</DialogTrigger>
            <DialogContent>
                <DialogTitle>{title}</DialogTitle>
                <DialogDescription>{description}</DialogDescription>
                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Form options={{ preserveScroll: true }} {...formProps}>
                        {({ processing }) => (
                            <Button
                                type="submit"
                                variant={confirmVariant}
                                disabled={processing}
                            >
                                {confirmLabel}
                            </Button>
                        )}
                    </Form>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
