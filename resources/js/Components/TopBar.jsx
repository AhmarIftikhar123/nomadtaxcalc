"use client";

import React, { useState, useRef, useEffect } from "react";
import { Bell, RotateCcw, Menu, UserIcon, LogIn } from "lucide-react";
import { Link } from "@inertiajs/react";
import Dropdown from "@/Components/Dropdown";
import Tooltip from "@/Components/Ui/Tooltip";
import ApplicationLogo from "@/Components/ApplicationLogo";

export default function TopBar({
    title,
    onRecalculate,
    onToggleSidebar,
    user, // Will be null/undefined for guest users
}) {
    const [showNotifications, setShowNotifications] = useState(false);
    const notificationRef = useRef(null);

    // Determine if user is authenticated
    const isAuthenticated = !!user;

    // Close dropdown when clicking outside
    useEffect(() => {
        function handleClickOutside(event) {
            if (
                notificationRef.current &&
                !notificationRef.current.contains(event.target)
            ) {
                setShowNotifications(false);
            }
        }

        if (showNotifications) {
            document.addEventListener("mousedown", handleClickOutside);
            return () => {
                document.removeEventListener("mousedown", handleClickOutside);
            };
        }
    }, [showNotifications]);

    return (
        <div className="bg-light border-b border-border-gray sticky top-0 z-40 h-16 overflow-visible">
            <div className="flex items-center justify-between px-6 h-full">
                {/* Left: Logo/Brand (for guests) OR Sidebar Toggle + Title (for authenticated) */}
                <div className="flex items-center gap-4">
                    {isAuthenticated ? (
                        <>
                            {/* Sidebar Toggle - Only for authenticated users */}
                            <button
                                onClick={onToggleSidebar}
                                className="p-2 hover:bg-border-gray rounded-lg text-primary transition-colors"
                                aria-label="Toggle sidebar"
                            >
                                <Menu className="w-6 h-6" />
                            </button>
                            <h1 className="text-lg font-semibold text-primary hidden md:block">
                                {title}
                            </h1>
                        </>
                    ) : (
                        /* Logo - Only for guest users */
                        <Link
                            href={route("home")}
                            className="flex items-center hover:opacity-80 transition-opacity"
                        >
                            <ApplicationLogo className="h-8 w-auto" />
                        </Link>
                    )}
                </div>

                {/* Right: Actions */}
                <div className="flex items-center gap-1">
                    {/* Recalculate Button - Only for authenticated users */}
                    {isAuthenticated && onRecalculate && (
                        <button
                            onClick={onRecalculate}
                            className="hidden md:flex px-4 py-2 bg-primary text-light rounded-lg font-medium text-sm hover:bg-dark transition-colors duration-200 items-center gap-2"
                        >
                            <RotateCcw className="w-4 h-4" />
                            Recalculate
                        </button>
                    )}

                    {isAuthenticated ? (
                        /* Authenticated User Section */
                        <>
                            {/* Notification Bell - Only for authenticated users */}
                            <div className="relative" ref={notificationRef}>
                                <button
                                    onClick={() =>
                                        setShowNotifications(!showNotifications)
                                    }
                                    className="p-2 hover:bg-border-gray rounded-lg transition-colors duration-200 relative"
                                    aria-label="Notifications"
                                >
                                    <Bell className="w-5 h-5 text-primary" />
                                    {/* Notification Badge */}
                                    <span className="absolute top-1 right-1 w-2 h-2 bg-primary rounded-full"></span>
                                </button>

                                {/* Notification Dropdown */}
                                {showNotifications && (
                                    <div className="absolute right-0 mt-2 w-72 bg-white rounded-lg border border-border-gray shadow-lg z-50">
                                        <div className="p-4 border-b border-border-gray">
                                            <p className="font-semibold text-primary text-sm">
                                                Notifications
                                            </p>
                                        </div>
                                        <div className="p-4">
                                            <p className="text-gray text-sm">
                                                No new notifications
                                            </p>
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* User Profile Dropdown */}
                            <div className="relative ms-3">
                                <Dropdown>
                                    <Dropdown.Trigger>
                                        <span className="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                className="inline-flex items-center gap-2 rounded-md border border-transparent bg-white px-1 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                <div className="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-light font-bold">
                                                    {user?.name
                                                        ?.charAt(0)
                                                        .toUpperCase() || (
                                                        <UserIcon className="w-4 h-4" />
                                                    )}
                                                </div>
                                                <span className="hidden md:block text-primary font-semibold">
                                                    {user?.name}
                                                </span>
                                            </button>
                                        </span>
                                    </Dropdown.Trigger>

                                    <Dropdown.Content>
                                        <Dropdown.Link
                                            href={route("profile.edit")}
                                        >
                                            Profile
                                        </Dropdown.Link>
                                        <Dropdown.Link
                                            href={route("logout")}
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </Dropdown.Link>
                                    </Dropdown.Content>
                                </Dropdown>
                            </div>
                        </>
                    ) : (
                        /* Guest User Section */
                        <Tooltip text="Login to monitor US tax thresholds & many more benefits." position="left">
                            <Link
                                href={route("login")}
                                className="flex items-center gap-2 px-4 py-2 bg-primary text-light rounded-lg font-medium text-sm hover:bg-dark transition-colors duration-200"
                            >
                                <LogIn className="w-4 h-4" />
                                Login
                            </Link>
                        </Tooltip>
                    )}
                </div>
            </div>
        </div>
    );
}