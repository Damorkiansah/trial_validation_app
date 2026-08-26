import { CheckIcon, ChevronsUpDownIcon, XIcon } from 'lucide-react';
import * as React from 'react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';

export interface MultiSelectOption {
    value: string;
    label: string;
}

interface MultiSelectProps {
    options: MultiSelectOption[];
    value: string[];
    onChange: (value: string[]) => void;
    placeholder?: string;
    searchPlaceholder?: string;
    emptyMessage?: string;
    disabled?: boolean;
    className?: string;
    'aria-invalid'?: boolean;
}

export function MultiSelect({
    options,
    value,
    onChange,
    placeholder = 'Pilih...',
    searchPlaceholder = 'Cari...',
    emptyMessage = 'Tidak ada hasil.',
    disabled = false,
    className,
    'aria-invalid': ariaInvalid,
}: MultiSelectProps) {
    const [open, setOpen] = React.useState(false);
    const selectedOptions = options.filter((option) =>
        value.includes(option.value),
    );

    function toggle(optionValue: string) {
        onChange(
            value.includes(optionValue)
                ? value.filter((v) => v !== optionValue)
                : [...value, optionValue],
        );
    }

    function remove(optionValue: string) {
        onChange(value.filter((v) => v !== optionValue));
    }

    return (
        <div className={cn('space-y-2', className)}>
            <Popover open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <Button
                        type="button"
                        variant="outline"
                        role="combobox"
                        aria-expanded={open}
                        aria-invalid={ariaInvalid}
                        disabled={disabled}
                        className={cn(
                            'w-full justify-between font-normal',
                            selectedOptions.length === 0 &&
                                'text-muted-foreground',
                        )}
                    >
                        <span className="truncate">
                            {selectedOptions.length > 0
                                ? `${selectedOptions.length} dipilih`
                                : placeholder}
                        </span>
                        <ChevronsUpDownIcon className="ml-2 size-4 shrink-0 opacity-50" />
                    </Button>
                </PopoverTrigger>
                <PopoverContent
                    className="w-(--radix-popover-trigger-width) p-0"
                    align="start"
                >
                    <Command>
                        <CommandInput placeholder={searchPlaceholder} />
                        <CommandList>
                            <CommandEmpty>{emptyMessage}</CommandEmpty>
                            <CommandGroup>
                                {options.map((option) => {
                                    const isSelected = value.includes(
                                        option.value,
                                    );

                                    return (
                                        <CommandItem
                                            key={option.value}
                                            value={option.label}
                                            onSelect={() =>
                                                toggle(option.value)
                                            }
                                        >
                                            <CheckIcon
                                                className={cn(
                                                    'size-4',
                                                    isSelected
                                                        ? 'opacity-100'
                                                        : 'opacity-0',
                                                )}
                                            />
                                            {option.label}
                                        </CommandItem>
                                    );
                                })}
                            </CommandGroup>
                        </CommandList>
                    </Command>
                </PopoverContent>
            </Popover>
            {selectedOptions.length > 0 && (
                <div className="flex flex-wrap gap-1.5">
                    {selectedOptions.map((option) => (
                        <Badge
                            key={option.value}
                            variant="secondary"
                            className="gap-1 pr-1"
                        >
                            {option.label}
                            <button
                                type="button"
                                onClick={() => remove(option.value)}
                                className="rounded-full p-0.5 hover:bg-black/10 dark:hover:bg-white/10"
                                aria-label={`Hapus ${option.label}`}
                            >
                                <XIcon className="size-3" />
                            </button>
                        </Badge>
                    ))}
                </div>
            )}
        </div>
    );
}
