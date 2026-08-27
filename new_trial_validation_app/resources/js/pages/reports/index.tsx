import { Head, Link } from '@inertiajs/react';
import {
    ClipboardList,
    FileCheck2,
    FileWarning,
    ListChecks,
    Printer,
} from 'lucide-react';
import Heading from '@/components/heading';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard } from '@/routes';
import {
    approved,
    auditPrintLog,
    departmentReview,
    index as reportsIndex,
    rejected,
    trialSummary,
} from '@/routes/reports';

const REPORTS = [
    {
        title: 'Approved Report',
        description: 'Daftar trial yang sudah approved.',
        href: approved(),
        icon: FileCheck2,
    },
    {
        title: 'Rejected Report',
        description: 'Daftar trial rejected atau need revision.',
        href: rejected(),
        icon: FileWarning,
    },
    {
        title: 'Trial Summary Report',
        description: 'Ringkasan semua trial dengan filter.',
        href: trialSummary(),
        icon: ListChecks,
    },
    {
        title: 'Department Review Report',
        description: 'Progress review per department.',
        href: departmentReview(),
        icon: ClipboardList,
    },
    {
        title: 'Audit Print Log',
        description: 'Log print report jika tersedia.',
        href: auditPrintLog(),
        icon: Printer,
    },
];

export default function ReportsIndex() {
    return (
        <>
            <Head title="Report" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Report"
                    description="Pilih jenis laporan trial validation."
                />

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {REPORTS.map((report) => (
                        <Link key={report.title} href={report.href.url}>
                            <Card className="h-full transition-colors hover:border-brand">
                                <CardHeader className="flex flex-row items-center gap-3 space-y-0">
                                    <report.icon className="size-6 text-brand" />
                                    <CardTitle>{report.title}</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <CardDescription>
                                        {report.description}
                                    </CardDescription>
                                </CardContent>
                            </Card>
                        </Link>
                    ))}
                </div>
            </div>
        </>
    );
}

ReportsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Report', href: reportsIndex() },
    ],
};
