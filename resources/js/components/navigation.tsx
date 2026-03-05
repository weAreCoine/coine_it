import { usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import { useEffect, useState } from 'react';
import AppLink from '@/components/appLink';
import DevLabel from '@/components/devLabel';
import NavigationItem = App.Entities.NavigationItem;

export default function Navigation() {
    const { navigationItems } = usePage<{ navigationItems: App.Entities.NavigationItem[] }>().props;
    const [hoveredIndex, setHoveredIndex] = useState<number | null>(null);
    const [open, setOpen] = useState<boolean>(false);

    useEffect(() => {
        const handleResize = () => setHoveredIndex(null);
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    return (
        <div className="primary__header relative">
            <DevLabel name="Navigation" />
            <div className="px-6">
                <AppLink href="/" title="Home">
                    <span className="font-display text-3xl font-black text-black">Coiné</span>
                </AppLink>
            </div>
            <nav className={clsx({ open: open, header__navigation: true })}>
                <div
                    onClick={() => {
                        setOpen(!open);
                    }}
                    className="hamburger"
                >
                    <span></span>
                    <span></span>
                </div>

                <ul className="primary__navigation">
                    {navigationItems.map((menuItem: App.Entities.NavigationItem, index: number) => {
                        const hasSubItems = menuItem.subItems.length > 0;
                        return (
                            <li
                                key={index}
                                onMouseEnter={() => window.innerWidth >= 1024 && setHoveredIndex(index)}
                                onMouseLeave={() => window.innerWidth >= 1024 && setHoveredIndex(null)}
                                onClick={() => window.innerWidth < 1024 && setHoveredIndex(index !== hoveredIndex ? index : null)}
                                className={clsx({
                                    has__subitems: hasSubItems,
                                    call__to__action: menuItem.isCallToAction,
                                    focus__item: hoveredIndex === index,
                                    mouse__in__nav: hoveredIndex !== null,
                                })}
                            >
                                <div>
                                    <AppLink
                                        href={menuItem.href}
                                        title={menuItem.title}
                                        prevent={menuItem.isPlaceholder}
                                        external={menuItem.isExternal || menuItem.targetBlank}
                                        className={clsx({
                                            current: menuItem.isCurrent,
                                            has__subitems: hasSubItems,
                                        })}
                                        {...(menuItem.targetBlank && { target: '_blank', rel: 'noopener noreferrer' })}
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                        </svg>
                                        <span>{menuItem.title}</span>
                                    </AppLink>
                                </div>
                                {hasSubItems && (
                                    <div
                                        className={clsx({
                                            navigation__submenu: true,
                                            is__visible: index === hoveredIndex,
                                        })}
                                    >
                                        <ul>
                                            {menuItem.subItems.map((subItem: NavigationItem, subIndex) => (
                                                <li key={subIndex} className={clsx({})}>
                                                    <AppLink href={subItem.href} title={subItem.title} external={subItem.isExternal}>
                                                        <span>{subItem.title}</span>
                                                    </AppLink>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                )}
                            </li>
                        );
                    })}
                </ul>
            </nav>
        </div>
    );
}
