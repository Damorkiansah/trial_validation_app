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

type FormRenderProps = { processing: boolean; errors: Record<string, string> };

type ConfirmDialogProps = {
    trigger: ReactNode;
    title: string;
    description: ReactNode;
    confirmLabel?: string;
    confirmVariant?: ComponentProps<typeof Button>['variant'];
    formProps: Omit<ComponentProps<typeof Form>, 'children'>;
    /** Extra fields rendered inside the <Form>, between the description and the footer buttons. */
    children?: (bag: FormRenderProps) => ReactNode;
};

export function ConfirmDialog({
    trigger,
    title,
    description,
    confirmLabel = 'Confirm',
    confirmVariant = 'destructive',
    formProps,
    children,
}: ConfirmDialogProps) {
    return (
        <Dialog>
            <DialogTrigger asChild>{trigger}</DialogTrigger>
            <DialogContent>
                <DialogTitle>{title}</DialogTitle>
                <Form options={{ preserveScroll: true }} {...formProps}>
                    {({ processing, errors }) => (
                        <>
                            <DialogDescription>{description}</DialogDescription>
                            {children?.({ processing, errors })}
                            <DialogFooter className="mt-4 gap-2">
                                <DialogClose asChild>
                                    <Button type="button" variant="secondary">
                                        Cancel
                                    </Button>
                                </DialogClose>
                                <Button
                                    type="submit"
                                    variant={confirmVariant}
                                    disabled={processing}
                                >
                                    {confirmLabel}
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
