import axios from "axios";
import { router } from "@inertiajs/react";

const web = axios.create({
    baseURL: "/",
    withCredentials: true, // session + CSRF
    headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
    },
    timeout: 15000,
});

// Request protection
web.interceptors.request.use((config) => {
    // Laravel automatically injects CSRF via cookie
    return config;
});

// Response protection
web.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error.response?.status;

        if (status === 401) {
            router.visit("/login");
        }

        if (status === 419) {
            window.location.reload();
        }

        if (status === 403) {
            console.warn("Forbidden request blocked.");
        }

        return Promise.reject(error);
    },
);

export default web;
