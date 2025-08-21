import { useState } from 'react';
import { Breadcrumbs } from '@/components/breadcrumbs';
import { UserInfo } from '@/components/user-info';
import { type User } from '@/types';
import { usePage } from '@inertiajs/react';
import { type SharedData } from '@/types';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import { type BreadcrumbItem as BreadcrumbItemType } from '@/types';
import AppearanceDropdown from '@/components/appearance-dropdown';

interface AppSidebarHeaderProps {
  breadcrumbs?: BreadcrumbItemType[];
}

export function AppSidebarHeader({ breadcrumbs = [] }: AppSidebarHeaderProps) {
  const [lang, setLang] = useState('en');
  const { auth } = usePage<SharedData>().props;

  return (
    <header className="border-sidebar-border/50 flex h-16 shrink-0 items-center justify-between px-6 md:px-4 border-b transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12">
      {/* Left: Sidebar + Breadcrumb */}
      <div className="flex items-center gap-2">
        <SidebarTrigger className="-ml-1" />
        <Breadcrumbs breadcrumbs={breadcrumbs} />
      </div>

      {/* Right: Language + Theme */}
      <div className="flex items-center gap-4">
        {auth.user?.name && <p className="text-sm font-medium">{auth.user.name}</p>}
        {auth.user?.role?.name && <p className="text-sm font-medium">{auth.user.role.name}</p>}
        <Select value={lang} onValueChange={setLang}>
          <SelectTrigger className="w-[120px]">
            <SelectValue placeholder="Language" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="en">🇺🇸 English</SelectItem>
            <SelectItem value="id">🇮🇩 Bahasa</SelectItem>
          </SelectContent>
        </Select>

        <AppearanceDropdown />
      </div>
    </header>
  );
}
