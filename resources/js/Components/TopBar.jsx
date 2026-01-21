"use client";

import React, { useState } from "react";
import { Bell, MessageSquare, RotateCcw } from "lucide-react";
import { Link } from "@inertiajs/react";

export default function TopBar({ title, onRecalculate }) {
    const [showNotifications, setShowNotifications] = useState(false);
    const [showChat, setShowChat] = useState(false);

    return (
        <div className="bg-light border-b border-border-gray sticky top-0 z-40">
            <div className="flex items-center justify-between px-6 py-4">
                {/* Left: Logo and Title */}
                <div className="flex items-center gap-4">
                    <div className="flex items-center gap-2">
                        <div className="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                            <span className="text-light font-bold text-sm">
                                TC
                            </span>
                        </div>
                    </div>
                    <h1 className="text-lg font-semibold text-primary">
                        {title}
                    </h1>
                </div>

                {/* Right: Actions */}
                <div className="flex items-center gap-4">
                    {/* Recalculate Button */}
                    {onRecalculate && (
                        <button
                            onClick={onRecalculate}
                            className="px-4 py-2 bg-primary text-light rounded-lg font-medium text-sm hover:bg-dark transition-colors duration-200 flex items-center gap-2"
                        >
                            <RotateCcw className="w-4 h-4" />
                            Recalculate
                        </button>
                    )}

                    {/* Notification Bell */}
                    <div className="relative">
                        <button
                            onClick={() => {
                                setShowNotifications(!showNotifications);
                                setShowChat(false);
                            }}
                            className="p-2 hover:bg-border-gray rounded-lg transition-colors duration-200 relative"
                            aria-label="Notifications"
                        >
                            <Bell className="w-5 h-5 text-primary" />
                            <span className="absolute top-1 right-1 w-2 h-2 bg-primary rounded-full"></span>
                        </button>

                        {/* Notification Dropdown */}
                        {showNotifications && (
                            <div className="absolute right-0 mt-2 w-72 bg-white rounded-lg border border-border-gray shadow-lg">
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

                    {/* Chat Icon */}
                    <div className="relative">
                        <button
                            onClick={() => {
                                setShowChat(!showChat);
                                setShowNotifications(false);
                            }}
                            className="p-2 hover:bg-border-gray rounded-lg transition-colors duration-200"
                            aria-label="Chat"
                        >
                            <MessageSquare className="w-5 h-5 text-primary" />
                        </button>

                        {/* Chat Dropdown */}
                        {showChat && (
                            <div className="absolute right-0 mt-2 w-72 bg-white rounded-lg border border-border-gray shadow-lg">
                                <div className="p-4 border-b border-border-gray">
                                    <p className="font-semibold text-primary text-sm">
                                        Messages
                                    </p>
                                </div>
                                <div className="p-4">
                                    <p className="text-gray text-sm">
                                        No messages
                                    </p>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
