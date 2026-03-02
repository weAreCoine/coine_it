import { usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import { useEffect, useState } from 'react';

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
        <div className="primary__header">
            <div className="px-6">
                <a href="/" title="Home">
                    <span className="text-5xl font-semibold">Coiné</span>
                </a>
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
                                    <a
                                        href={menuItem.href}
                                        title={menuItem.title}
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
                                    </a>
                                </div>
                                {hasSubItems && (
                                    <div
                                        className={clsx({
                                            navigation__submenu: true,
                                            is__visible: index === hoveredIndex,
                                        })}
                                    >
                                        <ul>
                                            {menuItem.subItems.map((subItem, subIndex) => (
                                                <li key={subIndex} className={clsx({})}>
                                                    <a href={subItem.href} title={subItem.title}>
                                                        <span>{subItem.title}</span>
                                                    </a>
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
