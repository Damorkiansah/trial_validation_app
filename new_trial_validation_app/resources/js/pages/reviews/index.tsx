import { Form, Head, Link } from '@inertiajs/react';
import ReviewController from '@/actions/App/Http/Controllers/ReviewController';
import Heading from '@/components/heading';
import { PaginationFooter } from '@/components/pagination-footer';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import { index as reviewsIndex } from '@/routes/reviews';
import { edit as reviewEdit } from '@/routes/trials/review';
import type { Paginated } from '@/types';

type ReviewItem = {
    id: number;
    trial_id: number;
    trial_code: string;
    product_name: string;
    review_round: number;
    status: string;
    reviewer_name: string | null;
    comment: string | null;
    active: boolean;
};

type PageProps = {
    items: Paginated<ReviewItem>;
};

export default function ReviewsIndex({ items }: PageProps) {
    return (
        <>
            <Head title="Review Queue" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Review Department"
                    description="Trial yang perlu direview oleh department Anda."
                />

                <Card>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Trial</TableHead>
                                    <TableHead>Product</TableHead>
                                    <TableHead>Round</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Reviewer</TableHead>
                                    <TableHead>Comment</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {items.data.map((item) => (
                                    <TableRow key={item.id}>
                                        <TableCell>
                                            <Link
                                                href={
                                                    reviewEdit({
                                                        trial: item.trial_id,
                                                    }).url
                                                }
                                                className="font-medium underline"
                                            >
                                                {item.trial_code}
                                            </Link>
                                        </TableCell>
                                        <TableCell>
                                            {item.product_name}
                                        </TableCell>
                                        <TableCell>
                                            {item.review_round}
                                        </TableCell>
                                        <TableCell>{item.status}</TableCell>
                                        <TableCell>
                                            {item.reviewer_name ?? '-'}
                                        </TableCell>
                                        <TableCell className="min-w-64">
                                            {item.active ? (
                                                <Form
                                                    {...ReviewController.update.form(
                                                        item.id,
                                                    )}
                                                    className="space-y-2"
                                                >
                                                    {({
                                                        processing,
                                                        errors,
                                                    }) => (
                                                        <>
                                                            {errors.comment && (
                                                                <Alert variant="destructive">
                                                                    <AlertDescription>
                                                                        {
                                                                            errors.comment
                                                                        }
                                                                    </AlertDescription>
                                                                </Alert>
                                                            )}
                                                            <Textarea
                                                                name="comment"
                                                                required
                                                                placeholder="Comment review..."
                                                            />
                                                            <Button
                                                                type="submit"
                                                                size="sm"
                                                                disabled={
                                                                    processing
                                                                }
                                                            >
                                                                Submit Review
                                                            </Button>
                                                        </>
                                                    )}
                                                </Form>
                                            ) : (
                                                (item.comment ?? '-')
                                            )}
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {items.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={6}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Tidak ada review pending.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={reviewsIndex().url}
                            query={{}}
                            currentPage={items.current_page}
                            lastPage={items.last_page}
                            total={items.total}
                            itemLabel="reviews"
                        />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

ReviewsIndex.layout = {
    breadcrumbs: [{ title: 'Review Queue', href: reviewsIndex() }],
};
